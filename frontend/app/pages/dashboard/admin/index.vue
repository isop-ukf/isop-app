<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Portál administrátora | ISOP",
    ogTitle: "Portál administrátora",
    description: "Portál administrátora ISOP",
    ogDescription: "Portál administrátora",
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Vitajte, {{ user?.name }}</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-domain" color="blue" class="mr-2" to="/dashboard/admin/companies">
                Firmy
            </v-btn>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <p>...</p>
        </v-card>
    </v-container>
</template>


<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>