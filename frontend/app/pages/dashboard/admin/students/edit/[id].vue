<script setup lang="ts">
import type { User } from '~/types/user';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only']
})

const route = useRoute();
const client = useSanctumClient();
const studentId = route.params.id;

const student = ref<User | null>(null);
const loading = ref(true);
const saving = ref(false);

// Delete state
const deleteDialog = ref(false);
const deleteLoading = ref(false);
const deleteError = ref<string | null>(null);
const deleteSuccess = ref(false);

const form = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    student_data: {
        study_field: '',
        personal_email: '',
        address: ''
    }
});

const { data } = await useSanctumFetch<User>(`/api/students/${studentId}`);

// Načítanie dát študenta
watch(data, (newData) => {
    if (newData) {
        student.value = newData;

        form.value.first_name = newData.first_name;
        form.value.last_name = newData.last_name;
        form.value.email = newData.email;
        form.value.phone = newData.phone;
        form.value.student_data.study_field = newData.student_data!.study_field;
        form.value.student_data.personal_email = newData.student_data!.personal_email;
        form.value.student_data.address = newData.student_data!.address;

        loading.value = false;
    }
}, { immediate: true });

// Uloženie zmien
async function saveChanges() {
    saving.value = true;
    try {
        await client(`/api/students/${studentId}`, {
            method: 'POST',
            body: form.value
        });

        alert('Údaje študenta boli úspešne aktualizované');
        navigateTo("/dashboard/admin/students");
    } catch (e) {
        if (e instanceof FetchError) {
            console.error('Error saving student:', e.response?._data.message);
            alert('Chyba:\n' + e.response?._data.message);
        }
    } finally {
        saving.value = false;
    }
}

function cancel() {
    navigateTo('/dashboard/admin/students');
}

// Funkcia na otvorenie delete dialogu
const openDeleteDialog = () => {
    deleteDialog.value = true;
    deleteError.value = null;
};

// Funkcia na zatvorenie dialogu
const closeDeleteDialog = () => {
    deleteDialog.value = false;
    deleteError.value = null;
    deleteSuccess.value = false;
};

// Funkcia na vymazanie študenta
const deleteStudent = async () => {
    if (!studentId) return;

    deleteLoading.value = true;
    deleteError.value = null;

    try {
        await client(`/api/students/${studentId}`, {
            method: 'DELETE'
        });

        deleteSuccess.value = true;

        // Presmeruj na zoznam po 1.5 sekundách
        setTimeout(() => {
            navigateTo('/dashboard/admin/students');
        }, 1500);

    } catch (e) {
        if (e instanceof FetchError) {
            deleteError.value = e.response?._data?.message || 'Chyba pri mazaní študenta.';
        } else {
            deleteError.value = 'Neznáma chyba pri mazaní študenta.';
        }
    } finally {
        deleteLoading.value = false;
    }
};
</script>

<template>
    <v-container class="h-100">
        <div v-if="loading" class="text-center">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </div>

        <div v-else>
            <v-row class="mb-4">
                <v-col>
                    <h1>Editovať študenta</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-title>Základné údaje</v-card-title>
                        <v-card-text>
                            <v-form>
                                <v-text-field v-model="form.first_name" label="Meno" required variant="outlined"
                                    class="mb-3"></v-text-field>

                                <v-text-field v-model="form.last_name" label="Priezvisko" required variant="outlined"
                                    class="mb-3"></v-text-field>

                                <v-text-field v-model="form.email" label="E-mail (prihlasovací)" type="email" required
                                    variant="outlined" class="mb-3"></v-text-field>

                                <v-text-field v-model="form.phone" label="Telefón" variant="outlined"
                                    class="mb-3"></v-text-field>

                                <v-divider class="my-4"></v-divider>

                                <h3 class="mb-3">Študijné údaje</h3>

                                <v-text-field v-model="form.student_data.study_field" label="Študijný program"
                                    variant="outlined" class="mb-3"></v-text-field>

                                <v-text-field v-model="form.student_data.personal_email" label="Osobný e-mail"
                                    type="email" variant="outlined" class="mb-3"></v-text-field>

                                <v-textarea v-model="form.student_data.address" label="Adresa" variant="outlined"
                                    rows="3"></v-textarea>
                            </v-form>
                        </v-card-text>

                        <v-card-actions class="px-6 pb-4">
                            <v-btn color="primary" @click="saveChanges" :loading="saving" :disabled="saving">
                                Uložiť zmeny
                            </v-btn>
                            <v-btn @click="cancel" :disabled="saving">
                                Zrušiť
                            </v-btn>
                            <v-spacer></v-spacer>
                            <v-btn color="red" variant="outlined" @click="openDeleteDialog" :disabled="saving">
                                Vymazať študenta
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Potvrdiť vymazanie
                </v-card-title>
                <v-card-text>
                    <p v-if="!deleteSuccess">
                        Naozaj chcete vymazať študenta <strong>{{ student?.name }}</strong>?
                    </p>
                    <p v-if="!deleteSuccess" class="text-error mt-2">
                        Táto akcia vymaže aj všetky súvisiace dáta (praxe, statusy, atď.) a <strong>nie je možné ju
                            vrátiť späť</strong>.
                    </p>

                    <!-- Error message -->
                    <ErrorAlert v-if="deleteError" :error="deleteError" />

                    <!-- Success message -->
                    <SuccessAlert v-if="deleteSuccess" title="Úspech" text="Študent bol úspešne vymazaný." />
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" variant="text" @click="closeDeleteDialog" :disabled="deleteLoading">
                        Zrušiť
                    </v-btn>
                    <v-btn color="red" variant="text" @click="deleteStudent" :loading="deleteLoading"
                        :disabled="deleteSuccess">
                        Vymazať
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<style scoped>
.h-100 {
    min-height: 100vh;
}
</style>
