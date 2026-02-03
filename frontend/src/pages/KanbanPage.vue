<template>
  <q-page class="q-pa-md">
    <div class="kanban-container">
      <KanbanColumn title="Backlog" color="backlog" column-status="backlog" :count="backlog.length" @drop="onDrop">
        <KanbanCard
          v-for="item in backlog"
          :key="item.id"
          :demand="item"
          :title="item.titulo"
          :cliente="item.cliente?.nome"
          :setor="item.setor"
          :responsavel="item.responsavel"
          :prioridade="item.prioridade"
          :flag-retorno="item.flag_retorno === true"
          @click="openCardModal"
        />
      </KanbanColumn>
      <KanbanColumn title="Autorização" color="autorizacao" column-status="autorizacao" :count="autorizacao.length" @drop="onDrop">
        <KanbanCard v-for="item in autorizacao" :key="item.id" :demand="item" :title="item.titulo" :cliente="item.cliente?.nome" :setor="item.setor" :responsavel="item.responsavel" :prioridade="item.prioridade" :flag-retorno="item.flag_retorno === true" @click="openCardModal" />
      </KanbanColumn>
      <KanbanColumn title="Fila" color="fila" column-status="fila" :count="fila.length" @drop="onDrop">
        <KanbanCard v-for="item in fila" :key="item.id" :demand="item" :title="item.titulo" :cliente="item.cliente?.nome" :setor="item.setor" :responsavel="item.responsavel" :prioridade="item.prioridade" :flag-retorno="item.flag_retorno === true" @click="openCardModal" />
      </KanbanColumn>
      <KanbanColumn title="Em desenvolvimento" color="desenvolvimento" column-status="desenvolvimento" :count="emDesenvolvimento.length" @drop="onDrop">
        <KanbanCard v-for="item in emDesenvolvimento" :key="item.id" :demand="item" :title="item.titulo" :cliente="item.cliente?.nome" :setor="item.setor" :responsavel="item.responsavel" :prioridade="item.prioridade" :flag-retorno="item.flag_retorno === true" @click="openCardModal" />
      </KanbanColumn>
      <KanbanColumn title="Teste" color="teste" column-status="teste" :count="emTeste.length" @drop="onDrop">
        <KanbanCard v-for="item in emTeste" :key="item.id" :demand="item" :title="item.titulo" :cliente="item.cliente?.nome" :setor="item.setor" :responsavel="item.responsavel" :prioridade="item.prioridade" :flag-retorno="item.flag_retorno === true" @click="openCardModal" />
      </KanbanColumn>
      <KanbanColumn title="Deploy" color="deploy" column-status="deploy" :count="deploy.length" @drop="onDrop">
        <KanbanCard v-for="item in deploy" :key="item.id" :demand="item" :title="item.titulo" :cliente="item.cliente?.nome" :setor="item.setor" :responsavel="item.responsavel" :prioridade="item.prioridade" :flag-retorno="item.flag_retorno === true" @click="openCardModal" />
      </KanbanColumn>
      <KanbanColumn title="Concluído" color="concluido" column-status="concluido" :count="concluido.length" @drop="onDrop">
        <KanbanCard v-for="item in concluido" :key="item.id" :demand="item" :title="item.titulo" :cliente="item.cliente?.nome" :setor="item.setor" :responsavel="item.responsavel" :prioridade="item.prioridade" :flag-retorno="item.flag_retorno === true" @click="openCardModal" />
      </KanbanColumn>
    </div>

    <q-dialog v-model="showCardModal" persistent>
      <q-card class="card-modal card-modal--full">
        <q-card-section class="q-pt-md q-gutter-y-md">
          <div v-if="selectedDemand" class="text-h6 q-mb-sm">Editar demanda</div>
          <q-form v-if="selectedDemand" @submit="onSave" class="q-gutter-y-md">
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Dados gerais</div>
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <q-input :model-value="selectedDemand.cliente.nome" label="Cliente" outlined dense readonly />
              </div>
              <div class="col-12">
                <q-input v-model="selectedDemand.titulo" label="Título *" outlined dense :rules="[val => !!val || 'Título é obrigatório']" />
              </div>
              <div class="col-6">
                <q-select v-model="selectedDemand.prioridade" label="Prioridade" outlined dense :options="['Alta', 'Normal', 'Baixa']" />
              </div>
              <div class="col-6">
                <q-input v-model="selectedDemand.setor" label="Setor" outlined dense />
              </div>
              <div class="col-12">
                <q-checkbox v-model="selectedDemand.flag_retorno" label="Flag retorno" />
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
            <q-input v-model="selectedDemand.descricao_detalhada" label="Descrição detalhada" outlined dense type="textarea" rows="3" placeholder="Detalhes da demanda..." class="q-mb-md" />
            <q-input v-model="selectedDemand.midia" label="Mídia / links" outlined dense type="textarea" rows="2" placeholder="Links de anexos..." class="q-mb-md" />
            <q-separator class="q-my-md" />
            <div class="valores-tempo">
              <div class="text-subtitle2 text-grey-8 q-mb-sm">Valores e tempo</div>
              <q-checkbox v-model="selectedDemand.cobrada_do_cliente" label="Cobrada do cliente" class="q-mb-sm" />
              <div class="row q-col-gutter-md">
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.valor_total" label="Valor total" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.valor_pago" label="Valor pago" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.tempo_estimado" label="Tempo estimado (min)" outlined dense type="number" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="selectedDemand.tempo_gasto" label="Tempo gasto (min)" outlined dense type="number" />
                </div>
              </div>
            </div>
            <q-separator class="q-my-md" />
            <div class="text-subtitle2 text-grey-8 q-mb-sm">Feedback</div>
            <q-input v-model="selectedDemand.feedback" label="Feedback" outlined dense type="textarea" rows="2" placeholder="Preenchido na etapa Teste" />
            <div class="q-mt-md text-caption text-grey-7">Status atual: {{ statusLabel(selectedDemand?.status) }}</div>
            <q-card-actions align="right" class="q-pa-none q-mt-md">
              <q-btn flat label="Fechar" color="grey" @click="closeCardModal" />
              <q-btn label="Salvar" type="submit" color="primary" />
              <q-btn v-if="selectedDemand && selectedDemand.status !== 'concluido'" label="Avançar" color="primary" outline @click="advanceDemand" />
            </q-card-actions>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, computed } from 'vue'
