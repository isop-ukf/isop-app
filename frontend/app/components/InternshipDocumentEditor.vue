<script setup lang="ts">
import type { Internship } from '~/types/internships';
import { FetchError } from 'ofetch';

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
const agreement = ref<File | null>(null);
const report = ref<File | null>(null);

const client = useSanctumClient();

async function onSubmit() {
    error.value = null;
    loading.value = true;

    const formData = new FormData();
    if (agreement.value) {
        formData.append('agreement', agreement.value);
    }
    if (report.value) {
        formData.append('report', report.value);
    }

    try {
        await client(`/api/internships/${props.internship.id}/documents`, {
            method: 'POST',
            body: formData
        });

        agreement.value = null;
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
        <v-alert v-if="loading" density="compact" text="Prosím čakajte..." title="Spracovávam" type="info"
            class="mx-auto mb-2"></v-alert>

        <!-- Chybová hláška -->
        <v-alert v-if="error" density="compact" :text="error" title="Chyba" type="error" class="mx-auto mb-2"></v-alert>

        <v-form @submit.prevent="onSubmit" :disabled="loading">
            <div>
                <h4 class="mb-2">Podpísaná zmluva / dohoda</h4>

                <v-alert v-if="props.internship.agreement" class="mb-2" type="warning" variant="tonal"
                    title="Existujúci dokument"
                    text="V systéme je už nahratá zmluva/dohoda. Ak chcete nahradiť existujúcu verziu, vyberte súbor, alebo v opačnom prípade nechajte toto pole nevyplnené." />

                <v-file-input v-model="agreement" :rules="[rules.isPdf, rules.maxSize]" accept=".pdf,application/pdf"
                    prepend-icon="mdi-handshake" label="Nahrať PDF zmluvu" variant="outlined" show-size clearable
                    hint="Povolené: PDF, max 10 MB" persistent-hint />
            </div>

            <br />

            <div>
                <h4 class="mb-2">Výkaz</h4>

                <v-alert v-if="props.internship.report" class="mb-2" type="warning" variant="tonal"
                    title="Existujúci dokument"
                    text="V systéme je už nahratý výkaz. Ak chcete nahradiť existujúcu verziu, vyberte súbor, alebo v opačnom prípade nechajte toto pole nevyplnené." />

                <v-file-input v-model="report" :rules="[rules.isPdf, rules.maxSize]" accept=".pdf,application/pdf"
                    prepend-icon="mdi-chart-box-outline" label="Nahrať PDF výkaz" variant="outlined" show-size clearable
                    hint="Povolené: PDF, max 10 MB" persistent-hint />
            </div>

            <br />

            <v-btn type="submit" color="success" size="large" block :disabled="!agreement && !report">
                Uloziť
            </v-btn>
        </v-form>
    </div>
</template>

<style scoped></style>
