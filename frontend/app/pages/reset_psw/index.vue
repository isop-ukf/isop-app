<script setup lang="ts">
import { FetchError } from 'ofetch';

const client = useSanctumClient();

definePageMeta({
    middleware: ['sanctum:guest'],
});

useSeoMeta({
    title: "Reset hesla | ISOP",
    ogTitle: "Reset hesla",
    description: "Reset hesla ISOP",
    ogDescription: "Reset hesla",
});

const rules = {
    required: (v: string) => (!!v && v.trim().length > 0) || 'Povinné pole',
    email: (v: string) => /.+@.+\..+/.test(v) || 'Zadajte platný email',
};

const isValid = ref(false);
const email = ref('');

const loading = ref(false);
const error = ref(null as null | string);

async function handleReset() {
    error.value = null;
    loading.value = true;

    try {
        await client("/api/password-reset", {
            method: 'POST',
            body: {
                email: email.value
            }
        });

        navigateTo("/reset_psw/request_sent");
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
    <v-container fluid class="page-container form-wrap">
        <v-card id="page-container-card">
            <h2 class="page-title">Reset hesla</h2>

            <!-- Chybová hláška -->
            <ErrorAlert v-if="error" :error="error" />

            <!-- Čakajúca hláška -->
            <LoadingAlert v-if="loading" />

            <v-form v-else v-model="isValid" @submit.prevent="handleReset">
                <v-text-field v-model="email" :rules="[rules.required, rules.email]" label="Email:" variant="outlined"
                    density="comfortable" />

                <v-btn type="submit" color="success" size="large" block :disabled="!isValid">
                    Odoslať Email
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

.form-wrap {
    max-width: 560px;
}

.page-title {
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700;
    margin: 24px 0 16px;
    color: #1f1f1f;
}

.mb-3 {
    margin-bottom: 12px;
}
</style>
