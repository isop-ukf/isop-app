<script setup lang="ts">
import type { Internship } from '~/types/internships';
import { InternshipStatus, possibleNextStates, prettyInternshipStatus, type NewInternshipStatusData } from '~/types/internship_status';
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
const possible_states = possibleNextStates(props.internship.status.status, user.value!.role).map((state) => ({
    title: prettyInternshipStatus(state),
    value: state
}));

const isValid = ref(false);
const new_state = ref(null as InternshipStatus | null);
const note = ref("");

const loading = ref(false);
const error = ref(null as null | string);

const route = useRoute();
const client = useSanctumClient();

async function submit() {
    error.value = null;
    loading.value = true;

    const new_status: NewInternshipStatusData = {
        status: new_state.value!,
        note: note.value,
    };

    try {
        await client(`/api/internships/${route.params.id}/status`, {
            method: 'PUT',
            body: new_status
        });

        new_state.value = null;
        note.value = "";
        emit('successfulSubmit');
    } catch (e) {
        if (e instanceof FetchError) {
            error.value = e.response?._data.message;
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div>
        <!-- Chybová hláška -->
        <v-alert v-if="error !== null" density="compact" :text="error" title="Chyba" type="error" id="login-error-alert"
            class="mx-auto alert"></v-alert>

        <v-form v-model="isValid" @submit.prevent="submit" :disabled="loading">
            <v-select v-model="new_state" label="Stav" :items="possible_states" item-value="value"></v-select>
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