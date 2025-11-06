<script setup lang="ts">
import type { NewInternship } from '~/types/internships';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Vytvorenie praxe | ISOP",
    ogTitle: "Vytvorenie praxe",
    description: "Vytvorenie praxe ISOP",
    ogDescription: "Vytvorenie praxe",
});

const loading = ref(false);
const error = ref(null as null | string);

const client = useSanctumClient();

async function handleInternshipRegistration(internship: NewInternship) {
    try {
        await client("/api/internships/new", {
            method: 'PUT',
            body: internship
        });

        navigateTo("/dashboard/student");
    } catch (e: any) {
        error.value = e.data?.message as string;
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Vytvorenie praxe</h1>

            <br />

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="loading" />

            <!-- Chybová hláška -->
            <ErrorAlert v-if="error" :error="error" />

            <InternshipEditor v-show="!loading" :submit="handleInternshipRegistration" />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 10px;
}

.alert {
    margin-bottom: 10px;
}
</style>