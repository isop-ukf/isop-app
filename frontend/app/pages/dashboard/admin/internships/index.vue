<script setup lang="ts">
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
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Praxe študentov</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <InternshipListView mode="admin" />
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