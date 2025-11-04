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
        <v-card id="page-container-card">
            <h1>Vitajte, {{ user?.name }} <em>({{ user?.company_data?.name }})</em></h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-account-circle" color="blue" class="mr-2" to="/dashboard/company/profile">
                Môj profil
            </v-btn>
            <v-btn prepend-icon="mdi-briefcase" color="blue" class="mr-2" to="/dashboard/company/internships">
                Praxe
            </v-btn>

            <!-- spacer -->
            <div style="height: 40px;"></div>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>