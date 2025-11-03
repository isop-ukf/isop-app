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

// ---- helpery pre sekciu Dokumenty ----
const docs = computed(() => {
    const d: any = data.value ?? {}
    return {
        contract: d.documents?.contract ?? d.contract ?? null,
        report: d.documents?.report ?? d.report ?? null,
    }
})

function docUrl(doc: any) {
    return doc?.url ?? doc?.download_url ?? doc?.link ?? null
}
function docName(doc: any) {
    return doc?.fileName ?? doc?.filename ?? doc?.name ?? 'dokument.pdf'
}
function docSize(doc: any) {
    const bytes = doc?.size ?? doc?.filesize ?? null
    if (!bytes && bytes !== 0) return null
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}
function docDate(doc: any) {
    const dt = doc?.uploadedAt ?? doc?.created_at ?? doc?.uploaded_at ?? null
    return dt ? new Date(dt).toLocaleString() : null
}
function docBy(doc: any) {
    return doc?.uploadedBy?.name ?? doc?.uploaded_by?.name ?? doc?.uploaded_by ?? null
}
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

                    <h2>Dokumenty</h2>
                    <v-row>
                        <!-- Podpísaná zmluva -->
                        <v-col cols="12" md="6">
                            <v-card variant="outlined">
                                <v-card-title class="d-flex align-center ga-2">
                                    <v-icon icon="mdi mdi-file-document-outline" />
                                    Podpísaná zmluva
                                </v-card-title>
                                <v-card-text>
                                    <template v-if="docs.contract">
                                        <div><strong>Súbor:</strong> {{ docName(docs.contract) }}</div>
                                        <div v-if="docDate(docs.contract)"><strong>Nahrané:</strong> {{ docDate(docs.contract) }}</div>
                                        <div v-if="docBy(docs.contract)"><strong>Nahral:</strong> {{ docBy(docs.contract) }}</div>
                                        <div v-if="docSize(docs.contract)"><strong>Veľkosť:</strong> {{ docSize(docs.contract) }}</div>

                                        <div class="mt-3 d-flex ga-2">
                                            <v-btn
                                                v-if="docUrl(docs.contract)"
                                                :href="docUrl(docs.contract)"
                                                target="_blank"
                                                variant="tonal"
                                                prepend-icon="mdi-open-in-new"
                                            >
                                                Otvoriť náhľad
                                            </v-btn>
                                            <v-btn
                                                v-if="docUrl(docs.contract)"
                                                :href="docUrl(docs.contract)"
                                                :download="docName(docs.contract)"
                                                variant="text"
                                                prepend-icon="mdi-download"
                                            >   
                                                Stiahnuť
                                            </v-btn>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <v-alert type="warning" variant="tonal" title="Neodovzdané" text="Zmluva zatiaľ nie je nahratá." />
                                    </template>
                                </v-card-text>
                            </v-card>
                        </v-col>

                        <!-- Výkaz -->
                        <v-col cols="12" md="6">
                            <v-card variant="outlined">
                                <v-card-title class="d-flex align-center ga-2">
                                    <v-icon icon="mdi-file-clock-outline" />
                                    Výkaz
                                </v-card-title>
                                <v-card-text>
                                    <template v-if="docs.report">
                                        <div><strong>Súbor:</strong> {{ docName(docs.report) }}</div>
                                        <div v-if="docDate(docs.report)"><strong>Nahrané:</strong> {{ docDate(docs.report) }}</div>
                                        <div v-if="docBy(docs.report)"><strong>Nahral:</strong> {{ docBy(docs.report) }}</div>
                                        <div v-if="docSize(docs.report)"><strong>Veľkosť:</strong> {{ docSize(docs.report) }}</div>

                                        <div class="mt-3 d-flex ga-2">
                                            <v-btn
                                                v-if="docUrl(docs.report)"
                                                :href="docUrl(docs.report)"
                                                target="_blank"
                                                variant="tonal"
                                                prepend-icon="mdi-open-in-new"
                                            >
                                                Otvoriť náhľad
                                            </v-btn>
                                            <v-btn
                                                v-if="docUrl(docs.report)"
                                                :href="docUrl(docs.report)"
                                                :download="docName(docs.report)"
                                                variant="text"
                                                prepend-icon="mdi-download"
                                            >
                                                Stiahnuť
                                            </v-btn>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <v-alert type="info" variant="tonal" title="Neodovzdané" text="Výkaz zatiaľ nie je nahratý." />
                                    </template>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
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