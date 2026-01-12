<script setup lang="ts">
import { NewRole } from '~/types/role';
import type { NewUser } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:guest'],
});

const client = useSanctumClient();

useSeoMeta({
    title: "Registrácia študenta | ISOP",
    ogTitle: "Registrácia študenta",
    description: "Registrácia študenta ISOP",
    ogDescription: "Registrácia študenta",
});

const rules = {
    required: (v: any) => (!!v && String(v).trim().length > 0) || 'Povinné pole',
    student_email: (v: string) =>
        (/.+@.+\..+/.test(v) && v.endsWith("@student.ukf.sk")) || 'Zadajte platný študentský email',
    personal_email: (v: string) =>
        /.+@.+\..+/.test(v) || 'Zadajte platný osobný email',
    phone: (v: string) =>
        (!v || /^\+[0-9]{6,13}$/.test(v)) || 'Zadajte platné telefónne číslo. Príklad: +421908123456',
    mustAgree: (v: boolean) => v === true || 'Je potrebné súhlasiť',
};
const programs = [
    { title: 'Aplikovaná informatika, Bc. (AI22b)', value: 'AI22b' },
    { title: 'Aplikovaná informatika, Bc. (AI15b)', value: 'AI15b' },
    { title: 'Aplikovaná informatika, Mgr. (AI22m)', value: 'AI22m' },
    { title: 'Aplikovaná informatika, Mgr. (AI15m)', value: 'AI15m' },
];

const isValid = ref(false);
const form = ref({
    firstName: '',
    lastName: '',
    address: '',
    studentEmail: '',
    personalEmail: '',
    phone: '',
    studyProgram: programs[0]!.value,
    year_of_study: 1,
    consent: false,
});
const maxYearOfStudy = ref(0);

const loading = ref(false);
const error = ref(null as null | string);

async function handleRegistration() {
    error.value = null;
    loading.value = true;

    const newUser: NewUser = {
        email: form.value.studentEmail,
        first_name: form.value.firstName,
        last_name: form.value.lastName,
        phone: form.value.phone,
        role: NewRole.STUDENT,
        company_data: undefined,
        student_data: {
            user_id: undefined,
            address: form.value.address,
            personal_email: form.value.personalEmail,
            study_field: form.value.studyProgram
        }
    };

    try {
        await client("/register", {
            method: 'POST',
            body: newUser
        });

        navigateTo("/");
    } catch (e) {
        error.value = simplifyApiError(e);
    } finally {
        loading.value = false;
    }
}

watch(form, (newForm) => {
    maxYearOfStudy.value = newForm.studyProgram.slice(-1) === 'b' ? 3 : 2;
}, { deep: true, immediate: true });
</script>

<template>
    <v-container fluid class="page-container form-wrap">
        <v-card id="page-container-card">
            <h4 class="page-title">Registrácia študenta</h4>

            <!-- Chybová hláška -->
            <ErrorAlert v-if="error" :error="error" />

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="loading" />

            <v-form v-else v-model="isValid" @submit.prevent="handleRegistration">
                <v-text-field v-model="form.firstName" :rules="[rules.required]" label="Meno:" variant="outlined"
                    density="comfortable" />

                <v-text-field v-model="form.lastName" :rules="[rules.required]" label="Priezvisko:" variant="outlined"
                    density="comfortable" />

                <v-text-field v-model="form.address" :rules="[rules.required]" label="Adresa:" variant="outlined"
                    density="comfortable" />

                <v-text-field v-model="form.studentEmail" :rules="[rules.required, rules.student_email]"
                    label="Študentský email:" variant="outlined" density="comfortable" />

                <v-text-field v-model="form.personalEmail" :rules="[rules.required, rules.personal_email]"
                    label="Alternatívny email:" variant="outlined" density="comfortable" />

                <v-text-field v-model="form.phone" :rules="[rules.required, rules.phone]"
                    label="Telefón (s predvoľbou):" variant="outlined" density="comfortable" />

                <v-select v-model="form.studyProgram" :items="programs" :rules="[rules.required]"
                    label="Študijný odbor:" variant="outlined" density="comfortable" />

                <v-number-input control-variant="split" v-model="form.year_of_study" :rules="[rules.required]"
                    label="Ročník:" :min="1" :max="maxYearOfStudy"></v-number-input>

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
.alert {
    margin-bottom: 10px;
}

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
