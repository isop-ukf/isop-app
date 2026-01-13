<script setup lang="ts">
import type { Paginated } from '~/types/pagination';
import type { CompanyData } from '~/types/company_data';

const props = defineProps<{
    adminOps?: boolean;
}>();

const page = ref(1);
const itemsPerPage = ref(15);
const totalItems = ref(0);
const deleteConfirmDialog = ref(false);
const companyToDelete = ref<CompanyData | null>(null);

// HEADERS
const allHeaders = [
    { title: "Názov", key: "name", sortable: false },
    { title: "IČO", key: "ico", sortable: false },
    { title: "Adresa", key: "address", sortable: false },
    { title: "Kontaktná osoba", key: "contact.name", sortable: false },
    { title: "E-mail", key: "contact.email", sortable: false },
    { title: "Telefón", key: "contact.phone", sortable: false },
    { title: "Prijímajú študentov", key: "hiring", sortable: false },
    { title: "Overená", key: "verified", sortable: false },
    { title: "Operácie", key: "operations", sortable: false }
];

const headers = props.adminOps !== true
    ? allHeaders.filter(h => h.key !== "operations" && h.key !== "verified")
    : allHeaders;

const client = useSanctumClient();

// FETCH dát (používame useLazySanctumFetch — NIE neexistujúcu funkciu)
const { data, error, pending, refresh } = await useLazySanctumFetch<Paginated<CompanyData>>(
    '/api/companies',
    {
        query: {
            page: page.value,
            per_page: itemsPerPage.value,
        },
        watch: [page, itemsPerPage]
    }
);

// DELETE firmy
async function deleteCompany(company: CompanyData) {
    pending.value = true;

    try {
        await client(`/api/companies/${company.id}`, { method: "DELETE" });
        await refresh();
    } catch (e) {
        alert(`Chyba pri mazaní firmy: ${simplifyApiError(e)}`);
    } finally {
        pending.value = false;
    }
}

function openDeleteDialog(company: CompanyData) {
    companyToDelete.value = company;
    deleteConfirmDialog.value = true;
}

async function confirmDeletion(confirm: boolean) {
    if (confirm && companyToDelete.value) {
        await deleteCompany(companyToDelete.value);
    }
    deleteConfirmDialog.value = false;
    companyToDelete.value = null;
}

// Nastav total items robustne: ak API vracia paginator -> total, inak použij length data[]
watch(data, (newData) => {
    totalItems.value = newData?.total ?? 0;
}, { deep: true, immediate: true });
</script>

<template>
    <div>
        <ErrorAlert v-if="error" :error="error.statusMessage ?? error.message" />

        <v-data-table-server v-model:items-per-page="itemsPerPage" v-model:page="page" :headers="headers"
            :items="data?.data" :items-length="totalItems" :loading="pending">

            <template #item.hiring="{ item }">
                {{ item.hiring ? 'Áno' : 'Nie' }}
            </template>

            <template #item.verified="{ item }" v-if="adminOps === true">
                <CompanyVerificationToggle :company="item.id" :current_value="item.verified" />
            </template>

            <template #item.operations="{ item }" v-if="adminOps === true">
                <v-tooltip text="Editovať">
                    <template #activator="{ props }">
                        <v-btn icon="mdi-pencil" size="small" variant="text" class="company-edit-btn"
                            :to="`/dashboard/admin/companies/edit/${item.id}`" />
                    </template>
                </v-tooltip>

                <v-tooltip text="Vymazať">
                    <template #activator="{ props }">
                        <v-btn icon="mdi-delete" size="small" color="error" variant="text" class="company-delete-btn"
                            @click="() => openDeleteDialog(item)" />
                    </template>
                </v-tooltip>
            </template>

        </v-data-table-server>

        <!-- Delete dialog -->
        <v-dialog v-model="deleteConfirmDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">
                    Potvrdiť vymazanie firmy
                </v-card-title>

                <v-card-text>
                    <p>Ste si istý, že chcete vymazať firmu {{ companyToDelete?.name }}?</p>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn color="red" variant="text" :loading="pending"
                        @click="async () => await confirmDeletion(true)">
                        Áno
                    </v-btn>

                    <v-btn color="black" variant="text" @click="async () => await confirmDeletion(false)">
                        Zrušiť
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
:deep(.v-data-table-header__content) {
    font-weight: bold;
}
</style>
