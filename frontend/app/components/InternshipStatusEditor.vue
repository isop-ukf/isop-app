<script setup lang="ts">
import type { Internship } from '~/types/internships';
import { InternshipStatus, prettyInternshipStatus, type NewInternshipStatusData } from '~/types/internship_status';
import type { User } from '~/types/user';
import { FetchError } from 'ofetch';

const props = defineProps<{
    internship: Internship
}>();
const emit = defineEmits(['successfulSubmit']);

const user = useSanctumUser<User>();

const rules = {
    required: (v: any) => (!!v && String(v).trim().length > 0) || 'Povinné pole',
};

const isValid = ref(false);
const new_state = ref(null as InternshipStatus | null);
const note = ref("");

const loading = ref(false);
const save_error = ref(null as null | string);

const client = useSanctumClient();
const { data, error: load_error, refresh } = await useSanctumFetch(`/api/internships/${props.internship.id}/next-statuses`, undefined, {
    transform: (statuses: InternshipStatus[]) => statuses.map((state) => ({
        title: prettyInternshipStatus(state),
        value: state
    }))
});

async function submit() {
    save_error.value = null;
    loading.value = true;

    const new_status: NewInternshipStatusData = {
        status: new_state.value!,
        note: note.value,
    };

    try {
        await client(`/api/internships/${props.internship.id}/status`, {
            method: 'PUT',
            body: new_status
        });

        new_state.value = null;
        note.value = "";
        refresh();
        emit('successfulSubmit');
    } catch (e) {
        if (e instanceof FetchError) {
            save_error.value = e.response?._data.message;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div>
        <!-- Chybová hláška -->
        <ErrorAlert v-if="save_error" :error="`Nepodarilo uložiť: ${save_error}`" />

        <!-- Chybová hláška -->
        <ErrorAlert v-if="load_error" :error="`Nepodarilo sa načítať stavy: ${save_error}`" />

        <v-form v-else v-model="isValid" @submit.prevent="submit" :disabled="loading">
            <v-select v-model="new_state" label="Stav" :items="data" item-value="value"></v-select>
            <v-text-field v-model="note" :rules="[rules.required]" label="Poznámka"></v-text-field>

            <v-btn type="submit" color="success" size="large" block :disabled="!isValid">
                Uloziť
            </v-btn>
        </v-form>
    </div>
</template>

<style scoped>
.alert {
    margin-bottom: 10px;
}
</style>