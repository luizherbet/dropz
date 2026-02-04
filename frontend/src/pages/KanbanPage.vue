<template>
  <q-page class="q-pa-md">
    <!-- Colunas e Cards -->
    <div class="kanban-container">
      <KanbanColumn title="Backlog" color="backlog" column-status="backlog" :count="backlog.length" @drop="onDrop">
        <KanbanCard
          v-for="item in backlog"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal"
        />
      </KanbanColumn>

      <KanbanColumn title="Autorização" color="autorizacao" column-status="autorizacao" :count="autorizacao.length" @drop="onDrop">
        <KanbanCard
          v-for="item in autorizacao"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal" />
      </KanbanColumn>

      <KanbanColumn title="Fila" color="fila" column-status="fila" :count="fila.length" @drop="onDrop">
        <KanbanCard
          v-for="item in fila"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal" />
      </KanbanColumn>

      <KanbanColumn title="Em desenvolvimento" color="desenvolvimento" column-status="desenvolvimento" :count="emDesenvolvimento.length" @drop="onDrop">
        <KanbanCard
          v-for="item in emDesenvolvimento"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal" />
      </KanbanColumn>

      <KanbanColumn title="Teste" color="teste" column-status="teste" :count="emTeste.length" @drop="onDrop">
        <KanbanCard
          v-for="item in emTeste"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal" />
      </KanbanColumn>

      <KanbanColumn title="Deploy" color="deploy" column-status="deploy" :count="deploy.length" @drop="onDrop">
        <KanbanCard
          v-for="item in deploy"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal" />
      </KanbanColumn>

      <KanbanColumn title="Concluído" color="concluido" column-status="concluido" :count="concluido.length" @drop="onDrop">
        <KanbanCard
          v-for="item in concluido"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.client?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retornou === true"
          @click="openCardModal" />
      </KanbanColumn>
    </div>

    <!-- Modal de aviso -->
    <q-dialog v-model="showMoveErrorModal" persistent>
      <q-card class="card-modal" style="min-width: 360px; max-width: 440px;">
        <q-card-section>
          <div class="text-h6 text-negative q-mb-md">
            <q-icon name="warning" class="q-mr-sm" />
            Não foi possível mover o card
          </div>
          <p v-if="moveErrorTitle" class="text-body2 text-grey-8 q-mb-sm">{{ moveErrorTitle }}</p>
          <p class="text-subtitle2 q-mb-xs">Verifique o que deve ser feito:</p>
          <ul class="q-pl-md text-body2 text-grey-9">
            <li v-for="(msg, i) in moveErrorMessages" :key="i" class="q-mb-xs">{{ msg }}</li>
          </ul>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Entendi" color="primary" @click="showMoveErrorModal = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Modal Demanda -->
    <q-dialog v-model="showCardModal">
      <q-card class="card-modal card-modal--full">
        <q-card-section class="q-pt-md q-gutter-y-md">
          <q-form v-if="selectedDemand" @submit.prevent="onSave" class="q-gutter-y-md">
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Dados gerais</div>
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <q-input :model-value="selectedDemand.client?.nome ?? ''" label="Cliente" outlined dense readonly />              </div>
              <div class="col-12">
                <q-input v-model="selectedDemand.titulo" label="Título *" outlined dense :rules="[val => !!val || 'Título é obrigatório']" />
              </div>
              <div class="col-6">
                <q-select
                  v-model="selectedDemand.prioridade" label="Prioridade" outlined dense :options="['Alta', 'Normal', 'Baixa']" />
              </div>
              <div class="col-6">
                <q-input v-model="selectedDemand.setor" label="Setor" outlined dense />
              </div>
            </div>
            <q-separator class="q-my-md" />
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Equipe</div>
            <div class="row q-col-gutter-md">
              <div class="col-6">
                <q-input v-model="selectedDemand.responsavel" label="Responsável" outlined dense />
              </div>
              <div class="col-6">
                <q-input v-model="selectedDemand.quem_deve_testar" label="Quem deve testar" outlined dense />
              </div>
            </div>
            <q-separator class="q-my-md" />
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Descrição e mídia</div>
            <q-input :input-style="{ resize: 'none' }"
              v-model="selectedDemand.descricao_detalhada"
              label="Descrição detalhada"
              outlined
              dense
              type="textarea"
              rows="3"
              placeholder="Detalhes da demanda..."
              class="q-mb-md"
            />
            <q-input :input-style="{ resize: 'none' }" v-model="selectedDemand.midia" label="Mídia / links" outlined dense type="textarea" rows="3" placeholder="Links de anexos..." class="q-mb-md" />
            <q-separator class="q-my-md" />
            <div class="valores-tempo">
              <div class="text-subtitle2 text-grey-8 q-mb-sm">Valores e tempo</div>
              <div class="col-12 q-mb-sm">
                <span
                  :class="selectedDemand?.cobrada_do_cliente ? 'text-green' : 'text-red'"
                  class="text-body2"
                >
                  {{ statusCobranca }}
                </span>
              </div>
              <div class="row q-col-gutter-md">
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.valor_total" label="Valor total" outlined dense type="number" />
                </div>
                <div class="col-6">
                <q-input v-model.number="selectedDemand.valor_pago" label="Valor pago" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.tempo_estimado" label="Tempo estimado (horas)" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.tempo_gasto" label="Tempo gasto (horas)" outlined dense type="number" />
                </div>
              </div>
            </div>
            <q-separator class="q-my-md" />
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Feedback</div>
            <q-input
              :input-style="{ resize: 'none' }"
              v-model="selectedDemand.feedback"
              label="Feedback"
              outlined
              dense
              type="textarea"
              rows="2"
              placeholder="Preenchido na etapa Teste"
            />
            <div class="q-mt-md text-caption text-grey-7">Status atual: {{ statusLabel(selectedDemand?.status) }}</div>
            <q-card-actions align="right" class="q-pa-none q-mt-md">
              <q-btn flat label="Fechar" color="grey" @click="closeCardModal" />
              <q-btn label="Salvar" type="submit" color="primary" :loading="saving" :disable="saving" />
              <q-btn v-if="selectedDemand && selectedDemand.status !== 'concluido'" type="button" label="Avançar" color="primary" outline @click="advanceDemand" />
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
import KanbanColumn from '../components/KanbanColumn.vue'
import KanbanCard from '../components/KanbanCard.vue'
import { getDemands, updateDemand, updateDemandStatus } from '../services/api'

