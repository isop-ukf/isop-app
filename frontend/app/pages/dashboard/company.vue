<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'company-only'],
});

useSeoMeta({
    title: "Portál firmy | ISOP",
    ogTitle: "Portál firmy",
    description: "Portál firmy ISOP",
    ogDescription: "Portál firmy",
});

const user = useSanctumUser<User>();

type ExistingReport = {
    fileName: string
    size: number
    uploadedBy: 'STUDENT' | 'FIRMA'
    uploadedAt: string
    url?: string
}

type Decision = 'APPROVE' | 'REJECT' | null

type Student = {
    id: number | string
    name: string
    existingReport: ExistingReport | null

    // lokálny klientsky stav (front-only)
    reportFile: File | null
    localPreviewUrl?: string | null
    decision: Decision
    rejectNote: string

    // UI stav pre daného študenta
    loading?: boolean
    success?: boolean
    error?: string | null
    formRef?: any
}

// zoznam studentov
const students = ref<Student[]>([
    {
        id: 1,
        name: 'Ján Novák',
        existingReport: {
            fileName: 'vykaz-jan-novak.pdf',
            size: 356812,
            uploadedBy: 'STUDENT',
            uploadedAt: '2025-03-04T10:15:00Z',
            url: '/api/mock-download/vykaz-jan-novak.pdf',
        },
        reportFile: null,
        localPreviewUrl: null,
        decision: null,
        rejectNote: '',
        loading: false,
        success: false,
        error: null,
        formRef: null,
    },
    {
        id: 2,
        name: 'Petra Kováčová',
        existingReport: null, 
        reportFile: null,
        localPreviewUrl: null,
        decision: null,
        rejectNote: '',
        loading: false,
        success: false,
        error: null,
        formRef: null,
    },
])

// validačné pravidlá 
const allowedMimes = ['application/pdf']
const maxSizeBytes = 10 * 1024 * 1024 // 10 MB
const rules = {
    reportRequiredIfMissing: (student: Student) => (file: File | null) =>
        !!student.existingReport || !!file || 'Výkaz je povinný, ak ešte neexistuje.',
    isPdf: (file: File | null) =>
        !file || allowedMimes.includes(file.type) || 'Povolený je iba PDF súbor.',
    maxSize: (file: File | null) =>
        !file || file.size <= maxSizeBytes || 'Maximálna veľkosť súboru je 10 MB.',
    decisionRequired: (v: Decision) => !!v || 'Vyberte, či výkaz potvrdzujete alebo zamietate.',
}

// helpers
function formatSize(bytes: number) {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}
function hasReport(s: Student) {
    return !!s.existingReport
}
function onReportChange(s: Student, file: File | null) {
    if (s.localPreviewUrl) {
        URL.revokeObjectURL(s.localPreviewUrl)
        s.localPreviewUrl = null
    }
    s.reportFile = file
    if (file) s.localPreviewUrl = URL.createObjectURL(file)
}

async function submitFor(s: Student) {
    s.error = null
    s.success = false

    // validácia 
    const ok = await s.formRef?.validate?.()
    if (!ok?.valid) return

    s.loading = true
    try {
        const fd = new FormData()
        if (s.reportFile) {
            fd.append('report', s.reportFile) // nový/aktualizovaný výkaz
        } else if (hasReport(s)) {
            fd.append('report_exists', 'true')
            // fd.append('report_id', '...') // neskôr z backendu
        }
        if (s.decision) fd.append('decision', s.decision)
        if (s.rejectNote && s.decision === 'REJECT') fd.append('reject_note', s.rejectNote)
        fd.append('student_id', String(s.id))

        console.log('Submitting for student', s.name, Array.from(fd.entries()))
        await new Promise((r) => setTimeout(r, 600))

        s.success = true
        if (s.localPreviewUrl) {
            URL.revokeObjectURL(s.localPreviewUrl)
            s.localPreviewUrl = null
        }
        s.reportFile = null
        s.decision = null
        s.rejectNote = ''
        s.formRef?.resetValidation?.()
    } catch (e: any) {
        s.error = e?.message ?? 'Odoslanie zlyhalo. Skúste znova.'
    } finally {
        s.loading = false
    }
}
</script>

