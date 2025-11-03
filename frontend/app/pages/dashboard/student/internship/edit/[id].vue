<script setup lang="ts">
import { InternshipStatus } from '~/types/internship_status';
import type { Internship } from '~/types/internships';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Edit praxe | ISOP",
    ogTitle: "Edit praxe",
    description: "Edit praxe ISOP",
    ogDescription: "Edit praxe",
});

const route = useRoute();
const { data, refresh } = await useSanctumFetch<Internship>(`/api/internships/${route.params.id}`);
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1 class>Nahratie dokumentov</h1>

            <v-alert v-if="data?.status.status !== InternshipStatus.CONFIRMED" type="error" variant="tonal"
                title="Blokované" text='Vaša prax nie je v stave "Schválená" a teda nemôžete nahrať dokumenty.' />

            <br />

            <InternshipDocumentEditor :internship="data!" @successful-submit="refresh" />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 10px;
}
</style>