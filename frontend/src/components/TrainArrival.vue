<template>
  <div class="train-arrival">
    <StationSelect v-model="selectedStation" />

    <LoadingSpinner v-if="loading" :show-text="true" />

    <PredictionGrid 
      v-else-if="predictions.length" 
      :predictions="predictions" 
    />

    <div v-else-if="selectedStation" class="no-trains">
      No trains currently predicted for this station.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { wmataService } from '../services/wmataService'
import type { Prediction } from '../services/wmataService'
import StationSelect from './station/StationSelect.vue'
import PredictionGrid from './predictions/PredictionGrid.vue'
import LoadingSpinner from './common/LoadingSpinner.vue'

const predictions = ref<Prediction[]>([])
const selectedStation = ref('')
const loading = ref(false)

const fetchPredictions = async () => {
  if (!selectedStation.value) {
    predictions.value = []
    return
  }

  try {
    loading.value = true
    predictions.value = await wmataService.getPredictions(selectedStation.value)
  } catch (e) {
    predictions.value = []
  } finally {
    loading.value = false
  }
}

// Watch for station changes
watch(selectedStation, fetchPredictions)
</script>

<style scoped>
.train-arrival {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-top: 2rem;
}

.no-trains {
  text-align: center;
  padding: 2rem;
  color: #666;
  background-color: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #eee;
}
</style>