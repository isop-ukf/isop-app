<script setup lang="ts">
import type { Internship } from '~/types/internships';

const props = defineProps<{
    internship: Internship
}>();

const client = useSanctumClient();

function triggerDownload(file: Blob, file_name: string) {
    const url = window.URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${file_name}.pdf`;
    link.target = "_blank";
    link.click();
    window.URL.revokeObjectURL(url);
}

async function downloadAgreement() {
    const agreement: Blob = await client(`/api/internships/${props.internship.id}/agreement`);
    triggerDownload(agreement, `agreement-${props.internship.id}`);
}

async function downloadReport() {
    const report: Blob = await client(`/api/internships/${props.internship.id}/report`);
    triggerDownload(report, `report-${props.internship.id}`);
}
</script>

<template>
    <div>
        <v-row>
            <!-- Podpísaná zmluva -->
            <v-col cols="12" md="6">
                <v-card variant="outlined">
                    <v-card-title class="d-flex align-center ga-2">
                        <v-icon icon="mdi mdi-file-document-outline" />
                        Podpísaná zmluva / dohoda
                    </v-card-title>
                    <v-card-text>
                        <v-alert v-if="!props.internship.agreement" type="warning" variant="tonal" title="Neodovzdané"
                            text="Zmluva zatiaľ nebola nahratá." />

                        <div v-else>
                            <v-alert type="success" variant="tonal" title="Odovzdané" text="Zmluva bola nahratá." />

                            <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2" block
                                @click="downloadAgreement">
                                Stiahnuť
                            </v-btn>
                        </div>
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
                        <v-alert v-if="!props.internship.report" type="info" variant="tonal" title="Neodovzdané"
                            text="Výkaz zatiaľ nebol nahratý." />

                        <div v-else>
                            <v-alert v-if="!props.internship.report_confirmed" type="error" variant="tonal"
                                title="Nepotvrdené" text="Výkaz bol nahratý, ale zatiaľ nebol potvrdený firmou." />

                            <v-alert v-else type="success" variant="tonal" title="Potvrdené"
                                text="Výkaz bol nahratý, aj potvrdený firmou." />

                            <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2" block
                                @click="downloadReport">
                                Stiahnuť
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
