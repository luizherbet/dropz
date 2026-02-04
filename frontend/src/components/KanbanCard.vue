<template>
  <div
    class="kanban-card-wrapper"
    draggable="true"
    @dragstart="onDragStart"
    @click="onClick"
  >
    <q-card class="kanban-card q-mb-sm" flat bordered>
      <q-card-section class="q-pa-sm">
        <div class="row items-start no-wrap">
          <div class="col card-title">{{ title }}</div>
        </div>
        <div v-if="cliente" class="text-caption text-grey-7 ellipsis q-mt-xs">
          {{ cliente }}
        </div>
        <div v-if="setor" class="text-caption text-grey-8 ellipsis q-mt-xs">
          Setor: {{ setor }}
        </div>
        <div v-if="responsavel" class="text-caption text-grey-8 ellipsis q-mt-xs">
          Responsável: {{ responsavel }}
        </div>
        <div class="row items-center no-wrap q-mt-xs q-gutter-xs">
          <q-chip v-if="prioridade" dense size="sm" :color="priorityColor" text-color="white">
            {{ prioridade }}
          </q-chip>
          <q-icon
            v-if="mostrarFlagRetorno"
            name="info"
            size="18px"
            color="orange"
            class="flex-shrink-0"
            title="Retorno"
          />
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  demand: { type: Object, default: null },
  title: { type: String, required: true },
  cliente: { type: String, default: '' },
  setor: { type: String, default: '' },
  responsavel: { type: String, default: '' },
  prioridade: { type: String, default: '' },
  flagRetornou: { type: Boolean, default: false },
})

const emit = defineEmits(['click'])

// Ícone aparece quando flag_retornou está true no banco (marcar no modal "Flag retorno" e clicar Salvar)
const mostrarFlagRetorno = computed(() => {
  const d = props.demand
  return d && !!d.flag_retornou
})

const priorityColor = computed(() => {
  if (props.prioridade === 'Alta') return 'negative'
  if (props.prioridade === 'Baixa') return 'positive'
  return 'grey-6'
})

function onDragStart(e) {
  if (!props.demand) return
  e.dataTransfer.setData('text/plain', String(props.demand.id))
}

function onClick() {
  if (props.demand) emit('click', props.demand)
}
</script>

<style scoped>
.kanban-card-wrapper {
  cursor: grab;
}
.kanban-card {
  max-width: 196px;
  border-radius: 8px;
  transition: box-shadow 0.2s;
}
.kanban-card-wrapper:hover .kanban-card {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
.card-title {
  font-weight: 600;
  font-size: 0.9rem;
}
.ellipsis {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
