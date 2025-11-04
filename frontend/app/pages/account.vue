<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Zmena hesla | ISOP",
    ogTitle: "Zmena hesla",
    description: "Zmena hesla študenta",
    ogDescription: "Zmena hesla študenta",
});

const client = useSanctumClient();

// Form state
const form = ref({
    password: '',
    password_confirmation: ''
});

const loading = ref(false);
const error = ref<string | null>(null);
const success = ref(false);

// Validácie
const passwordsMatch = computed(() => {
    if (!form.value.password || !form.value.password_confirmation) return true;
    return form.value.password === form.value.password_confirmation;
});

const passwordLengthValid = computed(() => {
    return form.value.password.length >= 8 || form.value.password.length === 0;
});

const canSubmit = computed(() => {
    return passwordsMatch.value &&
        passwordLengthValid.value &&
        form.value.password.length >= 8 &&
        form.value.password_confirmation.length >= 8;
});

// Funkcia na zmenu hesla
const changePassword = async () => {
    error.value = null;
    loading.value = true;

    try {
        await client('/api/students/change-password', {
            method: 'POST',
            body: form.value
        });

        success.value = true;

        // Vyčisti formulár
        form.value.password = '';
        form.value.password_confirmation = '';

        // Reset success alert po 5s
        setTimeout(() => {
            success.value = false;
        }, 5000);

    } catch (e: any) {
        error.value = e.data?.message || 'Chyba pri zmene hesla.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <v-container>
        <v-card class="mx-auto" max-width="600">
            <v-card-title>
                <h1>Zmena hesla</h1>
            </v-card-title>

            <v-card-text>
                <v-form @submit.prevent="changePassword">
                    <!-- Nové heslo -->
                    <v-text-field v-model="form.password" label="Nové heslo" type="password" variant="outlined"
                        class="mb-3" hint="Minimálne 8 znakov" persistent-hint :error="!passwordLengthValid"
                        :error-messages="!passwordLengthValid ? 'Heslo musí mať aspoň 8 znakov' : ''"></v-text-field>

                    <!-- Potvrdenie hesla -->
                    <v-text-field v-model="form.password_confirmation" label="Potvrďte nové heslo" type="password"
                        variant="outlined" class="mb-3" :error="!passwordsMatch"
                        :error-messages="!passwordsMatch ? 'Heslá sa nezhodujú' : ''"></v-text-field>

                    <!-- Error alert -->
                    <v-alert v-if="error" type="error" density="compact" class="mb-3">
                        {{ error }}
                    </v-alert>

                    <!-- Success alert -->
                    <v-alert v-if="success" type="success" density="compact" class="mb-3">
                        Heslo bolo úspešne zmenené.
                    </v-alert>

                    <!-- Submit button -->
                    <v-btn type="submit" color="primary" :loading="loading" :disabled="!canSubmit || success" block>
                        Zmeniť heslo
                    </v-btn>
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-btn color="grey" variant="text" to="/dashboard/student">
                    Späť na dashboard
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>