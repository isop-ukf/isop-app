<script setup lang="ts">
import { FetchError } from 'ofetch';

useSeoMeta({
    title: "Prihlásenie | ISOP",
    ogTitle: "Prihlásenie",
    description: "Prihlásenie ISOP",
    ogDescription: "Prihlásenie",
});

const rules = {
    required: (v: string) => (!!v && v.trim().length > 0) || 'Povinné pole',
    email: (v: string) => /.+@.+\..+/.test(v) || 'Zadajte platný email',
};

const isValid = ref(false);
const showPassword = ref(false);
const form = reactive({
    email: '',
    password: '',
});

const loading = ref(false);
const error = ref(null as null | string);

async function handleLogin() {
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
            <h2 class="page-title">Prihlásenie</h2>

            <!-- Chybová hláška -->
            <v-alert v-if="error !== null" density="compact" :text="error" title="Chyba" type="error"
                id="login-error-alert" class="mx-auto"></v-alert>

            <!-- Čakajúca hláška -->
            <v-alert v-if="loading" density="compact" text="Prosím čakajte..." title="Spracovávam" type="info"
                id="login-error-alert" class="mx-auto"></v-alert>

            <v-form v-else v-model="isValid" @submit.prevent="handleLogin">
                <v-text-field v-model="form.email" :rules="[rules.required, rules.email]" label="Email:"
                    variant="outlined" density="comfortable" />

                <v-text-field v-model="form.password" :rules="[rules.required]"
                    :type="showPassword ? 'text' : 'password'" label="Heslo:" variant="outlined" density="comfortable"
                    :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    @click:append-inner="showPassword = !showPassword" />

                <div class="actions-row" to="/reset_psw">
                    <v-spacer />
                    <v-btn type="button" variant="tonal" color="success" size="small" class="forgot-btn" dense
                        to="/reset_psw">
                        Zabudnuté heslo...
                    </v-btn>
                </div>

                <v-btn type="submit" color="success" size="large" block :disabled="!isValid">
                    Prihlásiť
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

.actions-row {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.forgot-btn {
    text-transform: none;
    font-weight: 600;
    border-radius: 999px;
}

.mb-1 {
    margin-bottom: 6px;
}

.mb-2 {
    margin-bottom: 10px;
}
</style>
