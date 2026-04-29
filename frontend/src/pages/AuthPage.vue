<template>
  <div class="auth-page flex flex-center">
    <q-card class="auth-card q-pa-xl">
      <q-card-section class="text-center">
        <img src="../assets/logo.png" alt="Dropz" class="auth-logo" />
        <div class="text-h5 text-weight-bold q-mt-md">Entrar no Dropz</div>
        <div class="text-grey-7 q-mt-sm">Acesse sua conta para continuar</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit.prevent="onAuthLogin" class="q-gutter-md">
          <q-input
            v-model="form.email"
            type="email"
            label="E-mail"
            outlined
            dense
          />

          <q-input
            v-model="form.password"
            type="password"
            label="Senha"
            outlined
            dense
          />

          <q-btn
            type="submit"
            color="primary"
            label="Entrar"
            class="full-width"
            unelevated
          />
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>
<script setup>
import {authLogin} from "src/services/api.js";
import {ref} from "vue";
import router from "src/router/index.js";
const form = ref({
  email: '',
  password: '',
})
async function onAuthLogin(){
const payload = {
  email: form.value.email,
  password: form.value.password,
}
try{
  const data = await authLogin(payload)
  console.log(data)
  localStorage.setItem('dropz_token', data.access_token)
  localStorage.setItem('dropz_user', JSON.stringify(data.user))
  router.push('/kanban')
}catch (err){
  console.log(err)
}
}
</script>
<style scoped>
.auth-page {
  min-height: 100vh;
  background: linear-gradient(180deg, #f5f7fb 0%, #eef3ff 100%);
}

.auth-card {
  width: 100%;
  max-width: 420px;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.auth-logo {
  width: 72px;
  height: auto;
}
</style>