import KanbanColumn from '../components/KanbanColumn.vue'
import KanbanCard from '../components/KanbanCard.vue'

const STATUS_FLOW = ['backlog', 'autorizacao', 'fila', 'desenvolvimento', 'teste', 'deploy', 'concluido']
function statusLabel(status) {
  if (!status) return ''
  if (status === 'backlog') return 'Backlog'
  if (status === 'autorizacao') return 'Autorização'
  if (status === 'fila') return 'Fila'
  if (status === 'desenvolvimento') return 'Em desenvolvimento'
  if (status === 'teste') return 'Teste'
  if (status === 'deploy') return 'Deploy'
  if (status === 'concluido') return 'Concluído'
  return status
}

const demands = ref([
  { id: 1, titulo: 'Ajuste layout login', prioridade: 'Alta', status: 'backlog', cliente: { nome: 'Cliente A' }, setor: 'Web', responsavel: 'Luiz', flag_retorno: true },
  { id: 2, titulo: 'API relatório', prioridade: 'Normal', status: 'autorizacao', cliente: { nome: 'Cliente B' }, setor: 'TI', responsavel: 'João', flag_retorno: false },
])

const backlog = computed(() => demands.value.filter(d => d.status === 'backlog'))
const autorizacao = computed(() => demands.value.filter(d => d.status === 'autorizacao'))
const fila = computed(() => demands.value.filter(d => d.status === 'fila'))
const emDesenvolvimento = computed(() => demands.value.filter(d => d.status === 'desenvolvimento'))
const emTeste = computed(() => demands.value.filter(d => d.status === 'teste'))
const deploy = computed(() => demands.value.filter(d => d.status === 'deploy'))
const concluido = computed(() => demands.value.filter(d => d.status === 'concluido'))

const showCardModal = ref(false)
const selectedDemand = ref(null)

function openCardModal(demand) {
  if (!demand.cliente) demand.cliente = { nome: '' }
  selectedDemand.value = demand
  showCardModal.value = true
}

function closeCardModal() {
  showCardModal.value = false
  selectedDemand.value = null
}

function onSave() {
  closeCardModal()
}

function advanceDemand() {
  if (!selectedDemand.value) return
  const idx = STATUS_FLOW.indexOf(selectedDemand.value.status)
  if (idx !== -1 && idx < STATUS_FLOW.length - 1) {
    selectedDemand.value.status = STATUS_FLOW[idx + 1]
  }
  closeCardModal()
}

function onDrop({ demandId, targetStatus }) {
  const demand = demands.value.find(d => d.id == demandId)
  if (demand) demand.status = targetStatus
}
</script>

<style scoped>
.kanban-container {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding-bottom: 8px;
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
