<template>
  <div class="station-dropdown-container">
    <label class="station-dropdown-label" for="station">Select a Station:</label>
    <div class="station-dropdown" :class="{ 'loading': loading, 'error': error }">
      <select 
        id="station" 
        v-model="selectedValue"
      >
        <option value="">Choose a station...</option>
        <option 
          v-for="station in stations" 
          :key="station.Code" 
          :value="station.Code"
        >
          {{ station.Name }}
        </option>
      </select>
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { wmataService } from '../../services/wmataService'
import type { Station } from '../../services/wmataService'

const props = defineProps<{
  modelValue: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const selectedValue = computed({
  get: () => props.modelValue,
  set: (value: string) => emit('update:modelValue', value)
})

const stations = ref<Station[]>([])
const loading = ref(false)
const error = ref('')

const fetchStations = async () => {
  try {
    loading.value = true
    stations.value = await wmataService.getStations()
  } catch (e) {
    error.value = 'Failed to load stations. Please try again later.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchStations)
</script> 