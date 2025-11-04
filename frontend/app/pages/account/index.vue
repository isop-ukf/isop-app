<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth'],
});

useSeoMeta({
    title: 'Môj profil | ISOP',
    ogTitle: 'Môj profil',
    description: 'Môj profil ISOP',
    ogDescription: 'Môj profil ISOP',
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Môj profil</h1>

            <v-divider class="my-4" />

            <!-- Osobné údaje -->
            <h3>Osobné údaje</h3>
            <v-list density="compact" class="readonly-list">
                <v-list-item>
                    <v-list-item-title>Meno</v-list-item-title>
                    <v-list-item-subtitle>{{ user?.first_name }}</v-list-item-subtitle>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title>Priezvisko</v-list-item-title>
                    <v-list-item-subtitle>{{ user?.last_name }}</v-list-item-subtitle>
                </v-list-item>
            </v-list>

            <!-- Údaje študenta -->
            <div v-if="user?.student_data">
                <h3>Študentské údaje</h3>
                <v-list density="compact" class="readonly-list">
                    <v-list-item>
                        <v-list-item-title>Osobný e-mail</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.student_data.personal_email }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                        <v-list-item-title>Študijný odbor</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.student_data.study_field }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                        <v-list-item-title>Adresa</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.student_data.address }}</v-list-item-subtitle>
                    </v-list-item>
                </v-list>
            </div>

            <!-- Údaje firmy -->
            <div v-if="user?.company_data">
                <h3>Firemné údaje</h3>
                <v-list density="compact" class="readonly-list">
                    <v-list-item>
                        <v-list-item-title>Názov</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.company_data.name }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                        <v-list-item-title>Adresa</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.company_data.address }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                        <v-list-item-title>IČO</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.company_data.ico }}</v-list-item-subtitle>
                    </v-list-item>
                </v-list>
            </div>

            <v-list density="compact" class="readonly-list">
                <v-list-item>
                    <v-btn prepend-icon="mdi mdi-pencil" color="orange">
                        Zmeniť heslo
                    </v-btn>
                </v-list-item>
            </v-list>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}

.readonly-list {
    --v-list-padding-start: 0px;
}

.readonly-list :deep(.v-list-item) {
    --v-list-item-padding-start: 0px;
    padding-left: 0 !important;
}

.readonly-list :deep(.v-list-item__content) {
    padding-left: 0 !important;
}

.readonly-list :deep(.v-list-item-subtitle) {
    white-space: pre-line;
}

.readonly-list :deep(.v-list-item-title) {
    font-weight: 600;
}
</style>
