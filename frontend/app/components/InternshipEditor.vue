<script setup lang="ts">
import type { CompanyData } from '~/types/company_data';
import type { NewInternship } from '~/types/internships';
import type { User } from '~/types/user';

const rules = {
    required: (v: any) => (!!v && String(v).trim().length > 0) || 'Povinné pole',
    mustAgree: (v: boolean) => v === true || 'Je potrebné potvrdiť',
};

const year_of_study_choices = [
    {
        title: '1',
        subtitle: 'bakalárske',
    },
    {
        title: '2',
        subtitle: 'bakalárske',
    },
    {
        title: '3',
        subtitle: 'bakalárske',
    },
    {
        title: '4',
        subtitle: 'magisterské',
    },
    {
        title: '5',
        subtitle: 'magisterské',
    }
];

const props = defineProps({
    start: {
        type: String,
        required: false,
        default: ''
    },
    end: {
        type: String,
        required: false,
        default: ''
    },
    year_of_study: {
        type: Number,
        required: false,
        default: 1
    },
    semester: {
        type: String,
        required: false,
        default: ''
    },
    company_id: {
        type: Number,
        required: false,
        default: -1
    },
    description: {
        type: String,
        required: false,
        default: ''
    },
    submit: {
        type: Function as PropType<(internship: NewInternship) => void>,
        required: true
    }
});

const isValid = ref(false);
const form = ref({
    start: props.start,
    end: props.end,
    year_of_study: props.year_of_study,
    semester: props.semester,
    company_id: props.company_id === -1 ? null : props.company_id,
    description: props.description,
    consent: false,
});

const user = useSanctumUser<User>();

function triggerSubmit() {
    const new_internship: NewInternship = {
        user_id: user.value?.id!,
        company_id: form.value.company_id!,
        start: form.value.start,
        end: form.value.end,
        year_of_study: form.value.year_of_study,
        semester: form.value.semester === "Zimný" ? "WINTER" : "SUMMER",
        position_description: form.value.description
    }

    props.submit(new_internship);
}

const { data, error } = await useSanctumFetch<CompanyData[]>('/api/companies/simple');
</script>

<template>
    <v-form v-model="isValid" @submit.prevent="triggerSubmit">
        <v-date-input v-model="form.start" :rules="[rules.required]" clearable label="Začiatok"></v-date-input>
        <v-date-input v-model="form.end" :rules="[rules.required]" clearable label="Koniec"></v-date-input>

        <v-select v-model="form.year_of_study" clearable label="Ročník" :items="year_of_study_choices"
            :item-props="(item) => { return { title: item.title, subtitle: item.subtitle } }"
            :rules="[rules.required]"></v-select>

        <v-select v-model="form.semester" clearable label="Semester" :items="['Zimný', 'Letný']"
            :rules="[rules.required]"></v-select>

        <v-select v-model="form.company_id" clearable label="Firma" :items="[{ title: 'a', subtitle: 'b', value: 0 }]"
            :item-props="(item) => { return { title: item.title, subtitle: item.subtitle } }"
            :rules="[rules.required]"></v-select>

        <v-textarea clearable label="Popis práce" :rules="[rules.required]"></v-textarea>

        <v-checkbox v-model="form.consent" :rules="[rules.mustAgree]" label="Potvrdzujem, že zadané údaje sú pravdivé"
            density="comfortable" />

        <v-btn type="submit" color="success" size="large" block :disabled="!isValid || !form.consent">
            Pridať
        </v-btn>
    </v-form>
</template>

<style scoped>
form {
    width: 80%;
    margin: 0 auto;
}
</style>
