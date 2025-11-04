<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'], 
});

useSeoMeta({
    title: 'Môj profil | ISOP',
    ogTitle: 'Môj profil',
    description: 'Profil študenta ISOP',
    ogDescription: 'Profil študenta ISOP',
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid class="page-container">
        <v-card id="profile-card">
            <template v-if="user">
                <div class="header">
                    <v-avatar size="64" class="mr-4">
                        <v-icon size="48">mdi-account-circle</v-icon>
                    </v-avatar>
                    <div>
                        <h2 class="title">Môj profil</h2>
                        <p class="subtitle">{{ user?.first_name }} {{ user?.last_name }}</p>
                    </div>
                </div>

                <v-divider class="my-4" />

                <v-row>
                    <!-- Osobné údaje + štúdium -->
                    <v-col cols="12" md="6">
                        <h3 class="section-title">Osobné údaje</h3>
                        <v-list density="compact" class="readonly-list">
                            <v-list-item>
                                <v-list-item-title>Meno</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.first_name || '—' }}</v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <v-list-item-title>Priezvisko</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.last_name || '—' }}</v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <v-list-item-title>Adresa</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.student_data?.address || '—' }}</v-list-item-subtitle>
                            </v-list-item>
           
                            <v-list-item>
                                <v-list-item-title>Študijný odbor</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.student_data?.study_field || '—' }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-col>

                    <!-- Kontaktné údaje -->
                    <v-col cols="12" md="6">
                        <h3 class="section-title">Kontaktné údaje</h3>
                        <v-list density="compact" class="readonly-list">
                            <v-list-item>
                                <v-list-item-title>Študentský email</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.email || '—' }}</v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <v-list-item-title>Alternatívny email</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.student_data?.personal_email || '—' }}</v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <v-list-item-title>Telefón</v-list-item-title>
                                <v-list-item-subtitle>{{ user?.phone || '—' }}</v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item class="mt-4">
                                <v-btn prepend-icon="mdi mdi-pencil" color="orange" class="mr-2">
                                    Zmeniť heslo
                                </v-btn>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
            </template>

            <template v-else>
                <v-skeleton-loader
                    type="heading, text, text, divider, list-item-two-line, list-item-two-line, list-item-two-line"
                />
            </template>
        </v-card>
    </v-container>
</template>

<style scoped>

.page-container {
    max-width: 1120px;
    margin: 0 auto;
    padding-left: 24px;
    padding-right: 24px;
}

#profile-card { padding: 16px; }
.header { display: flex; align-items: center; }
.title { font-size: 24px; font-weight: 700; margin: 0; }
.subtitle { color: #555; margin-top: 4px; }
.section-title { font-size: 16px; font-weight: 700; margin: 8px 0 8px; }
.mt-3 { margin-top: 12px; }

.readonly-list { --v-list-padding-start: 0px; }
.readonly-list :deep(.v-list-item) {
    --v-list-item-padding-start: 0px;
    padding-left: 0 !important;
}
.readonly-list :deep(.v-list-item__content) { padding-left: 0 !important; }
.readonly-list :deep(.v-list-item-subtitle) { white-space: pre-line; }
.readonly-list :deep(.v-list-item-title) { font-weight: 600; }

</style>
