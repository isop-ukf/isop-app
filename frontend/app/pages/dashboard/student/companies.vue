<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Partnerské firmy | ISOP",
    ogTitle: "Partnerské firmy",
    description: "Partnerské firmy ISOP",
    ogDescription: "Partnerské firmy",
});
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Partnerské firmy</h1>
            <hr />

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <CompanyListView />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>