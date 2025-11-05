<script setup lang="ts">
import type { CompanyData } from '~/types/company_data';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Partnerské firmy | ISOP",
    ogTitle: "Partnerské firmy",
    description: "Partnerské firmy ISOP",
    ogDescription: "Partnerské firmy",
});

const headers = [
    { title: 'Názov', key: 'name', align: 'left' },
    { title: 'IČO', key: 'ico', align: 'left' },
    { title: 'Kontaktná osoba', key: 'contact_name', align: 'left' },
    { title: 'Telefón', key: 'phone', align: 'middle' },
    { title: 'E-mail', key: 'email', align: 'middle' },
    { title: 'Prijímajú študentov', key: 'hiring', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const client = useSanctumClient();

const { data, error, refresh } = await useSanctumFetch<CompanyData[]>('/api/companies/simple');

// State pre delete dialog
const deleteDialog = ref(false);
const companyToDelete = ref<CompanyData | null>(null);
const deleteLoading = ref(false);
const deleteError = ref<string | null>(null);

// Funkcia na otvorenie delete dialogu
const openDeleteDialog = (company: CompanyData) => {
    companyToDelete.value = company;
    deleteDialog.value = true;
    deleteError.value = null;
};

// Funkcia na zatvorenie dialogu
const closeDeleteDialog = () => {
    deleteDialog.value = false;
    companyToDelete.value = null;
    deleteError.value = null;
};

// Funkcia na vymazanie firmy
const deleteCompany = async () => {
    if (!companyToDelete.value) return;

    deleteLoading.value = true;
    deleteError.value = null;

    try {
        await client(`/api/companies/${companyToDelete.value.id}`, {
            method: 'DELETE'
        });

        refresh();
        closeDeleteDialog();

    } catch (err) {
        if (err instanceof FetchError) {
            deleteError.value = err.data?.message;
        }
    } finally {
        deleteLoading.value = false;
    }
};

</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Partnerské firmy</h1>
            <hr />

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Chybová hláška -->
            <ErrorAlert v-if="error" :error="error?.message" />

            <div v-else>
                <p>Aktuálne spolupracujeme s {{ data?.length }} firmami.</p>

                <br />

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
                            <td>{{ item.ico }}</td>
                            <td>{{ item.contact.name }}</td>
                            <td>{{ item.contact.phone }}</td>
                            <td>{{ item.contact.email }}</td>
                            <td>{{ item.hiring ? "Áno" : "Nie" }}</td>
                            <td class="text-left">
                                <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-pencil" base-color="orange"
                                    :to="'/dashboard/admin/companies/edit/' + item.id">Editovať</v-btn>
                                <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-delete" base-color="red"
                                    @click="openDeleteDialog(item)">Vymazať</v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </div>
        </v-card>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Potvrdiť vymazanie
                </v-card-title>
                <v-card-text>
                    <p>
                        Naozaj chcete vymazať firmu <strong>{{ companyToDelete?.name }}</strong>?
                    </p>
                    <p class="text-error mt-2">
                        Táto akcia vymaže aj kontaktnú osobu (EMPLOYER), všetky praxe a statusy spojené s touto firmou a
                        <strong>nie je možné ju vrátiť späť</strong>.
                    </p>

                    <!-- Error message -->
                    <ErrorAlert v-if="deleteError" :error="deleteError" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" variant="text" @click="closeDeleteDialog" :disabled="deleteLoading">
                        Zrušiť
                    </v-btn>
                    <v-btn color="red" variant="text" @click="deleteCompany" :loading="deleteLoading">
                        Vymazať
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
</style>