<script setup lang="ts">
const props = defineProps<{
    company: number;
    current_value: boolean;
}>();

const client = useSanctumClient();

const loading = ref(false);
const value = ref(props.current_value);

async function updateVerifiedStatus() {
    loading.value = true;

    try {
        await client(`/api/companies/${props.company}/verification`, {
            method: 'PUT',
            body: {
                status: value.value
            }
        });
    } catch (e) {
        alert(`Chyba: ${simplifyApiError(e)}`);
        value.value = !value.value;
    }

    loading.value = false;
}
</script>

<template>
    <div>
        <v-switch v-model="value" :loading="loading" color="green" base-color="red"
            @update:model-value="updateVerifiedStatus" hide-details>
        </v-switch>
    </div>
</template>