<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'company-only'],
});

useSeoMeta({
    title: "Portál firmy | ISOP",
    ogTitle: "Portál firmy",
    description: "Portál firmy ISOP",
    ogDescription: "Portál firmy",
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="footer-card">
            <h1>Vitajte, {{ user?.name }} <em>({{ user?.company_data?.name }})</em></h1>


        </v-card>
    </v-container>
</template>