<template>
  <div class="page-container form-wrap">
    <h2 class="page-title">Prihlásenie</h2>

    <v-form v-model="isValid" @submit.prevent="onSubmit">
      <v-text-field
        v-model="form.email"
        :rules="[rules.required, rules.email]"
        label="Email:"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        v-model="form.password"
        :rules="[rules.required]"
        :type="showPassword ? 'text' : 'password'"
        label="Heslo:"
        variant="outlined"
        density="comfortable"
        :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        @click:append-inner="showPassword = !showPassword"
      />

      <div class="actions-row">
        <v-spacer />
       <v-btn
          type="button"              
          variant="tonal"
          color="success"
          size="small"
          class="forgot-btn"
          dense to="/reset_psw"      
        >
          Zabudnuté heslo...
        </v-btn>
      </div>

      <v-btn
        type="submit"
        color="success"
        size="large"
        block
        :disabled="!isValid"
      >
        Prihlásiť
      </v-btn>
    </v-form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

const isValid = ref(false)
const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
})

const rules = {
  required: v => (!!v && String(v).trim().length > 0) || 'Povinné pole',
  email: v => /.+@.+\..+/.test(v) || 'Zadajte platný email',
}

function onSubmit() {

}

function onForgot() {

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

.actions-row {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}
.forgot-btn {
  text-transform: none;
  font-weight: 600;
  border-radius: 999px;
}

.mb-1 { margin-bottom: 6px; }
.mb-2 { margin-bottom: 10px; }
</style>

