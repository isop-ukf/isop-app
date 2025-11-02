<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Študenti | ISOP",
    ogTitle: "Študenti",
    description: "Študenti ISOP",
    ogDescription: "Študenti",
});

const headers = [
    { title: 'Meno', key: 'name', align: 'left' },
    { title: 'E-mail', key: 'email', align: 'left' },
    { title: 'Telefón', key: 'phone', align: 'left' },
    { title: 'Študijný program', key: 'study_field', align: 'left' },
    { title: 'Osobný e-mail', key: 'personal_email', align: 'middle' },
    { title: 'Adresa', key: 'address', align: 'middle' },
    { title: 'Operácie', key: 'ops', align: 'middle' },
];

// Načítame všetkých študentov
const { data: students, error } = await useSanctumFetch<User[]>('/api/students');
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Študenti</h1>
            <hr />

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Chybová hláška -->
            <v-alert v-if="error" density="compact" :text="error?.message" title="Chyba" type="error"
                id="login-error-alert" class="mx-auto alert"></v-alert>

            <div v-else>
                <p>Aktuálne evidujeme {{ students?.length || 0 }} študentov.</p>

                <br />

                <v-table v-if="students && students.length > 0">
                    <thead>
                        <tr>
                            <th v-for="header in headers" :class="'text-' + header.align">
                                <strong>{{ header.title }}</strong>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in students" :key="item.id">
                            <td>{{ item.name }}</td>
                            <td>{{ item.email }}</td>
                            <td>{{ item.phone || '-' }}</td>
                            <td>{{ item.student_data?.study_field || '-' }}</td>
                            <td>{{ item.student_data?.personal_email || '-' }}</td>
                            <td>{{ item.student_data?.address || '-' }}</td>
                            <td class="text-left">
                                <v-btn class="m-1 op-btn" density="compact" append-icon="mdi-pencil" base-color="orange"
                                    :to="'/dashboard/admin/students/edit/' + item.id">Editovať</v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <v-alert v-else density="compact" text="Zatiaľ nie sú zaregistrovaní žiadni študenti." type="info"
                    class="mt-4"></v-alert>
            </div>
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

.alert {
    margin-top: 10px;
}
</style>
