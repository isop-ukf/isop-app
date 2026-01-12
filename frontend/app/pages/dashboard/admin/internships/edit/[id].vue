<script setup lang="ts">
import type { Internship, NewInternship } from '~/types/internships';
import { prettyInternshipStatus } from '~/types/internship_status';

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

const { data, error, pending, refresh } = await useLazySanctumFetch<Internship>(`/api/internships/${route.params.id}`);

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
        action_error.value = simplifyApiError(e);
    } finally {
        loading.value = false;
    }
}

async function forceRefresh() {
    await refresh();
    refreshKey.value++;
}
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Edit praxe</h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="loading" />

            <!-- Chybová hláška -->
            <ErrorAlert v-if="action_error" :error="action_error" />

            <div v-else>
                <!-- Čakajúca hláška -->
                <LoadingAlert v-if="pending" />

                <!-- Chybová hláška -->
                <ErrorAlert v-else-if="error" :error="error?.message" />

                <div v-else>
                    <div>
                        <h2>Základné informácie</h2>
                        <InternshipEditor :internship="data!" :submit="handleUpdateOfBasicInfo" />
                        <hr />
                    </div>

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
                        <InternshipStatusEditor :internship="data!" @successful-submit="forceRefresh" />
                    </div>

                    <hr />

                    <h2>Dokumenty</h2>
                    <InternshipDocumentViewer :internship="data!" />
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