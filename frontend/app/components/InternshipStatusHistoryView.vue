<script setup lang="ts">
import { prettyInternshipStatus, type InternshipStatusData } from '~/types/internship_status';
import type { Internship } from '~/types/internships';

const props = defineProps<{
    internship: Internship
}>();

const headers = [
    { title: 'Stav', key: 'status', align: 'left' },
    { title: 'Zmenené', key: 'changed', align: 'left' },
    { title: 'Poznámka', key: 'start', align: 'left' },
    { title: 'Zmenu vykonal', key: 'modified_by', align: 'left' },
];

const { data, error, pending, refresh } = await useSanctumFetch<InternshipStatusData[]>(`/api/internships/${props.internship.id}/statuses`);

watch(() => props.internship, () => {
    refresh();
});
</script>

<template>
    <div>
        <!-- Čakajúca hláška -->
        <v-alert v-if="pending" density="compact" text="Prosím čakajte..." title="Spracovávam" type="info"
            class="mx-auto alert"></v-alert>

        <!-- Chybová hláška -->
        <v-alert v-if="error" density="compact" :text="error?.message" title="Chyba" type="error"
            class="mx-auto alert"></v-alert>

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
                    <td>{{ prettyInternshipStatus(item.status) }}</td>
                    <td>{{ item.changed }}</td>
                    <td>{{ item.note }}</td>
                    <td>{{ item.modified_by.name }}</td>
                </tr>
            </tbody>
        </v-table>
    </div>
</template>