<template>
    <v-container fluid>
        <v-card id="footer-card">
            <h1 class="ml-6">Vitajte, {{ user?.name }} <em>({{ user?.company_data?.name }})</em></h1>

            <!-- spacer -->
            <div style="height: 40px;"></div>

            <v-btn prepend-icon="mdi-pencil" color="orange" class="mr-2 ml-6">
                Môj profil
            </v-btn>

            <h2 class="page-container page-title">Výkazy praxe – firma</h2>
            <p class="page-container page-lead">
                Pre každého študenta si môžete výkaz <strong>otvoriť/stiahnuť</strong>, prípadne ho
                <strong>nahrať</strong> za študenta a následne ho <strong>potvrdiť</strong> alebo
                <strong>zamietnuť</strong>.
            </p>

            <div class="form-left">
                <v-row class="mb-4" dense>
                    <v-col cols="12">
                        <v-alert type="info" variant="tonal" title="Postup">
                            <div>• Ak už výkaz existuje, otvoríte ho cez „Otvoriť“ alebo „Stiahnuť“. </div>
                            <div>• Ak výkaz chýba, môžete ho nahrať ako PDF a následne potvrdiť/zamietnuť.</div>
                        </v-alert>
                    </v-col>
                </v-row>

                <!-- Zoznam študentov -->
                <v-row>
                    <v-col v-for="s in students" :key="s.id" cols="12">
                        <v-card variant="outlined" class="mb-4">
                            <div class="student-header">
                                <div class="student-title">
                                    <h3 class="student-name">{{ s.name }}</h3>
                                </div>

                                <div class="student-actions">
                                    <!-- existujúci výkaz  -->
                                    <v-btn
                                        v-if="s.existingReport?.url"
                                        :href="s.existingReport.url"
                                        target="_blank"
                                        variant="tonal"
                                        size="small"
                                        prepend-icon="mdi-open-in-new"
                                    >
                                        Otvoriť
                                    </v-btn>

                                    <v-btn
                                        v-if="s.existingReport?.url"
                                        :href="s.existingReport.url"
                                        target="_blank"
                                        download
                                        variant="text"
                                        size="small"
                                        prepend-icon="mdi-download"
                                    >
                                        Stiahnuť
                                    </v-btn>
                                </div>
                            </div>

                            <v-divider />

                            <v-card-text>
                                <!-- info o existujúcom výkaze -->
                                <v-alert
                                    v-if="s.existingReport"
                                    type="success"
                                    variant="tonal"
                                    class="mb-4"
                                    :title="`Výkaz existuje (${s.existingReport.uploadedBy === 'STUDENT' ? 'nahral študent' : 'nahrala firma'})`"
                                >
                                    <div><strong>Súbor:</strong> {{ s.existingReport.fileName }} ({{ formatSize(s.existingReport.size) }})</div>
                                    <div><strong>Nahraté:</strong> {{ new Date(s.existingReport.uploadedAt).toLocaleString() }}</div>
                                </v-alert>

                                <v-form :ref="el => (s.formRef = el)" @submit.prevent="submitFor(s)">
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <h4 class="section-title">Výkaz</h4>
                                            <v-file-input
                                                class="no-left-gap"
                                                :model-value="s.reportFile"
                                                @update:model-value="onReportChange(s, $event as any)"
                                                :rules="[rules.reportRequiredIfMissing(s), rules.isPdf, rules.maxSize]"
                                                accept=".pdf,application/pdf"
                                                prepend-icon=""
                                                label="Nahrať PDF výkaz"
                                                variant="outlined"
                                                show-size
                                                clearable
                                                :disabled="s.loading"
                                                hint="Povolené: PDF, max 10 MB"
                                                persistent-hint
                                            />

                                            <!-- lokálny náhľad/stiahnutie súboru -->
                                            <div v-if="s.localPreviewUrl" class="mt-2 d-flex ga-2">
                                                <v-btn :href="s.localPreviewUrl" target="_blank" variant="tonal" prepend-icon="mdi-open-in-new">
                                                    Otvoriť náhľad
                                                </v-btn>
                                                <v-btn :href="s.localPreviewUrl" :download="s.reportFile?.name" variant="text" prepend-icon="mdi-download">
                                                    Stiahnuť vybraný súbor
                                                </v-btn>
                                            </div>
                                        </v-col>

                                        <v-col cols="12" md="6">
                                            <h4 class="section-title">Rozhodnutie</h4>
                                            <v-radio-group v-model="s.decision" :rules="[rules.decisionRequired]" inline>
                                                <v-radio label="Potvrdiť" value="APPROVE" />
                                                <v-radio label="Zamietnuť" value="REJECT" />
                                            </v-radio-group>

                                            <v-textarea
                                                v-if="s.decision === 'REJECT'"
                                                v-model="s.rejectNote"
                                                label="Dôvod zamietnutia (nepovinné)"
                                                variant="outlined"
                                                rows="3"
                                                auto-grow
                                                :disabled="s.loading"
                                            />
                                        </v-col>
                                    </v-row>

                                    <div class="actions">
                                        <v-btn :loading="s.loading" color="primary" type="submit">
                                            Odoslať pre {{ s.name }}
                                        </v-btn>
                                    </div>

                                    <v-alert
                                        v-if="s.success"
                                        type="success"
                                        class="mt-3"
                                        title="Odoslané"
                                        text="Rozhodnutie bolo zaznamenané."
                                    />
                                    <v-alert
                                        v-if="s.error"
                                        type="error"
                                        class="mt-3"
                                        title="Chyba"
                                        :text="s.error"
                                    />
                                </v-form>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
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
  font-size: 18px;
  font-weight: 700;
  margin: 8px 0 8px;
  color: #1f1f1f;
}
.student-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
}
.student-title {
  display: flex;
  align-items: center;
  gap: 8px;
}
.student-name {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}
.student-actions {
  display: flex;
  gap: 8px;
}
.actions {
  margin-top: 12px;
  margin-bottom: 8px;
}

</style>