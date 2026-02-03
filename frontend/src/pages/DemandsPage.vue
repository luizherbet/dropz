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
      @row-click="onRowClick"
      :rows-per-page-options="[0]"
    />

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
            <q-input
              v-model="form.descricao_detalhada"
              label="Descrição detalhada"
              outlined
              dense
              type="textarea"
              rows="3"
              placeholder="Detalhes da demanda..."
              class="q-mb-md"
            />
            <q-input
              v-model="form.midia"
              label="Mídia / links"
              outlined
              dense
              type="textarea"
              rows="2"
              placeholder="Links de anexos, imagens, etc."
              class="q-mb-md"
            />

            <q-separator class="q-my-md" />

            <div class="valores-tempo">
              <div class="text-subtitle2 text-grey-8 q-mb-sm">Valores e tempo</div>
              <q-checkbox v-model="form.cobrada_do_cliente" label="Cobrada do cliente" class="q-mb-sm" />
              <div class="row q-col-gutter-md">
                <div class="col-6">
                  <q-input v-model.number="form.valor_total" label="Valor total" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.valor_pago" label="Valor pago" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.tempo_estimado" label="Tempo estimado (min)" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.tempo_gasto" label="Tempo gasto (min)" outlined dense type="number" />
                </div>
              </div>
            </div>

            <q-separator class="q-my-md" />

            <div class="text-subtitle2 text-grey-8 q-mb-sm">Feedback</div>
            <q-input
              v-model="form.feedback"
              label="Feedback"
              outlined
              dense
              type="textarea"
              rows="2"
              placeholder="Preenchido na etapa Teste (quem testa)"
            />

            <q-card-actions align="right" class="q-mt-md">
              <q-btn label="Cancelar" flat color="grey" v-close-popup />
              <q-btn label="Salvar" type="submit" color="primary" />
            </q-card-actions>
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
const demands = ref([
  { id: 1, titulo: 'Ajuste layout login', prioridade: 'Alta', status: 'backlog', cliente: { nome: 'Tech Solutions Ltda' }, responsavel: 'Luiz' },
  { id: 2, titulo: 'API relatório mensal', prioridade: 'Normal', status: 'autorizacao', cliente: { nome: 'Comércio Digital' }, responsavel: 'João' },
  { id: 3, titulo: 'Correção bug checkout', prioridade: 'Alta', status: 'fila', cliente: { nome: 'Tech Solutions Ltda' }, responsavel: 'Maria' },
  { id: 4, titulo: 'Dashboard analytics', prioridade: 'Normal', status: 'desenvolvimento', cliente: { nome: 'Indústria Alpha' }, responsavel: 'Pedro' },
  { id: 5, titulo: 'Integração pagamento', prioridade: 'Alta', status: 'teste', cliente: { nome: 'Comércio Digital' }, responsavel: 'Ana' },
  { id: 6, titulo: 'Deploy homologação', prioridade: 'Baixa', status: 'deploy', cliente: { nome: 'Startup Beta' }, responsavel: 'Carlos' },
  { id: 7, titulo: 'Relatório fiscal jan/26', prioridade: 'Normal', status: 'concluido', cliente: { nome: 'Indústria Alpha' }, responsavel: 'Maria' },
])
const clientOptions = ref([
  { id: 1, nome: 'Tech Solutions Ltda' },
  { id: 2, nome: 'Comércio Digital' },
  { id: 3, nome: 'Indústria Alpha' },
  { id: 4, nome: 'Startup Beta' },
])
const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'titulo', label: 'Título', field: 'titulo', align: 'left', sortable: true },
  { name: 'cliente', label: 'Cliente', field: row => row.cliente?.nome || row.cliente_id, align: 'left' },
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

function onRowClick(evt, row) {
  const clienteId = clientOptions.value.find(c => c.nome === row.cliente?.nome)?.id ?? null
  form.value = {
    id: row.id,
    cliente_id: clienteId,
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

function onSubmit() {
  showDialog.value = false
  onReset()
}
const rowsFiltrados = computed(() => {
  const termo = (search.value || '').trim().toLowerCase()
  if (!termo) return demands.value
  return demands.value.filter((d) => {
    const titulo = (d.titulo || '').toLowerCase()
    const cliente = (d.cliente?.nome || '').toLowerCase()
    const responsavel = (d.responsavel || '').toLowerCase()
    const status = (d.status || '').toLowerCase()
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
