<script setup lang="ts">
import type { InternshipFilter } from '~/types/internships';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Praxe študentov | ISOP",
    ogTitle: "Praxe študentov",
    description: "Praxe študentov ISOP",
    ogDescription: "Praxe študentov",
});

const headers = [
    { title: 'Firma', key: 'company', align: 'left' },
    { title: 'Študent', key: 'student', align: 'left' },
    { title: 'Od', key: 'start', align: 'left' },
    { title: 'Do', key: 'end', align: 'left' },
    { title: 'Ročník', key: 'year_of_study', align: 'middle' },
    { title: 'Semester', key: 'semester', align: 'middle' },
    { title: 'Stav', key: 'status', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const internshipFilters = ref<InternshipFilter | null>(null);
const exportPending = ref(false);
const exportAvailable = ref(true);

const client = useSanctumClient();

async function requestExport() {
    exportPending.value = true;

    try {
        const file = await client<Blob>(`/api/internships/export`, {
            method: 'GET',
            query: internshipFilters.value ?? {},
        });
        triggerDownload(file, 'internships_export', 'csv');
    } catch (e) {
        if (e instanceof FetchError) {
            alert(`Chyba pri exportovaní: ${e.statusMessage ?? e.message}`);
        }
    } finally {
        exportPending.value = false;
    }
}
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Praxe študentov</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-file-export-outline" color="green" class="mr-2 mb-2" @click="requestExport"
                :disabled="!exportAvailable" :loading="exportPending">
                <v-tooltip activator="parent" location="top">
                    Exportovať aktuálne zobrazené výsledky do CSV súboru
                </v-tooltip>
                Export výsledkov
            </v-btn>

            <InternshipListView mode="admin" @filterApplied="(filters) => internshipFilters = filters"
                @itemsAvailable="(available) => exportAvailable = available" />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}

.alert {
    margin-bottom: 10px;
}

.op-btn {
    margin: 10px;
}
</style>