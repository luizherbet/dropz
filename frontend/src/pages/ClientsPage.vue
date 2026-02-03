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
      :rows="rowsFiltrados"
      :columns="columns"
      row-key="id"
      flat
      bordered
      :loading="loading"
      no-data-label="Nenhum cliente cadastrado"
      @row-click="onRowClick"
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
import { ref, computed } from 'vue'

const search = ref('')
const loading = ref(false)
const clients = ref([
  { id: 1, nome: 'Tech Solutions Ltda', email: 'contato@techsolutions.com', avisar_por_email: true, whatsapp: '(11) 99999-1111', avisar_por_whatsapp: true, observacoes: '' },
  { id: 2, nome: 'Comércio Digital', email: 'projetos@comerciodigital.com', avisar_por_email: true, whatsapp: '(11) 99999-2222', avisar_por_whatsapp: false, observacoes: '' },
  { id: 3, nome: 'Indústria Alpha', email: 'ti@industriaalpha.com', avisar_por_email: true, whatsapp: '', avisar_por_whatsapp: false, observacoes: 'Cliente corporativo' },
  { id: 4, nome: 'Startup Beta', email: 'ola@startupbeta.io', avisar_por_email: true, whatsapp: '(21) 98888-3333', avisar_por_whatsapp: true, observacoes: '' },
])
const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'nome', label: 'Nome', field: 'nome', align: 'left', sortable: true },
  { name: 'email', label: 'E-mail', field: 'email', align: 'left', sortable: true },
]
const showDialog = ref(false)
const form = ref({
  id: null,
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

function onRowClick(evt, row) {
  form.value = {
    id: row.id,
    nome: row.nome ?? '',
    email: row.email ?? '',
    avisar_por_email: row.avisar_por_email ?? true,
    whatsapp: row.whatsapp ?? '',
    avisar_por_whatsapp: row.avisar_por_whatsapp ?? false,
    observacoes: row.observacoes ?? '',
  }
  showDialog.value = true
}

function onReset() {
  form.value = {
    id: null,
    nome: '',
    email: '',
    avisar_por_email: true,
    whatsapp: '',
    avisar_por_whatsapp: false,
    observacoes: '',
  }
}

function atualizarCliente() {
  const idx = clients.value.findIndex(c => c.id === form.value.id)
  if (idx === -1) return
  const clienteAtual = clients.value[idx]
  clients.value[idx] = { ...clienteAtual, ...form.value }
}

function adicionarCliente() {
  let novoId = 1
  if (clients.value.length > 0) {
    const ids = clients.value.map(c => c.id)
    novoId = Math.max(...ids) + 1
  }
  const novo = {
    id: novoId,
    nome: form.value.nome,
    email: form.value.email,
    avisar_por_email: form.value.avisar_por_email,
    whatsapp: form.value.whatsapp,
    avisar_por_whatsapp: form.value.avisar_por_whatsapp,
    observacoes: form.value.observacoes,
  }
  clients.value.push(novo)
}

function onSubmit() {
  if (form.value.id != null) {
    atualizarCliente()
  } else {
    adicionarCliente()
  }
  showDialog.value = false
  onReset()
}

const rowsFiltrados = computed(() => {
  const termo = (search.value || '').trim().toLowerCase()
  if (!termo) return clients.value
  return clients.value.filter((c) => {
    const nome = (c.nome || '').toLowerCase()
    const email = (c.email || '').toLowerCase()
    return nome.includes(termo) || email.includes(termo)
  })
})
</script>

<style scoped>
.max-width-custom {
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}
</style>
