<template>
  <q-page class="max-width-custom q-pa-md">
    <div class="no-print q-mb-lg">
      <div class="text-subtitle2 text-grey-8 q-mb-sm">
        Selecione o cliente e o período (Ano-mês). Em seguida, clique em Gerar para exibir o relatório.
      </div>
      <div class="row q-col-gutter-md items-end">
        <div class="col-12 col-sm-4">
          <q-select
            v-model="filtroCliente"
            label="Cliente"
            outlined
            dense
            :options="clientes"
            option-value="id"
            option-label="nome"
            emit-value
            map-options
            clearable
          />
        </div>
        <div class="col-12 col-sm-4">
          <q-input
            v-model="filtroMesAno"
            label="Ano e mês"
            outlined
            dense
            placeholder="Ex: 2026-02"
            maxlength="7"
          />
        </div>
        <div class="col-12 col-sm-4">
          <q-btn
            color="primary"
            label="Gerar"
            icon="refresh"
            @click="gerar"
            :disable="!filtroCliente || !filtroMesAno"
          />
        </div>
      </div>
      <div v-if="erro" class="text-caption text-negative q-mt-xs">{{ erro }}</div>
    </div>

    <template v-if="relatorioGerado">
      <div class="q-mb-sm text-body2">
        Cliente: <strong>{{ nomeCliente }}</strong>
        <span v-if="filtroMesAno"> · {{ filtroMesAno }}</span>
      </div>

      <div class="q-mb-lg">
        <div class="text-subtitle2 text-grey-8 q-mb-sm">Demandas do período</div>
        <q-table
          :rows="lista"
          :columns="colunas"
          row-key="id"
          flat
          bordered
          :no-data-label="mensagemSemDados"
        />
      </div>

      <div v-if="lista.length > 0" class="no-print">
        <q-btn outline label="Exportar PDF" icon="picture_as_pdf" @click="exportarPdf" class="q-mr-sm" />
        <q-btn outline label="Exportar CSV" icon="table_chart" @click="exportarCsv" />
      </div>
    </template>
  </q-page>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { getClients, getMonthlyReport } from '../services/api'

const filtroCliente = ref(null)
const filtroMesAno = ref('')
const erro = ref('')
const relatorioGerado = ref(false)
const clientes = ref([])
const reportData = ref(null)

onMounted(async () => {
  try {
    const data = await getClients().catch(() => [])
    clientes.value = Array.isArray(data) ? data : []
  } catch {
    clientes.value = []
  }
})

watch([filtroCliente, filtroMesAno], () => {
  relatorioGerado.value = false
  erro.value = ''
})

function statusTexto(s) {
  if (!s) return ''
  const map = {
    backlog: 'Backlog',
    autorizacao: 'Autorização',
    fila: 'Fila',
    desenvolvimento: 'Em desenvolvimento',
    teste: 'Teste',
    deploy: 'Deploy',
    concluido: 'Concluído',
  }
  return map[s] || s
}

const colunas = [
  { name: 'titulo', label: 'Título', field: 'titulo', align: 'left' },
  { name: 'status', label: 'Status', field: (row) => statusTexto(row.status), align: 'left' },
  { name: 'cobrada', label: 'Cobrada', field: (row) => (row.cobrada_do_cliente ? 'Sim' : 'Não'), align: 'left' },
  { name: 'tempo_estimado', label: 'Tempo estimado (min)', field: 'tempo_estimado', align: 'right' },
  { name: 'tempo_gasto', label: 'Tempo gasto (min)', field: 'tempo_gasto', align: 'right' },
  { name: 'feedback', label: 'Feedback', field: (row) => row.feedback || '-', align: 'left' },
]

const nomeCliente = computed(() => {
  if (reportData.value?.client?.nome) return reportData.value.client.nome
  const c = clientes.value.find((x) => x.id === filtroCliente.value)
  return c ? c.nome : ''
})

const lista = computed(() => reportData.value?.demands ?? [])

async function gerar() {
  const d = new Date()
  const mesAnoMax = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0')

  erro.value = ''
  if (!filtroCliente.value) {
    erro.value = 'Selecione o cliente.'
    return
  }
  if (!filtroMesAno.value) {
    erro.value = 'Selecione o ano e o mês.'
    return
  }
  if (filtroMesAno.value > mesAnoMax) {
    erro.value = 'O período não pode ser futuro.'
    return
  }
  try {
    const data = await getMonthlyReport(filtroCliente.value, filtroMesAno.value)
    reportData.value = data
    relatorioGerado.value = true
  } catch (err) {
    const msg = err.response?.data?.message || 'Erro ao gerar relatório.'
    const errors = err.response?.data?.errors
    erro.value = errors?.month?.[0] || msg
    relatorioGerado.value = false
    reportData.value = null
  }
}

function exportarPdf() {
  window.print()
}

function exportarCsv() {
  if (lista.value.length === 0) return
  const cabecalho = 'Título;Status;Cobrada;Tempo estimado (min);Tempo gasto (min);Feedback'
  const linhas = lista.value.map((dem) => {
    const status = statusTexto(dem.status)
    const cobrada = dem.cobrada_do_cliente ? 'Sim' : 'Não'
    const te = dem.tempo_estimado ?? ''
    const tg = dem.tempo_gasto ?? ''
    const fb = (dem.feedback || '').replace(/;/g, ',')
    return dem.titulo + ';' + status + ';' + cobrada + ';' + te + ';' + tg + ';' + fb
  })
  const csv = cabecalho + '\n' + linhas.join('\n')
  const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'relatorio-' + filtroMesAno.value + '.csv'
  a.click()
  URL.revokeObjectURL(url)
}

const mensagemSemDados = computed(() => {
  if (relatorioGerado.value && lista.value.length === 0) {
    return 'Nenhuma demanda encontrada para este cliente no período.'
  }
  return 'Nenhum registro.'
})
</script>

<style scoped>
.max-width-custom {
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

@media print {
  .no-print {
    display: none !important;
  }
  @page {
    size: landscape;
  }
  body, .q-page, .max-width-custom {
    width: 100% !important;
    max-width: none !important;
  }
}
</style>
