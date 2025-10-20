<template>
  <div class="page-container form-wrap">
    <h4 class="page-title">Registrácia študenta</h4>
    <v-form v-model="isValid" @submit.prevent="onSubmit">
      <v-text-field
        v-model="form.title"
        label="Tituly pred:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.firstName"
        :rules="[rules.required]"
        label="Meno:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.lastName"
        :rules="[rules.required]"
        label="Priezvisko:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.address"
        label="Adresa:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.studentEmail"
        :rules="[rules.required, rules.email]"
        label="Študentský email:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.altEmail"
        :rules="[rules.optionalEmail]"
        label="Alternatívny email:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.phone"
        :rules="[rules.phone]"
        label="Telefón:"
        variant="outlined"
        density="comfortable"
      />

      <v-select
        v-model="form.studyProgram"
        :items="programs"
        :rules="[rules.required]"
        label="Študijný odbor:"
        variant="outlined"
        density="comfortable"
      />

      <v-checkbox
        v-model="form.consent"
        :rules="[rules.mustAgree]"
        label="Súhlasím s podmienkami spracúvania osobných údajov"
        density="comfortable"
      />

      <v-btn
        type="submit"
        color="success"
        size="large"
        block
        :disabled="!isValid || !form.consent"
      >
        Registrovať
      </v-btn>
    </v-form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

const isValid = ref(false)

const form = reactive({
  title: '',
  firstName: '',
  lastName: '',
  address: '',
  studentEmail: '',
  altEmail: '',
  phone: '',
  studyProgram: null,
  consent: false,
})

const programs = [
  'Aplikovaná informatika',
]

const rules = {
  required: v => (!!v && String(v).trim().length > 0) || 'Povinné pole',
  email: v =>
    /.+@.+\..+/.test(v) || 'Zadajte platný email',
  optionalEmail: v =>
    (!v || /.+@.+\..+/.test(v)) || 'Zadajte platný email',
  phone: v =>
    (!v || /^[0-9 +()-]{6,}$/.test(v)) || 'Zadajte platné telefónne číslo',
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
.page-title {
  font-size: 24px;
  line-height: 1.2;
  font-weight: 700;
  margin: 24px 0 16px;
  color: #1f1f1f;
}
.form-wrap {
  max-width: 640px; 
}
.mb-2 { margin-bottom: 8px; }
.mb-3 { margin-bottom: 12px; }
.mb-4 { margin-bottom: 16px; }
</style>

