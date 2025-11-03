<script setup lang="ts">
import type { User } from '~/types/user';

type PracticeStatus = 'NOVA' | 'V_KONTROLE' | 'SCHVALENA' | 'ZAMIETNUTA' | 'UKONCENA'

/* sem pôjde reálny stav praxe */
const practiceStatus = ref<PracticeStatus>('SCHVALENA')

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Edit praxe | ISOP",
    ogTitle: "Edit praxe",
    description: "Edit praxe ISOP",
    ogDescription: "Edit praxe",
});

const user = useSanctumUser<User>();

const formRef = ref()

// súbory
const contractFile = ref<File | null>(null)
const reportFile = ref<File | null>(null)

// nastavenie súborov (formát a veľkosť)
const allowedMimes = ['application/pdf']
const maxSizeBytes = 10 * 1024 * 1024 // 10 MB

// helpery
const isApproved = computed(() => practiceStatus.value === 'SCHVALENA')

// validácia
const rules = {
    requiredIfApproved: (v: File | null) =>
        !isApproved.value || !!v || 'Zmluva je povinná pri stave Schválená.',
    isPdf: (v: File | null) =>
        !v || allowedMimes.includes(v.type) || 'Povolený je iba PDF súbor.',
    maxSize: (v: File | null) =>
        !v || v.size <= maxSizeBytes || 'Maximálna veľkosť súboru je 10 MB.',
}

// UI stav
const loading = ref(false)
const success = ref(false)
const error = ref<string | null>(null)

function resetForm() {
    contractFile.value = null
    reportFile.value = null
    formRef.value?.resetValidation?.()
}

/* Mock submit – sem API */
async function submit() {
    error.value = null
    success.value = false

    const ok = await formRef.value?.validate?.()
    if (!ok?.valid) return

    loading.value = true
    try {
        // tvorba payloadu
        const fd = new FormData()
        if (contractFile.value) fd.append('contract', contractFile.value)
        if (reportFile.value) fd.append('report', reportFile.value)

        // sem pôjde endpoint
        await new Promise((r) => setTimeout(r, 800))

        success.value = true
        resetForm()
    } catch (e: any) {
        error.value = e?.message ?? 'Nahrávanie zlyhalo. Skúste znova.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <v-container fluid>
        <v-card>
            <h2 class="page-container page-title">Nahratie dokumentov</h2>
            <p class="page-container page-lead">
                Nahrajte podpísanú zmluvu (povinné pri stave <strong>Schválená</strong>) a výkaz (nepovinné).
            </p>

            <div class="form-left">
                <v-alert
                    v-if="isApproved"
                    type="info"
                    variant="tonal"
                    class="mb-4"
                    title="Zmluva je povinná"
                    text="Vaša prax je v stave Schválená - nahratie podpísanej zmluvy je vyžadované pred pokračovaním."
                />

                <v-chip
                    class="mb-4"
                    color="primary"
                    variant="flat"
                    :prepend-icon="isApproved ? 'mdi-check-decagram' : 'mdi-progress-clock'"
                >
                    Stav praxe: {{ practiceStatus }}
                </v-chip>

                <v-form ref="formRef" @submit.prevent="submit">
                    <v-row align="stretch" justify="start">
                        <v-col cols="12" md="6">
                            <h3 class="section-title">Podpísaná zmluva</h3>
                            <v-file-input
                                v-model="contractFile"
                                :rules="[rules.requiredIfApproved, rules.isPdf, rules.maxSize]"
                                accept=".pdf,application/pdf"
                                prepend-icon=""
                                label="Nahrať PDF zmluvu"
                                variant="outlined"
                                show-size
                                clearable
                                :disabled="loading"
                                hint="Povolené: PDF, max 10 MB"
                                persistent-hint
                            />
                        </v-col>

                        <v-col cols="12" md="6">
                            <h3 class="section-title">Výkaz (nepovinné)</h3>
                            <v-file-input
                                v-model="reportFile"
                                :rules="[rules.isPdf, rules.maxSize]"
                                accept=".pdf,application/pdf"
                                prepend-icon=""
                                label="Nahrať PDF výkaz"
                                variant="outlined"
                                show-size
                                clearable
                                :disabled="loading"
                                hint="Povolené: PDF, max 10 MB"
                                persistent-hint
                            />
                        </v-col>
                    </v-row>

                    <br/>

                    <div class="actions">
                        <v-btn :loading="loading" color="primary" type="submit">
                            Odoslať
                        </v-btn>
                    </div>

                    <v-alert
                        v-if="success"
                        type="success"
                        class="mt-4 mb-4"
                        title="Dokumenty nahrané"
                        text="Vaše dokumenty boli úspešne odoslané."
                    />
                    <v-alert
                        v-if="error"
                        type="error"
                        class="mt-4 mb-4"
                        title="Chyba"
                        :text="error"
                    />
                </v-form>
            </div>
        </v-card>
    </v-container>
</template>

<style scoped>

.page-container {
  max-width: 1120px;
  margin: 0 auto;
  padding-left: 24px;
  padding-right: 24px;
}

.form-left {
  padding-left: 24px;
  padding-right: 24px;
}

.page-title {
  font-size: 32px;
  line-height: 1.2;
  font-weight: 700;
  margin: 16px 0 8px;
  color: #1f1f1f;
}

.page-lead {
  margin: 0 0 24px 0;
  color: #6b6b6b;
}

.section-title {
  font-size: 22px;
  font-weight: 700;
  margin: 16px 0 12px;
  color: #1f1f1f;
}

.actions {
  margin-left: 0;
  margin-top: 8px;
  margin-bottom: 24px;
}

</style>