<script setup lang="ts">
import { InternshipStatus, prettyInternshipStatus } from '~/types/internship_status';
import type { Internship, NewInternship } from '~/types/internships';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'company-only'],
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

const { data, error: load_error, pending, refresh } = await useLazySanctumFetch<Internship>(`/api/internships/${route.params.id}`);

async function handleUpdateOfBasicInfo(internship: NewInternship) {
    action_error.value = null;
    loading.value = true;

    try {
        await client(`/api/internships/${route.params.id}/basic`, {
            method: 'POST',
            body: internship
        });

        navigateTo("/dashboard/company/internships");
    } catch (e) {
        if (e instanceof FetchError) {
            action_error.value = e.response?._data.message;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Edit praxe</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="pending" />

            <!-- Chybová hláška -->
            <ErrorAlert v-else-if="load_error" :error="load_error.message" />

            <div v-else>
                <!-- Čakajúca hláška -->
                <LoadingAlert v-if="loading" />

                <!-- Chybová hláška -->
                <ErrorAlert v-else-if="action_error" :error="action_error" />

                <div v-else>
                    <div>
                        <h2>Základné informácie</h2>
                        <ErrorAlert v-if="data?.status.status !== InternshipStatus.SUBMITTED" title="Blokované"
                            error='Vaša prax nie je v stave "Zadaná" a teda nemôžete meniť údaje' />
                        <InternshipEditor v-else :internship="data!" :submit="handleUpdateOfBasicInfo" />
                    </div>

                    <hr />

                    <div>
                        <h2>Stav</h2>
                        <h4>Aktuálny stav</h4>
                        <p>{{ prettyInternshipStatus(data?.status.status!) }}</p>
                        <p>Poznámka: <em>{{ data?.status.note }}</em></p>
                        <p>Posledná zmena: <em>{{ data?.status.changed }}, {{ data?.status.modified_by.name }}</em></p>

                        <br />

                        <h4>História</h4>
                        <InternshipStatusHistoryView :internship="data!" />

                        <br />

                        <h4>Zmena stavu</h4>
                        <InternshipStatusEditor :internship="data!"
                            @successful-submit="() => { refresh(); refreshKey++; }" />
                    </div>

                    <hr />

                    <div>
                        <h2>Nahratie dokumentov</h2>

                        <ErrorAlert v-if="data?.status.status !== InternshipStatus.CONFIRMED" title="Blokované"
                            error='Vaša prax nie je v stave "Schválená" a teda nemôžete nahrať dokumenty.' />

                        <InternshipDocumentEditor v-else :internship="data!" @successful-submit="refresh" />
                    </div>
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

hr {
    margin-top: 20px;
    margin-bottom: 20px;
}
</style>