<script setup lang="ts">
import { FetchError } from 'ofetch';

const props = defineProps<{
    internship_id: number
}>();

const client = useSanctumClient();

const loading = ref(false);

async function requestDownload() {
    loading.value = true;

    try {
        const proof = await client<Blob>(`/api/internships/${props.internship_id}/default-proof`);
        triggerDownload(proof, `default-proof-${props.internship_id}`);
    } catch (e) {
        if (e instanceof FetchError) {
            alert(`Nepodarilo sa vygenerovať zmluvu: ${e.statusMessage}`);
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div>
        <v-btn prepend-icon="mdi-download" color="blue" class="mr-2 mt-2" block :disabled="loading"
            @click="requestDownload">
            <span v-show="!loading">Stiahnuť originálnu zmluvu</span>
            <span v-show="loading">Prosím čakajte...</span>
        </v-btn>
    </div>
</template>

<style scoped></style>