const $q = useQuasar()
const STATUS_FLOW = ['backlog', 'autorizacao', 'fila', 'desenvolvimento', 'teste', 'deploy', 'concluido']

const demands = ref([])
const saving = ref(false)
const advancing = ref(false)

const backlog = computed(() => demands.value.filter(d => d.status === 'backlog'))
const autorizacao = computed(() => demands.value.filter(d => d.status === 'autorizacao'))
const fila = computed(() => demands.value.filter(d => d.status === 'fila'))
const emDesenvolvimento = computed(() => demands.value.filter(d => d.status === 'desenvolvimento'))
const emTeste = computed(() => demands.value.filter(d => d.status === 'teste'))
const deploy = computed(() => demands.value.filter(d => d.status === 'deploy'))
const concluido = computed(() => demands.value.filter(d => d.status === 'concluido'))

const statusCobranca = computed(() => {
  const demanda = selectedDemand.value
  if (!demanda) return ''
  if (demanda.cobrada_do_cliente) return 'Demanda cobrada'
  return 'Demanda com valor a cobrar'
})

const showCardModal = ref(false)
const selectedDemand = ref(null)

const showMoveErrorModal = ref(false)
const moveErrorTitle = ref('')
const moveErrorMessages = ref([])

function abrirModalErroMovimento(data) {
  moveErrorTitle.value = data?.message || 'Não foi possível realizar a ação.'
  const erros = data?.errors
  if (erros && typeof erros === 'object') {
    const lista = Object.values(erros).flat().filter(Boolean)
    moveErrorMessages.value = lista.length > 0 ? lista : [moveErrorTitle.value]
  } else {
    moveErrorMessages.value = [moveErrorTitle.value]
  }
  showMoveErrorModal.value = true
}

function statusLabel(status) {
  if (!status) return ''
  const labels = {
    backlog: 'Backlog',
    autorizacao: 'Autorização',
    fila: 'Fila',
    desenvolvimento: 'Em desenvolvimento',
    teste: 'Teste',
    deploy: 'Deploy',
    concluido: 'Concluído',
  }
  return labels[status] || status
}

onMounted(async () => {
  try {
    let data
    try {
      data = await getDemands()
    } catch (err) {
      if (err.response?.status === 404) {
        data = []
      } else {
        throw err
      }
    }
    demands.value = Array.isArray(data) ? data : []
  } catch (err) {
    demands.value = []
    const msg = err.response?.data?.message || 'Erro ao carregar demandas.'
    $q.notify({ type: 'negative', message: msg, position: 'top' })
  }
})

