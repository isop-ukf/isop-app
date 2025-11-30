<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Portál študenta | ISOP",
    ogTitle: "Portál študenta",
    description: "Portál študenta ISOP",
    ogDescription: "Portál študenta",
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Vitajte, {{ user?.name }}</h1>
            <hr />
            <small>{{ user?.student_data?.study_field }}, {{ user?.student_data?.personal_email }}</small>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-plus" color="blue" class="mr-2" to="/dashboard/student/internships/create">
                Pridať
            </v-btn>
            <v-btn prepend-icon="mdi-domain" color="blue" class="mr-2" to="/dashboard/student/companies">
                Firmy
            </v-btn>
            <v-btn prepend-icon="mdi-pencil" color="orange" class="mr-2" to="/account">
                Môj profil
            </v-btn>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <h3>Moje praxe</h3>

            <InternshipListView mode="student" />
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>