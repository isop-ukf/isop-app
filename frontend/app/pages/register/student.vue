<script setup lang="ts">
import { FetchError } from 'ofetch';

const rules = {
    required: (v: string) => (!!v && v.trim().length > 0) || 'Povinné pole',
    email: (v: string) =>
        /.+@.+\..+/.test(v) || 'Zadajte platný email',
    optionalEmail: (v: string) =>
        (!v || /.+@.+\..+/.test(v)) || 'Zadajte platný email',
    phone: (v: string) =>
        (!v || /^[0-9 +()-]{6,}$/.test(v)) || 'Zadajte platné telefónne číslo',
    mustAgree: (v: boolean) => v === true || 'Je potrebné súhlasiť',
};
const programs = [
    'Aplikovaná informatika',
];

const isValid = ref(false);
const form = reactive({
    title: '',
    firstName: '',
    lastName: '',
    address: '',
    studentEmail: '',
    altEmail: '',
    phone: '',
    studyProgram: null as (string | null),
    consent: false,
});

const loading = ref(false);
const error = ref(null as null | string);

async function handleRegistration() {
    error.value = null;
    loading.value = true;

    try {
        // TODO: implement
    } catch (e) {
        if (e instanceof FetchError && e.response?.status === 422) {
            error.value = e.response?._data.message;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <v-container fluid class="page-container form-wrap">
        <v-card id="page-container-card">
            <h4 class="page-title">Registrácia študenta</h4>

            <!-- Chybová hláška -->
            <v-alert v-if="error !== null" density="compact" :text="error" title="Chyba" type="error"
                id="login-error-alert" class="mx-auto"></v-alert>

            <!-- Čakajúca hláška -->
            <v-alert v-if="loading" density="compact" text="Prosím čakajte..." title="Spracovávam" type="info"
                id="login-error-alert" class="mx-auto"></v-alert>

            <v-form v-model="isValid" @submit.prevent="handleRegistration">
                <v-text-field v-model="form.title" label="Tituly pred:" variant="outlined" density="comfortable" />

                <v-text-field v-model="form.firstName" :rules="[rules.required]" label="Meno:" variant="outlined"
                    density="comfortable" />

                <v-text-field v-model="form.lastName" :rules="[rules.required]" label="Priezvisko:" variant="outlined"
                    density="comfortable" />

                <v-text-field v-model="form.address" label="Adresa:" variant="outlined" density="comfortable" />

                <v-text-field v-model="form.studentEmail" :rules="[rules.required, rules.email]"
                    label="Študentský email:" variant="outlined" density="comfortable" />

                <v-text-field v-model="form.altEmail" :rules="[rules.optionalEmail]" label="Alternatívny email:"
                    variant="outlined" density="comfortable" />

                <v-text-field v-model="form.phone" :rules="[rules.phone]" label="Telefón:" variant="outlined"
                    density="comfortable" />

                <v-select v-model="form.studyProgram" :items="programs" :rules="[rules.required]"
                    label="Študijný odbor:" variant="outlined" density="comfortable" />

                <v-checkbox v-model="form.consent" :rules="[rules.mustAgree]"
                    label="Súhlasím s podmienkami spracúvania osobných údajov" density="comfortable" />

                <v-btn type="submit" color="success" size="large" block :disabled="!isValid || !form.consent">
                    Registrovať
                </v-btn>
            </v-form>
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

#page-container-card {
    padding: 10px;
}

.page-title {
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700;
    margin: 24px 0 16px;
    color: #1f1f1f;
}

.form-wrap {
    max-width: 640px;
}

.mb-2 {
    margin-bottom: 8px;
}

.mb-3 {
    margin-bottom: 12px;
}

.mb-4 {
    margin-bottom: 16px;
}
</style>
