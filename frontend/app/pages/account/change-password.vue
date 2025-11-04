<script setup lang="ts">
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth'],
});

useSeoMeta({
    title: "Zmena hesla | ISOP",
    ogTitle: "Zmena hesla",
    description: "Zmena hesla študenta",
    ogDescription: "Zmena hesla študenta",
});

const client = useSanctumClient();

const password = ref('');
const password_confirmation = ref('');

const loading = ref(false);
const error = ref<string | null>(null);
const success = ref(false);

// Funkcia na zmenu hesla
const changePassword = async () => {
    error.value = null;
    loading.value = true;

    try {
        await client('/api/account/change-password', {
            method: 'POST',
            body: {
                password: password.value,
            }
        });

        success.value = true;

        // Vyčisti formulár
        password.value = '';
        password_confirmation.value = '';
    } catch (e) {
        if (e instanceof FetchError) {
            error.value = e.data?.message;
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <h1>Zmena hesla</h1>

            <!-- Error alert -->
            <v-alert v-if="error" type="error" density="compact" class="mb-3">
                {{ error }}
            </v-alert>

            <!-- Success alert -->
            <v-alert v-else-if="success" type="success" density="compact" class="mb-3">
                Heslo bolo úspešne zmenené.
            </v-alert>

            <v-form v-else :disabled="loading" @submit.prevent="changePassword">
                <!-- Nové heslo -->
                <v-text-field v-model="password" label="Nové heslo" type="password" variant="outlined" class="mb-3"
                    hint="Minimálne 8 znakov" persistent-hint></v-text-field>

                <!-- Potvrdenie hesla -->
                <v-text-field v-model="password_confirmation" label="Potvrďte nové heslo" type="password"
                    variant="outlined" class="mb-3"></v-text-field>

                <!-- Submit button -->
                <v-btn type="submit" color="primary" :disabled="password !== password_confirmation" class="mb-2">
                    Zmeniť heslo
                </v-btn>
            </v-form>

            <!-- Zrušiť -->
            <v-btn type="submit" color="yellow" to="/dashboard" class="mb-2">
                Späť na dashboard
            </v-btn>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>