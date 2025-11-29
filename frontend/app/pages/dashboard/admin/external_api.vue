<script setup lang="ts">
import { FetchError } from 'ofetch';
import type { ApiKey, NewApiKey } from '~/types/api_keys';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "API Manažment | ISOP",
    ogTitle: "API Manažment",
    description: "API Manažment ISOP",
    ogDescription: "API Manažment",
});

const headers = [
    { title: 'Názov', key: 'name', align: 'left' },
    { title: 'Vytvorené', key: 'created_at', align: 'left' },
    { title: 'Naposledy použité', key: 'last_used_at', align: 'left' },
    { title: 'Vlastník', key: 'owner', align: 'left' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const creationDialog = ref(false);
const keyDisplayDialog = ref(false);
const deletionConfirmDialog = ref<{ open: boolean, key: ApiKey | null }>({ open: false, key: null });
const newKey = ref<string>("");
const newKeyName = ref("");
const waiting = ref(false);

const { data, error, pending, refresh } = useLazySanctumFetch<ApiKey[]>('/api/external/keys');
const client = useSanctumClient();

function closeKeyDisplayDialog() {
    keyDisplayDialog.value = false;
    newKey.value = "";
}

async function copyNewKeyToClipboard() {
    await navigator.clipboard.writeText(newKey.value);
}

function openDeletionConfirmDialog(key: ApiKey) {
    deletionConfirmDialog.value = { open: true, key: key };
}

async function confirmDeletion(confirm: boolean) {
    const key = deletionConfirmDialog.value.key!;

    if (!confirm) {
        deletionConfirmDialog.value = { open: false, key: null };
        return;
    };

    await deleteKey(key);
    deletionConfirmDialog.value = { open: false, key: null };
}

async function requestNewKey() {
    waiting.value = true;

    try {
        const result = await client<NewApiKey>('/api/external/keys', {
            method: 'PUT',
            body: {
                name: newKeyName.value
            }
        });

        newKey.value = result.key;
        await refresh();
        creationDialog.value = false;

        keyDisplayDialog.value = true;
    } catch (e) {
        if (e instanceof FetchError) {
            alert(`Chyba: ${e.data?.message}`);
        }
    } finally {
        waiting.value = false;
    }
}

async function deleteKey(key: ApiKey) {
    waiting.value = true;

    try {
        await client<NewApiKey>(`/api/external/keys/${key.id}`, {
            method: 'DELETE',
        });

        await refresh();

        deletionConfirmDialog.value = { open: false, key: null };
    } catch (e) {
        if (e instanceof FetchError) {
            alert(`Chyba: ${e.data?.message}`);
        }
    } finally {
        waiting.value = false;
    }
}
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>API Manažment</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-plus" color="blue" class="mr-2" @click="() => creationDialog = true"
                :disabled="waiting">
                Pridať
            </v-btn>

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="pending" />

            <!-- Chybová hláška -->
            <ErrorAlert v-else-if="error" :error="error?.message" />

            <v-table>
                <thead>
                    <tr>
                        <th v-for="header in headers" :class="'text-' + header.align">
                            <strong>{{ header.title }}</strong>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data">
                        <td>{{ item.name }}</td>
                        <td>{{ item.created_at }}</td>
                        <td>{{ item.last_used_at ?? "Nikdy" }}</td>
                        <td>{{ item.owner }}</td>
                        <td class="text-left">
                            <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-delete" base-color="red"
                                @click="() => openDeletionConfirmDialog(item)">Vymazať</v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>

            <!-- spacer -->
            <div style="height: 40px;"></div>
        </v-card>

        <!-- New API key dialog -->
        <v-dialog v-model="creationDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Nový API kľúč
                </v-card-title>

                <v-card-text>
                    <v-text-field label="Názov kľúča" required v-model="newKeyName" id="newKeyName"></v-text-field>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green" variant="text" @click="requestNewKey" :loading="waiting">
                        Vytvoriť
                    </v-btn>
                    <v-btn color="black" variant="text" @click="creationDialog = false">
                        Zavrieť
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- New API key display dialog -->
        <v-dialog v-model="keyDisplayDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Nový API kľúč
                </v-card-title>

                <v-card-text>
                    <p>Nový klúč bol úspešne vytvorený!</p>

                    <p id="key-copy-warn">Nezabudnite skopírovať kľúč! Po zavretí tohto okna ho nebudete vedieť
                        zobraziť znovu!</p>

                    <v-text-field label="API kľúč" required readonly v-model="newKey"></v-text-field>
                    <v-btn prepend-icon="mdi-content-copy" color="blue" block
                        @click="copyNewKeyToClipboard">Copy</v-btn>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="black" variant="text" @click="closeKeyDisplayDialog">
                        Zavrieť
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Key deletion confirmation dialog -->
        <v-dialog v-model="deletionConfirmDialog.open" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Vymazať API kľúč
                </v-card-title>

                <v-card-text>
                    <p>Ste si istý že chcete deaktivovať a vymazať vybraný API kľúč?</p>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn color="red" variant="text" @click="async () => { confirmDeletion(true) }" :loading="waiting">
                        Áno
                    </v-btn>

                    <v-btn color="black" variant="text" @click="async () => { confirmDeletion(false) }">
                        Zrušiť
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}

.op-btn {
    margin: 10px;
}

#key-copy-warn {
    color: red;
    font-weight: bold;
}
</style>