<script setup lang="ts">
import type { Internship, NewInternship } from '~/types/internships';
import { prettyInternshipStatus } from '~/types/internship_status';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only'],
});

useSeoMeta({
    title: "Edit praxe | ISOP",
    ogTitle: "Edit praxe",
    description: "Edit praxe ISOP",
    ogDescription: "Edit praxe",
});

const route = useRoute();
const client = useSanctumClient();

const loading = ref(false);
const action_error = ref(null as null | string);
const refreshKey = ref(0);

async function handleUpdateOfBasicInfo(internship: NewInternship) {
    action_error.value = null;
    loading.value = true;

    try {
        await client(`/api/internships/${route.params.id}/basic`, {
            method: 'POST',
            body: internship
        });

        navigateTo("/dashboard/admin/internships");
    } catch (e) {
        if (e instanceof FetchError && (e.response?.status === 422 || e.response?.status === 400)) {
            action_error.value = e.response?._data.message;
        }
    } finally {
        loading.value = false;
    }
}

const { data, error, refresh } = await useSanctumFetch<Internship>(`/api/internships/${route.params.id}`);
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Edit praxe</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Čakajúca hláška -->
            <v-alert v-if="loading" density="compact" text="Prosím čakajte..." title="Spracovávam" type="info"
                class="mx-auto alert"></v-alert>

            <!-- Chybová hláška -->
            <v-alert v-if="action_error !== null" density="compact" :text="action_error" title="Chyba" type="error"
                class="mx-auto alert"></v-alert>

            <div v-else>
                <!-- Chybová hláška -->
                <v-alert v-if="error" density="compact" :text="error?.message" title="Chyba" type="error"
                    class="mx-auto alert"></v-alert>

                <div v-else>
                    <div>
                        <h2>Základné informácie</h2>
                        <InternshipEditor :internship="data!" :submit="handleUpdateOfBasicInfo" />
                        <hr />
                    </div>

                    <div :key="refreshKey">
                        <h2>Stav</h2>
                        <h4>Aktuálny stav</h4>
                        <p>{{ prettyInternshipStatus(data?.status.status!) }}</p>
                        <p>Poznámka: <em>{{ data?.status.note }}</em></p>
                        <p>Posledná zmena: <em>{{ data?.status.changed }}, {{ data?.status.modified_by.name }}</em></p>

                        <br />

                        <h4>História</h4>
                        <InternshipStatusHistoryView :internship="data?.id" />

                        <br />

                        <h4>Zmena stavu</h4>
                        <InternshipStatusEditor :internship="data!"
                            @successful-submit="() => { refresh(); refreshKey++; }" />
                    </div>

                    <hr />

                    <h2>Dokumenty</h2>
                    <p>...</p>
                    <hr />
                </div>
            </div>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 10px;
}

.alert {
    margin-bottom: 10px;
}

hr {
    margin-top: 20px;
    margin-bottom: 20px;
}
</style>