<script setup lang="ts">
import { prettyInternshipStatus } from '~/types/internship_status';
import type { Internship } from '~/types/internships';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Praxe študentov | ISOP",
    ogTitle: "Praxe študentov",
    description: "Praxe študentov ISOP",
    ogDescription: "Praxe študentov",
});

const headers = [
    { title: 'Firma', key: 'company', align: 'left' },
    { title: 'Študent', key: 'student', align: 'left' },
    { title: 'Od', key: 'start', align: 'left' },
    { title: 'Do', key: 'end', align: 'left' },
    { title: 'Ročník', key: 'year_of_study', align: 'middle' },
    { title: 'Semester', key: 'semester', align: 'middle' },
    { title: 'Stav', key: 'status', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const { data, error } = await useSanctumFetch<Internship[]>('/api/internships');
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Praxe študentov</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Chybová hláška -->
            <v-alert v-if="error" density="compact" :text="error?.message" title="Chyba" type="error"
                id="login-error-alert" class="mx-auto alert"></v-alert>
            
            <v-table v-else>
                <thead>
                    <tr>
                        <th v-for="header in headers" :class="'text-' + header.align">
                            <strong>{{ header.title }}</strong>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data">
                        <td>{{ item.company.name }}</td>
                        <td>{{ item.user!.name }}</td>
                        <td>{{ item.start }}</td>
                        <td>{{ item.end }}</td>
                        <td>{{ item.year_of_study }}</td>
                        <td>{{ item.semester === "WINTER" ? "Zimný" : "Letný" }}</td>
                        <td>
                            <v-btn class="m-1" density="compact" base-color="grey">
                                {{ prettyInternshipStatus(item.status.status) }}
                            </v-btn>
                        </td>
                        <td class="text-left">
                            <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-pencil" base-color="orange"
                                :to="'/dashboard/admin/internships/edit/' + item.id">Editovať</v-btn>
                            <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-trash-can-outline"
                                base-color="red" @click="async () => { }">Zmazať</v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}

.alert {
    margin-bottom: 10px;
}

.op-btn {
    margin: 10px;
}
</style>