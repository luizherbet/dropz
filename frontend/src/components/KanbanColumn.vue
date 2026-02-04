<template>
  <div class="kanban-column" :class="columnClass">
    <div class="column-header">
      <span class="column-title">{{ title }}</span>
      <span class="column-count">{{ count }}</span>
    </div>
    <div
      class="column-cards q-gutter-y-sm"
      @dragover.prevent="onDragOver"
      @drop="onDrop"
      :class="{ 'column-cards--drag-over': dragOver }"
    >
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  color: { type: String, default: 'backlog' },
  count: { type: Number, default: 0 },
  columnStatus: { type: String, required: true },
})

const emit = defineEmits(['drop'])
const dragOver = ref(false)

function onDragOver(e) {
  e.preventDefault()
  dragOver.value = true
}

function onDrop(e) {
  dragOver.value = false
  const id = e.dataTransfer.getData('text/plain')
  if (!id) return
  emit('drop', { demandId: id, targetStatus: props.columnStatus })
}

const columnClass = computed(() => `column-${props.color}`)
</script>

<style scoped>
.kanban-column {
  min-width: 220px;
  max-width: 260px;
  border-radius: 12px;
  padding: 10px;
  min-height: 380px;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.column-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 6px;
  margin-bottom: 6px;
  border-bottom: 2px solid rgba(0, 0, 0, 0.08);
}

.column-title {
  font-weight: 600;
  font-size: 0.85rem;
}

.column-count {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 999px;
  padding: 2px 6px;
  font-size: 0.75rem;
  font-weight: 500;
}

.column-cards {
  flex: 1;
  overflow-y: auto;
}

.column-backlog {
  background: #eceff1;
  border: 1px solid #b0bec5;
}
.column-autorizacao {
  background: #e8eaf6;
  border: 1px solid #9fa8da;
}
.column-fila {
  background: #f3e5f5;
  border: 1px solid #ce93d8;
}
.column-desenvolvimento {
  background: #fff3e0;
  border: 1px solid #ffb74d;
}
.column-teste {
  background: #e3f2fd;
  border: 1px solid #64b5f6;
}
.column-deploy {
  background: #e0f2f1;
  border: 1px solid #4db6ac;
}
.column-concluido {
  background: #e8f5e9;
  border: 1px solid #81c784;
}

</style>
