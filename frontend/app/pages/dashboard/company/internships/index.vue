<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'company-only'],
});

useSeoMeta({
    title: "Portál firmy - praxe | ISOP",
    ogTitle: "Portál firmy - praxe",
    description: "Portál firmy - praxe ISOP",
    ogDescription: "Portál firmy - praxe",
});

const headers = [
    { title: 'Študent', key: 'student', align: 'left' },
    { title: 'Od', key: 'start', align: 'left' },
    { title: 'Do', key: 'end', align: 'left' },
    { title: 'Ročník', key: 'year_of_study', align: 'middle' },
    { title: 'Semester', key: 'semester', align: 'middle' },
    { title: 'Stav', key: 'status', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Praxe študentov</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <InternshipListView mode="company" />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>