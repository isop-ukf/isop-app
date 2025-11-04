<script setup lang="ts">
import type { CompanyData } from '~/types/company_data';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin-only']
})

const route = useRoute();
const client = useSanctumClient();
const companyId = route.params.id;

const loading = ref(true);
const saving = ref(false);

// Delete state
const deleteDialog = ref(false);
const deleteLoading = ref(false);
const deleteError = ref<string | null>(null);
const deleteSuccess = ref(false);
const company = ref<CompanyData | null>(null);

const form = ref({
    name: '',
    address: '',
    ico: 0,
    hiring: false,
    contact: {
        first_name: '',
        last_name: '',
        email: '',
        phone: ''
    }
});

// Načítanie dát firmy
const { data } = await useSanctumFetch<CompanyData>(`/api/companies/${companyId}`);

watch(data, (newData) => {
    if (newData) {
        company.value = newData;
        form.value.name = newData.name;
        form.value.address = newData.address;
        form.value.ico = newData.ico;
        form.value.hiring = !!newData.hiring;
        form.value.contact.first_name = newData.contact?.first_name;
        form.value.contact.last_name = newData.contact?.last_name;
        form.value.contact.email = newData.contact?.email;
        form.value.contact.phone = newData.contact?.phone;
        loading.value = false;
    }
}, { immediate: true });

// Uloženie zmien
async function saveChanges() {
    saving.value = true;
    try {
        await client(`/api/companies/${companyId}`, {
            method: 'POST',
            body: form.value
        });

        alert('Údaje firmy boli úspešne aktualizované');
        navigateTo("/dashboard/admin/companies");
    } catch (e) {
        if (e instanceof FetchError) {
            console.error('Error saving company:', e.response?._data.message);
            alert('Chyba:\n' + e.response?._data.message);
        }
    } finally {
        saving.value = false;
    }
}

function cancel() {
    navigateTo('/dashboard/admin/companies');
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

// Funkcia na vymazanie firmy
const deleteCompany = async () => {
    if (!companyId) return;

    deleteLoading.value = true;
    deleteError.value = null;

    try {
        await client(`/api/companies/${companyId}`, {
            method: 'DELETE'
        });

        deleteSuccess.value = true;

        // Presmeruj na zoznam po 1.5 sekundách
        setTimeout(() => {
            navigateTo('/dashboard/admin/companies');
        }, 1500);

    } catch (e) {
        if (e instanceof FetchError) {
            deleteError.value = e.response?._data?.message || 'Chyba pri mazaní firmy.';
        } else {
            deleteError.value = 'Neznáma chyba pri mazaní firmy.';
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
                    <h1>Editovať firmu</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-title>Údaje firmy</v-card-title>
                        <v-card-text>
                            <v-form>
                                <v-text-field v-model="form.name" label="Názov firmy" required variant="outlined"
                                    class="mb-3"></v-text-field>

                                <v-textarea v-model="form.address" label="Adresa" required variant="outlined" rows="3"
                                    class="mb-3"></v-textarea>

                                <v-text-field v-model.number="form.ico" label="IČO" type="number" required
                                    variant="outlined" class="mb-3"></v-text-field>

                                <v-checkbox v-model="form.hiring" label="Prijíma študentov na prax"
                                    class="mb-3"></v-checkbox>

                                <v-divider class="my-4"></v-divider>

                                <h3 class="mb-3">Kontaktná osoba</h3>

                                <v-text-field v-model="form.contact.first_name" label="Meno" required variant="outlined"
                                    class="mb-3"></v-text-field>

                                <v-text-field v-model="form.contact.last_name" label="Priezvisko" required
                                    variant="outlined" class="mb-3"></v-text-field>

                                <v-text-field v-model="form.contact.email" label="E-mail" type="email" required
                                    variant="outlined" class="mb-3"></v-text-field>

                                <v-text-field v-model="form.contact.phone" label="Telefón"
                                    variant="outlined"></v-text-field>
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
                                Vymazať firmu
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
                        Naozaj chcete vymazať firmu <strong>{{ company?.name }}</strong>?
                    </p>
                    <p v-if="!deleteSuccess" class="text-error mt-2">
                        Táto akcia vymaže aj kontaktnú osobu (EMPLOYER), všetky praxe a statusy spojené s touto firmou a
                        <strong>nie je možné ju vrátiť späť</strong>.
                    </p>

                    <!-- Error message -->
                    <v-alert v-if="deleteError" type="error" density="compact" class="mt-3">
                        {{ deleteError }}
                    </v-alert>

                    <!-- Success message -->
                    <v-alert v-if="deleteSuccess" type="success" density="compact" class="mt-3">
                        Firma bola úspešne vymazaná. Presmerovanie...
                    </v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" variant="text" @click="closeDeleteDialog" :disabled="deleteLoading">
                        Zrušiť
                    </v-btn>
                    <v-btn color="red" variant="text" @click="deleteCompany" :loading="deleteLoading"
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
