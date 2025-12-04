<script setup lang="ts">
import type { Internship } from '~/types/internships';
import { FetchError } from 'ofetch';
import type { User } from '~/types/user';
import { Role } from '~/types/role';

const props = defineProps<{
    internship: Internship
}>();
const emit = defineEmits(['successfulSubmit']);

const rules = {
    isPdf: (v: File | null) =>
        !v || v.type === "application/pdf" || 'Povolený je iba PDF súbor.',
    maxSize: (v: File | null) =>
        !v || v.size <= (10 * 1024 * 1024 /* 10 MB */) || 'Maximálna veľkosť súboru je 10 MB.',
};

const loading = ref(false);
const error = ref<string | null>(null);
const proof = ref<File | null>(null);
const report = ref<File | null>(null);
const report_confirmed = ref(props.internship.report_confirmed);

const client = useSanctumClient();
const user = useSanctumUser<User>();

function triggerDownload(file: Blob, file_name: string) {
    const url = window.URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${file_name}.pdf`;
    link.target = "_blank";
    link.click();
    window.URL.revokeObjectURL(url);
}

async function downloadProof() {
    const proof: Blob = await client(`/api/internships/${props.internship.id}/proof`);
    triggerDownload(proof, `proof-${props.internship.id}`);
}

async function downloadReport() {
    const report: Blob = await client(`/api/internships/${props.internship.id}/report`);
    triggerDownload(report, `report-${props.internship.id}`);
}

async function onSubmit() {
    error.value = null;
    loading.value = true;

    const formData = new FormData();
    formData.append('report_confirmed', report_confirmed.value ? '1' : '0');
    if (proof.value) {
        formData.append('proof', proof.value);
    }
    if (report.value) {
        formData.append('report', report.value);
    }

    try {
        await client(`/api/internships/${props.internship.id}/documents`, {
            method: 'POST',
            body: formData
        });

        proof.value = null;
        report.value = null;
        emit('successfulSubmit');
    } catch (e) {
        if (e instanceof FetchError) {
            error.value = e.response?._data.message;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div>
        <!-- Čakajúca hláška -->
        <LoadingAlert v-if="loading" />

        <!-- Chybová hláška -->
        <ErrorAlert v-if="error" :error="error" />

        <v-form @submit.prevent="onSubmit" :disabled="loading">
            <div>
                <h4 class="mb-2">Dokument o vykonaní praxe</h4>

                <p>Zmluva/dohoda o brigádnickej praxi alebo 3 faktúry v pre živnostníkov.</p>
                <InternshipAgreementDownloader :internship_id="internship.id" />

                <WarningAlert v-if="props.internship.proof" title="Existujúci dokument"
                    text="V systéme je už nahratý dokument. Ak chcete nahradiť existujúcu verziu, vyberte súbor, alebo v opačnom prípade nechajte toto pole nevyplnené.">
                    <br />

                    <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2" @click="downloadProof">
                        Stiahnuť
                    </v-btn>
                </WarningAlert>

                <v-file-input v-model="proof" :rules="[rules.isPdf, rules.maxSize]" accept=".pdf,application/pdf"
                    prepend-icon="mdi-handshake" label="Nahrať PDF dokument" variant="outlined" show-size clearable
                    hint="Povolené: PDF, max 10 MB" persistent-hint />
            </div>

            <br />

            <div>
                <h4 class="mb-2">Výkaz</h4>

                <p>Dokument o hodnotení praxe.</p>
                <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2 mb-2" target="_blank"
                    href="https://www.fpvai.ukf.sk/images/Organizacia_studia/odborna_prax/aplikovana_informatika/Priloha_Vykaz_o_vykonanej_odbornej_praxi-AI.docx">
                    <span>Stiahnuť šablónu na výkaz</span>
                </v-btn>

                <WarningAlert v-if="props.internship.report" title="Existujúci dokument"
                    text="V systéme je už nahratý výkaz. Ak chcete nahradiť existujúcu verziu, vyberte súbor, alebo v opačnom prípade nechajte toto pole nevyplnené.">

                    <br />

                    <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2" @click="downloadReport">
                        Stiahnuť
                    </v-btn>
                </WarningAlert>

                <v-file-input v-model="report" :rules="[rules.isPdf, rules.maxSize]" accept=".pdf,application/pdf"
                    prepend-icon="mdi-chart-box-outline" label="Nahrať PDF výkaz" variant="outlined" show-size clearable
                    hint="Povolené: PDF, max 10 MB" persistent-hint />

                <v-checkbox v-if="user?.role === Role.EMPLOYER"
                    :disabled="!props.internship.proof || !props.internship.report" v-model="report_confirmed"
                    label="Výkaz je správny"></v-checkbox>
            </div>

            <br />

            <v-btn type="submit" color="success" size="large" block
                :disabled="!proof && !report && (!props.internship.proof || !props.internship.report)">
                Uloziť
            </v-btn>
        </v-form>
    </div>
</template>

<style scoped></style>
