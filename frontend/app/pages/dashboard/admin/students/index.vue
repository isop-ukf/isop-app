<script setup lang="ts">
import type { User } from '~/types/user';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Študenti | ISOP",
    ogTitle: "Študenti",
    description: "Študenti ISOP",
    ogDescription: "Študenti",
});

const headers = [
    { title: 'Meno', key: 'name', align: 'left' },
    { title: 'E-mail', key: 'email', align: 'left' },
    { title: 'Telefón', key: 'phone', align: 'left' },
    { title: 'Študijný program', key: 'study_field', align: 'left' },
    { title: 'Osobný e-mail', key: 'personal_email', align: 'middle' },
    { title: 'Adresa', key: 'address', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const client = useSanctumClient();

// Načítame všetkých študentov
const { data: students, error, refresh } = await useSanctumFetch<User[]>('/api/students');

// State pre delete dialog
const deleteDialog = ref(false);
const studentToDelete = ref<User | null>(null);
const deleteLoading = ref(false);
const deleteError = ref<string | null>(null);

// Funkcia na otvorenie delete dialogu
const openDeleteDialog = (student: User) => {
    studentToDelete.value = student;
    deleteDialog.value = true;
    deleteError.value = null;
};

// Funkcia na zatvorenie dialogu
const closeDeleteDialog = () => {
    deleteDialog.value = false;
    studentToDelete.value = null;
    deleteError.value = null;
};

// Funkcia na vymazanie študenta
const deleteStudent = async () => {
    if (!studentToDelete.value) return;

    deleteLoading.value = true;
    deleteError.value = null;

    try {
        await client(`/api/students/${studentToDelete.value.id}`, {
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
            <h1>Študenti</h1>
            <hr />

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Chybová hláška -->
            <v-alert v-if="error" density="compact" :text="error?.message" title="Chyba" type="error"
                id="login-error-alert" class="mx-auto alert"></v-alert>

            <div v-else>
                <p>Aktuálne evidujeme {{ students?.length || 0 }} študentov.</p>

                <br />

                <v-table v-if="students && students.length > 0">
                    <thead>
                        <tr>
                            <th v-for="header in headers" :class="'text-' + header.align">
                                <strong>{{ header.title }}</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in students" :key="item.id">
                            <td>{{ item.name }}</td>
                            <td>{{ item.email }}</td>
                            <td>{{ item.phone || '-' }}</td>
                            <td>{{ item.student_data?.study_field || '-' }}</td>
                            <td>{{ item.student_data?.personal_email || '-' }}</td>
                            <td>{{ item.student_data?.address || '-' }}</td>
                            <td class="text-left">
                                <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-pencil" base-color="orange"
                                    :to="'/dashboard/admin/students/edit/' + item.id">Editovať</v-btn>
                                <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-delete" base-color="red"
                                    @click="openDeleteDialog(item)">Vymazať</v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <v-alert v-else density="compact" text="Zatiaľ nie sú zaregistrovaní žiadni študenti." type="info"
                    class="mt-4"></v-alert>
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
                        Naozaj chcete vymazať študenta <strong>{{ studentToDelete?.name }}</strong>?
                    </p>
                    <p class="text-error mt-2">
                        Táto akcia vymaže aj všetky súvisiace dáta (praxe, statusy, atď.) a <strong>nie je možné ju
                            vrátiť späť</strong>.
                    </p>

                    <!-- Error message -->
                    <v-alert v-if="deleteError" type="error" density="compact" class="mt-3">
                        {{ deleteError }}
                    </v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" variant="text" @click="closeDeleteDialog" :disabled="deleteLoading">
                        Zrušiť
                    </v-btn>
                    <v-btn color="red" variant="text" @click="deleteStudent" :loading="deleteLoading">
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

.alert {
    margin-top: 10px;
}
</style>
