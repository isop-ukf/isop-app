<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Vytvorenie praxe | ISOP",
    ogTitle: "Vytvorenie praxe",
    description: "Vytvorenie praxe ISOP",
    ogDescription: "Vytvorenie praxe",
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Vytvorenie praxe</h1>

            <br />

            <InternshipEditor :submit="(form: any) => console.log(form)" />
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