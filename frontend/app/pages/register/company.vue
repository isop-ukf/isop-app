<template>
    <div class="page-container form-wrap">
        <h4 class="page-title">Registrácia firmy</h4>

        <v-form v-model="isValid" @submit.prevent="onSubmit">
            <v-text-field v-model="form.companyName" :rules="[rules.required]" label="Názov firmy:" variant="outlined"
                density="comfortable" />
            <v-text-field v-model="form.companyAddress" :rules="[rules.required]" label="Adresa:" variant="outlined"
                density="comfortable" />

            <h4 class="section-heading mt-4">Kontaktná osoba</h4>

            <v-text-field v-model="form.contactName" :rules="[rules.required]" label="Meno:" variant="outlined"
                density="comfortable" />
            <v-text-field v-model="form.contactEmail" :rules="[rules.required, rules.email]" label="Email:"
                variant="outlined" density="comfortable" />
            <v-text-field v-model="form.contactPhone" :rules="[rules.phone]" label="Telefón:" variant="outlined"
                density="comfortable" />

            <v-checkbox v-model="form.consent" :rules="[rules.mustAgree]"
                label="Súhlasím s podmienkami spracúvania osobných údajov" density="comfortable" />

            <v-btn type="submit" color="success" size="large" block :disabled="!isValid || !form.consent">
                Registrovať
            </v-btn>
        </v-form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

const isValid = ref(false)

const form = reactive({
    companyName: '',
    companyAddress: '',
    contactName: '',
    contactEmail: '',
    contactPhone: '',
    consent: false,
})

const rules = {
    required: v => (!!v && String(v).trim().length > 0) || 'Povinné pole',
    email: v => /.+@.+\..+/.test(v) || 'Zadajte platný email',
    phone: v => (!v || /^[0-9 +()-]{6,}$/.test(v)) || 'Zadajte platné telefónne číslo',
    mustAgree: v => v === true || 'Je potrebné súhlasiť',
}

function onSubmit() {

}
</script>

<style scoped>
.page-container {
    max-width: 1120px;
    margin: 0 auto;
    padding-left: 24px;
    padding-right: 24px;
}

.form-wrap {
    max-width: 640px;
}

.page-title {
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700;
    margin: 24px 0 16px;
    color: #1f1f1f;
}

.section-heading {
    font-size: 18px;
    font-weight: 700;
    margin: 8px 0 8px;
    color: #1f1f1f;
}

.mb-2 {
    margin-bottom: 8px;
}

.mb-3 {
    margin-bottom: 12px;
}

.mt-4 {
    margin-top: 16px;
}
</style>
