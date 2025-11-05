<script setup lang="ts">
import { prettyInternshipStatus } from '~/types/internship_status';
import type { Internship } from '~/types/internships';
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

const headers = [
    { title: 'Firma', key: 'company', align: 'left' },
    { title: 'Od', key: 'start', align: 'left' },
    { title: 'Do', key: 'end', align: 'left' },
    { title: 'Ročník', key: 'year_of_study', align: 'middle' },
    { title: 'Semester', key: 'semester', align: 'middle' },
    { title: 'Stav', key: 'status', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

const user = useSanctumUser<User>();
const { data, error } = await useSanctumFetch<Internship[]>('/api/internships/my');
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Vitajte, {{ user?.name }}</h1>
            <hr />
            <small>{{ user?.student_data?.study_field }}, {{ user?.student_data?.personal_email }}</small>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-plus" color="blue" class="mr-2" to="/dashboard/student/internship/create">
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
                        <td>{{ item.company.name }}</td>
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
                                :to="'/dashboard/student/internship/edit/' + item.id">Editovať</v-btn>
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

.op-btn {
    margin: 10px;
}
</style>