function openCardModal(demand) {
  if (!demand.client) demand.client = demand.cliente || { nome: '' }
  selectedDemand.value = demand
  showCardModal.value = true
}

function closeCardModal() {
  showCardModal.value = false
  selectedDemand.value = null
}

async function onSave() {
  const demanda = selectedDemand.value
  if (!demanda || !demanda.id) {
    closeCardModal()
    return
  }
  saving.value = true
  try {
    const payload = {
      titulo: demanda.titulo,
      prioridade: demanda.prioridade || 'Normal',
      setor: demanda.setor ?? '',
      responsavel: demanda.responsavel ?? '',
      quem_deve_testar: demanda.quem_deve_testar ?? '',
      descricao_detalhada: demanda.descricao_detalhada ?? '',
      midia: demanda.midia ?? '',
      valor_total: demanda.valor_total ?? null,
      valor_pago: demanda.valor_pago ?? null,
      tempo_estimado: demanda.tempo_estimado ?? null,
      tempo_gasto: demanda.tempo_gasto ?? null,
      feedback: demanda.feedback ?? '',
      flag_retornou: demanda.flag_retornou === true,
    }
    const updated = await updateDemand(demanda.id, payload)
    const idx = demands.value.findIndex(d => d.id === demanda.id)
    if (idx !== -1 && updated) {
      demands.value.splice(idx, 1, { ...demands.value[idx], ...updated })
      selectedDemand.value = { ...selectedDemand.value, ...updated }
    }
    $q.notify({ type: 'positive', message: 'Alterações salvas com sucesso!', timeout: 2500, position: 'top' })
    closeCardModal()
  } catch (err) {
    const data = err.response?.data || {}
    let msg = data.message || 'Erro ao salvar.'
    if (data.errors) {
      const lista = Object.values(data.errors).flat()
      if (lista.length > 0) msg = lista[0]
    }
    $q.notify({ type: 'negative', message: msg, position: 'top' })
  } finally {
    saving.value = false
  }
}

async function advanceDemand() {
  const demanda = selectedDemand.value
  if (!demanda || !demanda.id) return
  const idx = STATUS_FLOW.indexOf(demanda.status)
  if (idx === -1 || idx >= STATUS_FLOW.length - 1) return
  const proximoStatus = STATUS_FLOW[idx + 1]

  advancing.value = true
  try {
    const updated = await updateDemandStatus(demanda.id, proximoStatus)
    const idxLista = demands.value.findIndex(d => d.id === demanda.id)
    if (idxLista !== -1) {
      demands.value.splice(idxLista, 1, { ...demands.value[idxLista], ...updated })
      selectedDemand.value = { ...selectedDemand.value, ...updated }
    }
    $q.notify({ type: 'positive', message: 'Status atualizado.', position: 'top' })
    closeCardModal()
  } catch (err) {
    closeCardModal()
    abrirModalErroMovimento(err.response?.data || {})
  } finally {
    advancing.value = false
  }
}

// Ao arrastar um card para outra coluna
async function onDrop({ demandId, targetStatus }) {
  const idx = demands.value.findIndex(d => d.id == demandId)
  if (idx === -1) return

  const demanda = demands.value[idx]
  const statusAnterior = demanda.status

  // Atualiza na tela logo
  demands.value.splice(idx, 1, { ...demanda, status: targetStatus })

  try {
    const updated = await updateDemandStatus(demandId, targetStatus)
    const idxAtual = demands.value.findIndex(d => d.id == demandId)
    if (idxAtual !== -1) {
      demands.value.splice(idxAtual, 1, { ...demands.value[idxAtual], ...updated })
    }
  } catch (err) {
    const idxAtual = demands.value.findIndex(d => d.id == demandId)
    if (idxAtual !== -1) {
      demands.value.splice(idxAtual, 1, { ...demands.value[idxAtual], status: statusAnterior })
    }
    abrirModalErroMovimento(err.response?.data || {})
  }
}
</script>

<style scoped>
.kanban-container {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  min-height: calc(100vh - 180px);
  max-width: 1700px;
  margin-left: auto;
  margin-right: auto;
}
.card-modal--full {
  min-width: 420px;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
}
.valores-tempo :deep(input[type="number"]::-webkit-outer-spin-button),
.valores-tempo :deep(input[type="number"]::-webkit-inner-spin-button) { -webkit-appearance: none; margin: 0; }
.valores-tempo :deep(input[type="number"]) { -moz-appearance: textfield; appearance: textfield; }
</style>
