<script setup lang="ts">
import type { Paginated } from '~/types/pagination';
import { FetchError } from 'ofetch';
import type { User } from '~/types/user';

const props = defineProps<{
    adminOps?: boolean;
}>();

const page = ref(1);
const itemsPerPage = ref(15);
const totalItems = ref(0);
const deleteConfirmDialog = ref(false);
const studentToDelete = ref<User | null>(null);

const allHeaders = [
    { title: "Meno", key: "name", sortable: false },
    { title: "E-mail", key: "email", sortable: false },
    { title: "Osobný e-mail", key: "student_data.personal_email", sortable: false },
    { title: "Telefón", key: "phone", sortable: false },
    { title: "Študijný odbor", key: "student_data.study_field", sortable: false },
    { title: "Adresa", key: "student_data.address", sortable: false },
    { title: "Operácie", key: "operations", sortable: false }
];

const headers = props.adminOps !== true
    ? allHeaders.filter(header => header.key !== "operations")
    : allHeaders;

const client = useSanctumClient();
const { data, error, pending, refresh } = await useLazySanctumFetch<Paginated<User>>('/api/students', () => ({
    query: {
        page: page.value,
        per_page: itemsPerPage.value,
    }
}), {
    watch: [page, itemsPerPage]
});

async function delteStudent(student: User) {
    pending.value = true;

    try {
        await client(`/api/students/${student.id}`, {
            method: 'DELETE',
        });
        await refresh();
    } catch (e) {
        if (e instanceof FetchError) {
            alert(`Chyba pri mazaní študenta: ${e.statusMessage ?? e.message}`);
        }
    } finally {
        pending.value = false;
    }
}

function openDeleteDialog(student: User) {
    studentToDelete.value = student;
    deleteConfirmDialog.value = true;
}

async function confirmDeletion(confirm: boolean) {
    if (confirm && studentToDelete.value) {
        await delteStudent(studentToDelete.value);
    }
    deleteConfirmDialog.value = false;
    studentToDelete.value = null;
}

watch(data, (newData) => {
    totalItems.value = newData?.total ?? 0;
}, { deep: true, immediate: true });
</script>

<template>
    <div>
        <ErrorAlert v-if="error" :error="error.statusMessage ?? error.message" />

        <v-data-table-server v-model:items-per-page="itemsPerPage" v-model:page="page" :headers="headers"
            :items="data?.data" :items-length="totalItems" :loading="pending">

            <template #item.operations="{ item }" v-if="adminOps === true">
                <v-tooltip text="Editovať">
                    <template #activator="{ props }">
                        <v-btn icon="mdi-pencil" size="small" variant="text"
                            :to="`/dashboard/admin/students/edit/${item.id}`" class="student-edit-btn" />
                    </template>
                </v-tooltip>
                <v-tooltip text="Vymazať">
                    <template #activator="{ props }">
                        <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                            @click="() => openDeleteDialog(item)" class="student-delete-btn" />
                    </template>
                </v-tooltip>
            </template>
        </v-data-table-server>

        <!-- Delete confirm dialog -->
        <v-dialog v-model="deleteConfirmDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Potvrdiť vymazanie študenta
                </v-card-title>

                <v-card-text>
                    <p>Ste si istý, že chcete vymazať študenta {{ studentToDelete?.name }}?</p>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" variant="text" @click="async () => await confirmDeletion(true)"
                        :loading="pending">
                        Áno
                    </v-btn>
                    <v-btn color="black" variant="text" @click="async () => await confirmDeletion(false)">
                        Zrusiť
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
:deep(.v-data-table-header__content) {
    font-weight: bold;
}
</style>