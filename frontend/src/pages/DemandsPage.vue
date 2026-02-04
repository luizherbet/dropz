<template>
  <q-page class="max-width-custom q-pa-md">
    <div class="row q-mb-md items-center">
      <q-input
        v-model="search"
        outlined
        dense
        placeholder="Buscar demandas..."
        class="col-grow q-mr-md"
        clearable
      />
      <q-btn
        color="primary"
        label="Criar nova demanda"
        icon="add"
        @click="onNewDemand"
      />
    </div>

    <q-table
      :rows="rowsFiltrados"
      :columns="columns"
      row-key="id"
      flat
      bordered
      :loading="loading"
      no-data-label="Nenhuma demanda cadastrada"
      :rows-per-page-options="[5, 10, 20]"
      @row-click="onRowClick"
    />

    <!-- Modal cadastro de demanda -->
    <q-dialog v-model="showDialog" persistent>
      <q-card class="demand-modal-card">
        <q-card-section class="q-pt-md q-gutter-y-md">
          <q-form @submit="onSubmit" @reset="onReset">
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Dados gerais</div>
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <q-select
                  v-model="form.cliente_id"
                  label="Cliente *"
                  outlined
                  dense
                  :options="clientOptions"
                  option-value="id"
                  option-label="nome"
                  emit-value
                  map-options
                  :rules="[val => val != null && val !== '' || 'Selecione o cliente']"
                  :disable="!!form.id"
                />
              </div>
              <div class="col-12">
                <q-input
                  v-model="form.titulo"
                  label="Título *"
                  outlined
                  dense
                  :rules="[val => !!val || 'Título é obrigatório']"
                />
              </div>
              <div class="col-6">
                <q-select
                  v-model="form.prioridade"
                  label="Prioridade"
                  outlined
                  dense
                  :options="['Alta', 'Normal', 'Baixa']"
                />
              </div>
              <div class="col-6">
                <q-input v-model="form.setor" label="Setor" outlined dense />
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-subtitle2 text-grey-8 q-mb-sm">Equipe</div>
            <div class="row q-col-gutter-md">
              <div class="col-6">
                <q-input v-model="form.responsavel" label="Responsável" outlined dense />
              </div>
              <div class="col-6">
                <q-input v-model="form.quem_deve_testar" label="Quem deve testar" outlined dense />
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-subtitle2 text-grey-8 q-mb-sm">Descrição e mídia</div>
            <q-input :input-style="{ resize: 'none' }"
              v-model="form.descricao_detalhada"
              label="Descrição detalhada"
              outlined
              dense
              type="textarea"
              rows="3"
              placeholder="Detalhes da demanda..."
              class="q-mb-md"
            />
            <q-input :input-style="{ resize: 'none' }"
              v-model="form.midia"
              label="Mídia / links"
              outlined
              dense
              type="textarea"
              rows="3"
              placeholder="Links de anexos, imagens, etc."
              class="q-mb-md"
            />

            <q-separator class="q-my-md" />

            <div class="valores-tempo">
              <div class="text-subtitle2 text-grey-8 q-mb-sm">Valores e tempo</div>
              <div class="q-mb-sm">
                <span
                  :class="form.cobrada_do_cliente ? 'text-green' : 'text-red'"
                  class="text-body2"
                >
                  {{ form.cobrada_do_cliente ? 'Demanda cobrada' : 'Demanda com valor a cobrar' }}
                </span>
              </div>
              <div class="row q-col-gutter-md">
                <div class="col-6">
                  <q-input v-model.number="form.valor_total" label="Valor total" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.valor_pago" label="Valor pago" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.tempo_estimado" label="Tempo estimado (horas)" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.tempo_gasto" label="Tempo gasto (horas)" outlined dense type="number" readonly />
                  <q-tooltip>
                    O tempo gasto será calculado quando a demanda for concluída
                  </q-tooltip>
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-subtitle2 text-grey-8 q-mb-sm" >Feedback</div>
            <q-input :input-style="{ resize: 'none' }"
              v-model="form.feedback"
              label="Feedback"
              outlined
              dense
              type="textarea"
              rows="3"
              placeholder="Preenchido na etapa Teste (quem testa)"
            />

            <q-card-actions align="right" class="q-mt-md">
              <q-btn label="Cancelar" flat color="red" v-close-popup />
              <q-btn label="Salvar" type="submit" color="primary" />
            </q-card-actions>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { getClients, getDemands, createDemand, updateDemand } from '../services/api'

const $q = useQuasar()
const search = ref('')
const loading = ref(false)
const submitting = ref(false)
const demands = ref([])
const clientOptions = ref([])

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'titulo', label: 'Título', field: 'titulo', align: 'left', sortable: true },
  { name: 'cliente', label: 'Cliente', field: row => row.client?.nome ?? row.cliente_id, align: 'left' },
  { name: 'prioridade', label: 'Prioridade', field: 'prioridade', align: 'left', sortable: true },
  { name: 'status', label: 'Status', field: 'status', align: 'left', sortable: true },
  { name: 'responsavel', label: 'Responsável', field: 'responsavel', align: 'left' },
]

