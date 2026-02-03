<template>
  <q-page class="max-width-custom q-pa-md">
    <div class="no-print q-mb-lg">
      <div class="text-subtitle2 text-grey-8 q-mb-sm">
        Selecione o cliente e o período (mês/ano). Em seguida, clique em Gerar para exibir o relatório.
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
            label="Mês e ano"
            outlined
            dense
            type="month"
            :max="mesAnoMax"
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
import { ref, computed, watch } from 'vue'

const filtroCliente = ref(null)
const filtroMesAno = ref('')
const erro = ref('')
const relatorioGerado = ref(false)

const clientes = ref([
  { id: 1, nome: 'Tech Solutions Ltda' },
  { id: 2, nome: 'Comércio Digital' },
  { id: 3, nome: 'Indústria Alpha' },
  { id: 4, nome: 'Startup Beta' },
])

const demandas = ref([
  { id: 1, titulo: 'Ajuste layout', cliente_id: 1, status: 'concluido', cobrada_do_cliente: true, tempo_estimado: 120, tempo_gasto: 90, feedback: 'Ok', mes: '2026-01' },
  { id: 2, titulo: 'API relatório', cliente_id: 2, status: 'desenvolvimento', cobrada_do_cliente: true, tempo_estimado: 240, tempo_gasto: 100, feedback: '', mes: '2026-01' },
  { id: 3, titulo: 'Bug', cliente_id: 1, status: 'teste', cobrada_do_cliente: false, tempo_estimado: 60, tempo_gasto: 45, feedback: 'Testando', mes: '2026-01' },
  { id: 4, titulo: 'Dashboard analytics', cliente_id: 3, status: 'desenvolvimento', cobrada_do_cliente: true, tempo_estimado: 180, tempo_gasto: 100, feedback: '', mes: '2026-01' },
  { id: 5, titulo: 'Relatório fiscal jan/26', cliente_id: 3, status: 'concluido', cobrada_do_cliente: true, tempo_estimado: 90, tempo_gasto: 90, feedback: 'Aprovado', mes: '2026-01' },
  { id: 6, titulo: 'Migração servidor', cliente_id: 2, status: 'concluido', cobrada_do_cliente: false, tempo_estimado: 300, tempo_gasto: 280, feedback: 'Concluído', mes: '2025-12' },
])

watch([filtroCliente, filtroMesAno], () => {
  relatorioGerado.value = false
  erro.value = ''
})

function statusTexto(s) {
  if (s === 'backlog') return 'Backlog'
  if (s === 'autorizacao') return 'Autorização'
  if (s === 'fila') return 'Fila'
  if (s === 'desenvolvimento') return 'Em desenvolvimento'
  if (s === 'teste') return 'Teste'
  if (s === 'deploy') return 'Deploy'
  if (s === 'concluido') return 'Concluído'
  return s
}

const colunas = [
  { name: 'titulo', label: 'Título', field: 'titulo', align: 'left' },
  { name: 'status', label: 'Status', field: (row) => statusTexto(row.status), align: 'left' },
  { name: 'cobrada', label: 'Cobrada', field: (row) => row.cobrada_do_cliente ? 'Sim' : 'Não', align: 'left' },
  { name: 'tempo_estimado', label: 'Tempo estimado (min)', field: 'tempo_estimado', align: 'right' },
  { name: 'tempo_gasto', label: 'Tempo gasto (min)', field: 'tempo_gasto', align: 'right' },
  { name: 'feedback', label: 'Feedback', field: (row) => row.feedback || '-', align: 'left' },
]

const d = new Date()
const mesAnoMax = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0')

const nomeCliente = computed(() => {
  if (!filtroCliente.value) return ''
  const c = clientes.value.find((x) => x.id === filtroCliente.value)
  return c ? c.nome : ''
})

const lista = computed(() => {
  if (!filtroCliente.value || !filtroMesAno.value) return []
  return demandas.value.filter(
    (dem) => dem.cliente_id === filtroCliente.value && dem.mes === filtroMesAno.value
  )
})

function gerar() {
  erro.value = ''
  if (!filtroCliente.value) {
    erro.value = 'Selecione o cliente.'
    return
  }
  if (!filtroMesAno.value) {
    erro.value = 'Selecione o mês e o ano.'
    return
  }
  if (filtroMesAno.value > mesAnoMax) {
    erro.value = 'O período não pode ser futuro.'
    return
  }
  relatorioGerado.value = true
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
}
</style>
