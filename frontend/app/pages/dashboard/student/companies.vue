<script setup lang="ts">
import type { CompanyData } from '~/types/company_data';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Partnerské firmy | ISOP",
    ogTitle: "Partnerské firmy",
    description: "Partnerské firmy ISOP",
    ogDescription: "Partnerské firmy",
});

const headers = [
    { title: 'Názov', key: 'name', align: 'left' },
    { title: 'IČO', key: 'ico', align: 'left' },
    { title: 'Kontaktná osoba', key: 'contact_name', align: 'left' },
    { title: 'Telefón', key: 'phone', align: 'middle' },
    { title: 'E-mail', key: 'email', align: 'middle' },
    { title: 'Prijímajú študentov', key: 'hiring', align: 'middle' },
];

const { data, error, pending } = await useLazySanctumFetch<CompanyData[]>('/api/companies/simple');
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Partnerské firmy</h1>
            <hr />

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="pending" />

            <!-- Chybová hláška -->
            <ErrorAlert v-else-if="error" :error="error?.message" />

            <div v-else>
                <p>Aktuálne spolupracujeme s {{ data?.length }} firmami.</p>

                <br />

                <v-table>
                    <thead>
                        <tr>
                            <th v-for="header in headers" :class="'text-' + header.align">
                                <strong>{{ header.title }}</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in data">
                            <td>{{ item.name }}</td>
                            <td>{{ item.ico }}</td>
                            <td>{{ item.contact.name }}</td>
                            <td>{{ item.contact.phone }}</td>
                            <td>{{ item.contact.email }}</td>
                            <td>{{ item.hiring ? "Áno" : "Nie" }}</td>
                        </tr>
                    </tbody>
                </v-table>
            </div>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>