const showDialog = ref(false)
const form = ref({
  id: null,
  cliente_id: null,
  titulo: '',
  prioridade: 'Normal',
  setor: '',
  responsavel: '',
  quem_deve_testar: '',
  descricao_detalhada: '',
  midia: '',
  cobrada_do_cliente: false,
  valor_total: null,
  valor_pago: null,
  tempo_estimado: null,
  tempo_gasto: null,
  feedback: '',
})

onMounted(async () => {
  loading.value = true
  try {
    const [clientsData, demandsData] = await Promise.all([
      getClients().catch(() => []),
      getDemands().catch((err) => (err.response?.status === 404 ? [] : Promise.reject(err))),
    ])
    clientOptions.value = Array.isArray(clientsData) ? clientsData : []
    demands.value = Array.isArray(demandsData) ? demandsData : []
  } catch (err) {
    demands.value = []
    $q.notify({ type: 'negative', message: err.response?.data?.message || 'Erro ao carregar demandas.', position: 'top' })
  } finally {
    loading.value = false
  }
})

function onRowClick(evt, row) {
  form.value = {
    id: row.id,
    cliente_id: row.cliente_id ?? null,
    titulo: row.titulo ?? '',
    prioridade: row.prioridade ?? 'Normal',
    setor: row.setor ?? '',
    responsavel: row.responsavel ?? '',
    quem_deve_testar: row.quem_deve_testar ?? '',
    descricao_detalhada: row.descricao_detalhada ?? '',
    midia: row.midia ?? '',
    cobrada_do_cliente: row.cobrada_do_cliente ?? false,
    valor_total: row.valor_total ?? null,
    valor_pago: row.valor_pago ?? null,
    tempo_estimado: row.tempo_estimado ?? null,
    tempo_gasto: row.tempo_gasto ?? null,
    feedback: row.feedback ?? '',
  }
  showDialog.value = true
}

function onNewDemand() {
  onReset()
  showDialog.value = true
}

function onReset() {
  form.value = {
    id: null,
    cliente_id: null,
    titulo: '',
    prioridade: 'Normal',
    setor: '',
    responsavel: '',
    quem_deve_testar: '',
    descricao_detalhada: '',
    midia: '',
    cobrada_do_cliente: false,
    valor_total: null,
    valor_pago: null,
    tempo_estimado: null,
    tempo_gasto: null,
    feedback: '',
  }
}

function buildPayload() {
  return {
    titulo: form.value.titulo,
    prioridade: form.value.prioridade || 'Normal',
    setor: form.value.setor ?? '',
    responsavel: form.value.responsavel ?? '',
    quem_deve_testar: form.value.quem_deve_testar ?? '',
    descricao_detalhada: form.value.descricao_detalhada ?? '',
    midia: form.value.midia ?? '',
    cobrada_do_cliente: !!form.value.cobrada_do_cliente,
    valor_total: form.value.valor_total ?? null,
    valor_pago: form.value.valor_pago ?? null,
    tempo_estimado: form.value.tempo_estimado ?? null,
    tempo_gasto: form.value.tempo_gasto ?? null,
    feedback: form.value.feedback ?? '',
  }
}

async function onSubmit() {
  submitting.value = true
  try {
    if (form.value.id != null) {
      const payload = buildPayload()
      const updated = await updateDemand(form.value.id, payload)
      const idx = demands.value.findIndex(d => d.id === form.value.id)
      if (idx !== -1) demands.value[idx] = { ...demands.value[idx], ...updated }
      $q.notify({ type: 'positive', message: 'Demanda atualizada.', position: 'top' })
    } else {
      const payload = { ...buildPayload(), cliente_id: form.value.cliente_id }
      if (payload.cliente_id == null || payload.cliente_id === '') {
        $q.notify({ type: 'negative', message: 'Selecione o cliente.', position: 'top' })
        submitting.value = false
        return
      }
      const created = await createDemand(payload)
      demands.value.push(created)
      $q.notify({ type: 'positive', message: 'Demanda criada.', position: 'top' })
    }
    showDialog.value = false
    onReset()
  } catch (err) {
    const msg = err.response?.data?.message || 'Erro ao salvar.'
    const errors = err.response?.data?.errors
    if (errors) {
      const first = Object.values(errors).flat()[0]
      $q.notify({ type: 'negative', message: first || msg, position: 'top' })
    } else {
      $q.notify({ type: 'negative', message: msg, position: 'top' })
    }
  } finally {
    submitting.value = false
  }
}

const rowsFiltrados = computed(() => {
  const termo = (search.value || '').trim().toLowerCase()
  if (!termo) return demands.value
  return demands.value.filter((demand) => {
    const titulo = (demand.titulo || '').toLowerCase()
    const cliente = (demand.client?.nome || d.cliente?.nome || '').toLowerCase()
    const responsavel = (demand.responsavel || '').toLowerCase()
    const status = (demand.status || '').toLowerCase()
    return (
      titulo.includes(termo) ||
      cliente.includes(termo) ||
      responsavel.includes(termo) ||
      status.includes(termo)
    )
  })
})
</script>

<style scoped>
.max-width-custom {
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.demand-modal-card {
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
}

.valores-tempo :deep(input[type="number"]::-webkit-outer-spin-button),
.valores-tempo :deep(input[type="number"]::-webkit-inner-spin-button) {
  -webkit-appearance: none;
  margin: 0;
}

.valores-tempo :deep(input[type="number"]) {
  -moz-appearance: textfield;
  appearance: textfield;
}
</style>
