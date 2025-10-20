<template>
  <div class="page-container form-wrap">
    <h2 class="page-title">Reset hesla</h2>

    <v-form v-model="isValid" @submit.prevent="onSubmit">
      <v-text-field
        v-model="email"
        :rules="[rules.required, rules.email]"
        label="Email:"
        variant="outlined"
        density="comfortable"
      />

      <v-btn
        type="submit"
        color="success"
        size="large"
        block
        :disabled="!isValid"
      >
        Odoslať Email
      </v-btn>
    </v-form>

    <v-snackbar v-model="snackbar" timeout="2500">
      Odoslané na mail
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const isValid = ref(false)
const email = ref('')
const snackbar = ref(false)

const rules = {
  required: v => (!!v && String(v).trim().length > 0) || 'Povinné pole',
  email: v => /.+@.+\..+/.test(v) || 'Zadajte platný email',
}

function onSubmit() {
  snackbar.value = true
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
  max-width: 560px;
}
.page-title {
  font-size: 24px;
  line-height: 1.2;
  font-weight: 700;
  margin: 24px 0 16px;
  color: #1f1f1f;
}
.mb-3 { margin-bottom: 12px; }
</style>


