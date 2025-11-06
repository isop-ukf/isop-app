<script setup lang="ts">
import { FetchError } from 'ofetch';

const route = useRoute();
const client = useSanctumClient();

definePageMeta({
    middleware: ['sanctum:guest'],
});

useSeoMeta({
    title: "Aktivácia účtu | ISOP",
    ogTitle: "Aktivácia účtu",
    description: "Aktivácia účtu ISOP",
    ogDescription: "Aktivácia účtu",
});

const rules = {
    required: (v: string) => (!!v && v.trim().length > 0) || 'Povinné pole',
};

const isValid = ref(false);
const showPassword = ref(false);
const password = ref('');

const loading = ref(false);
const error = ref(null as null | string);
const success = ref(false);

async function handleLogin() {
    error.value = null;
    loading.value = true;
    success.value = false;

    try {
        await client('/api/account/activate', {
            method: 'POST',
            body: {
                token: route.params.token,
                password: password.value
            }
        });

        success.value = true;
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
            <h2 class="page-title">Aktivácia účtu</h2>

            <SuccessAlert v-show="success" title="Aktivácia ukončená" text="">
                <p>Váš účet bol úspešne aktivovaný! Prihláste sa <NuxtLink to="/login">tu</NuxtLink>.
                </p>
            </SuccessAlert>

            <div v-show="!success">
                <!-- Chybová hláška -->
                <ErrorAlert v-if="error" :error="error" />

                <!-- Čakajúca hláška -->
                <LoadingAlert v-if="loading" />

                <v-form v-else v-model="isValid" @submit.prevent="handleLogin">
                    <v-text-field v-model="password" :rules="[rules.required]"
                        :type="showPassword ? 'text' : 'password'" label="Heslo:" variant="outlined"
                        density="comfortable"
                        :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                        @click:append-inner="showPassword = !showPassword" />

                    <v-btn type="submit" color="success" size="large" block :disabled="!isValid">
                        Aktivovať
                    </v-btn>
                </v-form>
            </div>
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
