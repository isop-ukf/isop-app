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

const form = ref({
    name: '',
    address: '',
    ico: 0,
    hiring: false,
    contact: {
        name: '',
        email: '',
        phone: ''
    }
});

// Načítanie dát firmy
const { data } = await useSanctumFetch<CompanyData>(`/api/companies/${companyId}`);

watch(data, (newData) => {
    if (newData) {
        form.value.name = newData.name;
        form.value.address = newData.address;
        form.value.ico = newData.ico;
        form.value.hiring = !!newData.hiring;
        form.value.contact.name = newData.contact?.name;
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

                                <v-text-field v-model="form.contact.name" label="Meno a priezvisko" required
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
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>
    </v-container>
</template>

<style scoped>
.h-100 {
    min-height: 100vh;
}
</style>
