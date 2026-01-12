<script setup lang="ts">
import { NewRole } from '~/types/role';
import type { NewUser } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:guest'],
});

const client = useSanctumClient();

useSeoMeta({
    title: "Registrácia firmy | ISOP",
    ogTitle: "Registrácia firmy",
    description: "Registrácia firmy ISOP",
    ogDescription: "Registrácia firmy",
});

const rules = {
    required: (v: any) => (!!v && String(v).trim().length > 0) || 'Povinné pole',
    email: (v: string) => /.+@.+\..+/.test(v) || 'Zadajte platný email',
    phone: (v: string) =>
        (!v || /^\+[0-9]{6,13}$/.test(v)) || 'Zadajte platné telefónne číslo. Príklad: +421908123456',
    mustAgree: (v: boolean) => v === true || 'Je potrebné súhlasiť',
};

const isValid = ref(false);
const form = ref({
    name: '',
    address: '',
    ico: 123456789,
    email: '',
    first_name: '',
    last_name: '',
    phone: '',
    consent: false,
});

const loading = ref(false);
const error = ref(null as null | string);

async function handleRegistration() {
    error.value = null;
    loading.value = true;

    const newUser: NewUser = {
        email: form.value.email,
        first_name: form.value.first_name,
        last_name: form.value.last_name,
        phone: form.value.phone,
        role: NewRole.EMPLOYER,
        student_data: undefined,
        company_data: {
            name: form.value.name,
            address: form.value.address,
            ico: form.value.ico,
            hiring: false
        },
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
</script>

<template>
    <v-container fluid class="page-container form-wrap">
        <v-card id="page-container-card">
            <h4 class="page-title">Registrácia firmy</h4>

            <!-- Chybová hláška -->
            <ErrorAlert v-if=error :error="error" />

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="loading" />

            <v-form v-else v-model="isValid" @submit.prevent="handleRegistration">
                <v-text-field v-model="form.name" :rules="[rules.required]" label="Názov firmy:" variant="outlined"
                    density="comfortable" />
                <v-text-field v-model="form.address" :rules="[rules.required]" label="Adresa:" variant="outlined"
                    density="comfortable" />
                <v-number-input v-model="form.ico" :rules="[rules.required]" label="IČO:"
                    control-variant="hidden"></v-number-input>

                <h4 class="section-heading mt-4">Kontaktná osoba</h4>

                <v-text-field v-model="form.first_name" :rules="[rules.required]" label="Meno:" variant="outlined"
                    density="comfortable" />
                <v-text-field v-model="form.last_name" :rules="[rules.required]" label="Priezvisko:" variant="outlined"
                    density="comfortable" />
                <v-text-field v-model="form.email" :rules="[rules.required, rules.email]" label="Email:"
                    variant="outlined" density="comfortable" />
                <v-text-field v-model="form.phone" :rules="[rules.phone]" label="Telefón (s predvoľbou):"
                    variant="outlined" density="comfortable" />

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

.form-wrap {
    max-width: 640px;
}

.page-title {
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700;
    margin: 24px 0 16px;
    color: #1f1f1f;
}

.section-heading {
    font-size: 18px;
    font-weight: 700;
    margin: 8px 0 8px;
    color: #1f1f1f;
}

.mb-2 {
    margin-bottom: 8px;
}

.mb-3 {
    margin-bottom: 12px;
}

.mt-4 {
    margin-top: 16px;
}
</style>
