<template>
  <q-page class="max-width-custom q-pa-md">
    <div class="row q-mb-md items-center">
      <q-input
        v-model="search"
        outlined
        dense
        placeholder="Buscar clientes..."
        class="col-grow q-mr-md"
        clearable
      />
      <q-btn
        color="primary"
        label="Cadastrar novo cliente"
        icon="add"
        @click="onNewClient"
      />
    </div>

    <q-table
      :rows="clients"
      :columns="columns"
      row-key="id"
      flat
      bordered
      :loading="loading"
      no-data-label="Nenhum cliente cadastrado"
    />

    <!-- Modal cadastro de cliente -->
    <q-dialog v-model="showDialog" persistent>
      <q-card class="q-pa-md" style="min-width: 400px;">
        <q-card-section>
          <div class="text-h6">Cadastrar novo cliente</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
            <q-input
              v-model="form.nome"
              label="Nome *"
              outlined
              dense
              :rules="[val => !!val || 'Nome é obrigatório']"
            />
            <q-input
              v-model="form.email"
              label="E-mail *"
              type="email"
              outlined
              dense
              :rules="[val => !!val || 'E-mail é obrigatório', val => /.+@.+\..+/.test(val) || 'E-mail inválido']"
            />
            <q-checkbox v-model="form.avisar_por_email" label="Avisar por e-mail" />
            <q-input
              v-model="form.whatsapp"
              label="WhatsApp"
              outlined
              dense
              placeholder="Número ou observação"
            />
            <q-checkbox v-model="form.avisar_por_whatsapp" label="Avisar por WhatsApp" />
            <q-input
              v-model="form.observacoes"
              label="Observações"
              outlined
              dense
              type="textarea"
              rows="3"
            />

            <div class="row justify-end q-gutter-sm">
              <q-btn label="Cancelar" flat color="grey" v-close-popup />
              <q-btn label="Salvar" type="submit" color="primary" />
            </div>

          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'

const search = ref('')
const loading = ref(false)
const clients = ref([])

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'nome', label: 'Nome', field: 'nome', align: 'left', sortable: true },
  { name: 'email', label: 'E-mail', field: 'email', align: 'left', sortable: true },
]
const showDialog = ref(false)
const form = ref({
  nome: '',
  email: '',
  avisar_por_email: true,
  whatsapp: '',
  avisar_por_whatsapp: false,
  observacoes: '',
})

function onNewClient() {
  onReset()
  showDialog.value = true
}

function onReset() {
  form.value = {
    nome: '',
    email: '',
    avisar_por_email: true,
    whatsapp: '',
    avisar_por_whatsapp: false,
    observacoes: '',
  }
}

function onSubmit() {
  showDialog.value = false
  onReset()
}

</script>

<style scoped>
.max-width-custom {
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}
</style>
