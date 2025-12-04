<script setup lang="ts">
import type { CompanyData } from '~/types/company_data';
import { convertDate, type Internship, type NewInternship } from '~/types/internships';
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
const semester_choices = [
    {
        title: "Zimný",
        value: "WINTER"
    },
    {
        title: "Letný",
        value: "SUMMER"
    }
];

const props = defineProps<{
    internship?: Internship,
    submit: (new_internship: NewInternship) => void,
}>();

const isValid = ref(false);
const form = ref({
    start: props.internship?.start ? convertDate(props.internship.start) : null,
    end: props.internship?.end ? convertDate(props.internship.end) : null,
    year_of_study: props.internship?.year_of_study || 2025,
    semester: props.internship?.semester || "WINTER",
    company_id: props.internship?.company?.id == undefined ? null : props.internship.company.id,
    description: props.internship?.position_description || "",
    consent: false,
});

const user = useSanctumUser<User>();

function dateTimeFixup(datetime: Date) {
    const year = datetime.getFullYear()
    const month = String(datetime.getMonth() + 1).padStart(2, '0')
    const day = String(datetime.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}`;
}

function triggerSubmit() {
    const new_internship: NewInternship = {
        user_id: user.value?.id!,
        company_id: form.value.company_id!,
        start: dateTimeFixup(form.value.start!),
        end: dateTimeFixup(form.value.end!),
        year_of_study: form.value.year_of_study,
        semester: form.value.semester,
        position_description: form.value.description
    };

    props.submit(new_internship);
}

function companyListProps(company: CompanyData) {
    return {
        title: company.name,
        subtitle: `IČO: ${company.ico}, Zodpovedný: ${company.contact.name}, ${!company.hiring ? "ne" : ""}prijímajú nových študentov`
    };
}

function yearOfStudyValueHandler(item: { title: string, subtitle: string }) {
    return parseInt(item.title) || 0;
}

const { data, pending, error } = await useLazySanctumFetch<CompanyData[]>('/api/companies/simple');
</script>

<template>
    <v-form v-model="isValid" @submit.prevent="triggerSubmit">
        <v-date-input v-model="form.start" :rules="[rules.required]" clearable label="Začiatok"></v-date-input>
        <v-date-input v-model="form.end" :rules="[rules.required]" clearable label="Koniec"></v-date-input>

        <v-select v-model="form.year_of_study" clearable label="Ročník" :items="year_of_study_choices"
            :item-props="(item) => { return { title: item.title, subtitle: item.subtitle } }"
            :item-value="yearOfStudyValueHandler" :rules="[rules.required]"></v-select>

        <v-select v-model="form.semester" clearable label="Semester" :items="semester_choices"
            :rules="[rules.required]"></v-select>

        <!-- Výber firmy -->

        <!-- Čakajúca hláška -->
        <LoadingAlert v-if="pending" />

        <!-- Chybová hláška -->
        <ErrorAlert v-else-if="error" :error="error.message" />

        <v-select v-else v-model="form.company_id" clearable label="Firma" :items="data" :item-props="companyListProps"
            item-value="id" :rules="[rules.required]"></v-select>

        <v-textarea v-model="form.description" clearable label="Popis práce" :rules="[rules.required]"></v-textarea>

        <v-checkbox v-model="form.consent" :rules="[rules.mustAgree]" label="Potvrdzujem, že zadané údaje sú pravdivé"
            density="comfortable" />

        <v-btn type="submit" color="success" size="large" block :disabled="!isValid || !form.consent">
            Uloziť
        </v-btn>
    </v-form>
</template>

<style scoped>
form {
    width: 80%;
    margin: 0 auto;
}

.alert {
    margin-bottom: 10px;
}
</style>
