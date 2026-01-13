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

            <v-row align="center" no-gutters>
                <v-col cols="auto" class="mr-3">
                    <v-btn prepend-icon="mdi-briefcase" color="blue" to="/dashboard/company/internships">
                        Praxe
                    </v-btn>
                </v-col>
                <v-col cols="auto" class="mr-3">
                    <v-btn prepend-icon="mdi-account-circle" color="blue" to="/account">
                        Môj profil
                    </v-btn>
                </v-col>
                <v-col cols="auto">
                    <CompanyHiringToggle :company="user!.company_data!.id"
                        :current_value="user!.company_data!.hiring" />
                </v-col>
            </v-row>

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