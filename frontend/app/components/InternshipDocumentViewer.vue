<script setup lang="ts">
import type { Internship } from '~/types/internships';

const props = defineProps<{
    internship: Internship
}>();

const client = useSanctumClient();

async function downloadAgreement() {
    const proof: Blob = await client(`/api/internships/${props.internship.id}/proof`);
    triggerDownload(proof, `proof-${props.internship.id}`);
}

async function downloadReport() {
    const report: Blob = await client(`/api/internships/${props.internship.id}/report`);
    triggerDownload(report, `report-${props.internship.id}`);
}
</script>

<template>
    <div>
        <v-row class="d-flex">
            <!-- Podpísaný dokument k praxe -->
            <v-col cols="12" md="6">
                <v-card variant="outlined" class="h-100">
                    <v-card-title class="d-flex align-center ga-2">
                        <v-icon icon="mdi mdi-file-document-outline" />
                        Dokument o vykonaní praxe
                    </v-card-title>

                    <v-card-text>
                        <p>Zmluva/dohoda o brigádnickej praxi alebo 3 faktúry v pre živnostníkov.</p>

                        <InternshipAgreementDownloader :internship_id="internship.id" />

                        <WarningAlert v-if="!props.internship.proof" title="Neodovzdané"
                            text="Dokument zatiaľ nebol nahratý." />

                        <div v-else>
                            <SuccessAlert title="Odovzdané" text="Zmluva bola nahratá." />

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
                <v-card variant="outlined" class="h-100">
                    <v-card-title class="d-flex align-center ga-2">
                        <v-icon icon="mdi-file-clock-outline" />
                        Výkaz
                    </v-card-title>

                    <v-card-text>
                        <p>Dokument o hodnotení praxe.</p>

                        <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2" block target="_blank"
                            href="https://www.fpvai.ukf.sk/images/Organizacia_studia/odborna_prax/aplikovana_informatika/Priloha_Vykaz_o_vykonanej_odbornej_praxi-AI.docx">
                            <span>Stiahnuť šablónu na výkaz</span>
                        </v-btn>

                        <WarningAlert v-if="!props.internship.report" title="Neodovzdané"
                            text="Výkaz zatiaľ nebol nahratý." />

                        <div v-else>
                            <ErrorAlert v-if="!props.internship.report_confirmed" title="Nepotvrdené"
                                error="Výkaz bol nahratý, ale zatiaľ nebol potvrdený firmou." />

                            <SuccessAlert v-else title="Potvrdené" text="Výkaz bol nahratý, aj potvrdený firmou." />

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
