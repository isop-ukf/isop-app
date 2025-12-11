<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Študenti | ISOP",
    ogTitle: "Študenti",
    description: "Študenti ISOP",
    ogDescription: "Študenti",
});
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Študenti</h1>
            <hr />

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <StudentsListView admin-ops />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>
