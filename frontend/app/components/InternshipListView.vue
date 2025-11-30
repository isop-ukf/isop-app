<script setup lang="ts">
import type { Internship } from '~/types/internships';
import type { Paginated } from '~/types/pagination';
import { prettyInternshipStatus } from '~/types/internship_status';
import { FetchError } from 'ofetch';

const props = defineProps<{
    mode: 'admin' | 'company' | 'student';
}>();

const filters = ref({
    year: null,
    company: null,
    study_programe: null,
    student: null,
});

const page = ref(1);
const itemsPerPage = ref(15);
const totalItems = ref(0);
const deleteConfirmDialog = ref(false);
const internshipToDelete = ref<Internship | null>(null);

const allHeaders = [
    { title: "Študent", key: "student.name", sortable: false },
    { title: "Firma", key: "company.name", sortable: false },
    { title: "Od", key: "start", sortable: false },
    { title: "Do", key: "end", sortable: false },
    { title: "Semester", key: "semester", sortable: false },
    { title: "Ročník", key: "year_of_study", sortable: false },
    { title: "Študijný odbor", key: "student.student_data.study_field", sortable: false },
    { title: "Stav", key: "status", sortable: false },
    { title: "Operácie", key: "operations", sortable: false }
];

const headers = props.mode === 'company'
    ? allHeaders.filter(header => header.key !== "company.name")
    : allHeaders;

const client = useSanctumClient();
const { data, error, pending, refresh } = await useLazySanctumFetch<Paginated<Internship>>('/api/internships', () => ({
    params: {
        ...filters.value,
        page: page.value,
        per_page: itemsPerPage.value,
    }
}), {
    watch: [filters.value, page, itemsPerPage]
});

watch(data, (newData) => {
    totalItems.value = newData?.total ?? 0;
});

watch(filters, async () => {
    page.value = 1;
}, { deep: true });

async function delteInternship(internship: Internship) {
    pending.value = true;

    try {
        await client(`/api/internships/${internship.id}`, {
            method: 'DELETE',
        });
        await refresh();
    } catch (e) {
        if (e instanceof FetchError) {
            alert(`Chyba pri mazaní stáže: ${e.statusMessage ?? e.message}`);
        }
    } finally {
        pending.value = false;
    }
}

function openDeleteDialog(internship: Internship) {
    internshipToDelete.value = internship;
    deleteConfirmDialog.value = true;
}

async function confirmDeletion(confirm: boolean) {
    if (confirm && internshipToDelete.value) {
        await delteInternship(internshipToDelete.value);
    }
    deleteConfirmDialog.value = false;
    internshipToDelete.value = null;
}
</script>

<template>
    <div>
        <ErrorAlert v-if="error" :error="error.statusMessage ?? error.message" />

        <v-row>
            <v-col cols="12" md="3">
                <v-text-field v-model="filters.year" label="Rok" type="number" clearable density="compact" />
            </v-col>
            <v-col cols="12" md="3" v-if="mode !== 'company'">
                <v-text-field v-model="filters.company" label="Názov firmy" clearable density="compact" />
            </v-col>
            <v-col cols="12" md="3" v-if="mode !== 'student'">
                <v-text-field v-model="filters.study_programe" label="Študijný program" clearable density="compact" />
            </v-col>
            <v-col cols="12" md="3" v-if="mode !== 'student'">
                <v-text-field v-model="filters.student" label="Študent" clearable density="compact" />
            </v-col>
        </v-row>

        <v-data-table-server v-model:items-per-page="itemsPerPage" v-model:page="page" :headers="headers"
            :items="data?.data" :items-length="totalItems" :loading="pending">

            <template #item.status="{ item }">
                {{ prettyInternshipStatus(item.status.status) }}
            </template>

            <template #item.semester="{ item }">
                {{ item.semester === "WINTER" ? "Zimný" : "Letný" }}
            </template>

            <template #item.operations="{ item }">
                <v-tooltip text="Editovať">
                    <template #activator="{ props }">
                        <v-btn icon="mdi-pencil" size="small" variant="text"
                            :to="`/dashboard/${mode}/internships/edit/${item.id}`" />
                    </template>
                </v-tooltip>
                <v-tooltip text="Vymazať" v-if="mode === 'admin'">
                    <template #activator="{ props }">
                        <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                            @click="() => openDeleteDialog(item)" />
                    </template>
                </v-tooltip>
            </template>
        </v-data-table-server>

        <!-- Delete confirm dialog -->
        <v-dialog v-model="deleteConfirmDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Potvrdiť vymazanie praxe
                </v-card-title>

                <v-card-text>
                    <p>Ste si istý, že chcete vymazať tento záznam?</p>
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