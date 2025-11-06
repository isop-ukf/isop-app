<script setup lang="ts">
import type { Internship } from '~/types/internships';
import { prettyInternshipStatus } from '~/types/internship_status';

definePageMeta({
    middleware: ['sanctum:auth', 'company-only'],
});

useSeoMeta({
    title: "Portál firmy - praxe | ISOP",
    ogTitle: "Portál firmy - praxe",
    description: "Portál firmy - praxe ISOP",
    ogDescription: "Portál firmy - praxe",
});

const headers = [
    { title: 'Študent', key: 'student', align: 'left' },
    { title: 'Od', key: 'start', align: 'left' },
    { title: 'Do', key: 'end', align: 'left' },
    { title: 'Ročník', key: 'year_of_study', align: 'middle' },
    { title: 'Semester', key: 'semester', align: 'middle' },
    { title: 'Stav', key: 'status', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const { data, error } = await useSanctumFetch<Internship[]>('/api/internships/my');
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Praxe študentov</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Chybová hláška -->
            <ErrorAlert v-if="error" :error="error?.message" />

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
                        <td>{{ item.student.name }}</td>
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
                                :to="'/dashboard/company/internships/edit/' + item.id">Editovať</v-btn>
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