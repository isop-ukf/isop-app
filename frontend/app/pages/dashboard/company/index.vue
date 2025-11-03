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
            <h1 class="ml-6">Vitajte, {{ user?.name }} <em>({{ user?.company_data?.name }})</em></h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <td class="text-left">
                <v-btn prepend-icon="mdi-pencil" color="orange" class="mr-2 ml-6 mb-4">
                    Môj profil
                </v-btn>
   
                <v-btn prepend-icon="mdi-pencil" color="orange" class="mr-2 ml-6 mb-4" :to="'/dashboard/company/internship'">
                    Editovať
                </v-btn>
            </td>
        </v-card>
    </v-container>
</template>

<style scoped>


</style>