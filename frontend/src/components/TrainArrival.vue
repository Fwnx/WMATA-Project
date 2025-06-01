<template>
  <div class="train-arrival">
    <div class="station-dropdown-container">
      <label class="station-dropdown-label" for="station">Select a Station:</label>
      <div class="station-dropdown" :class="{ 'loading': loading, 'error': error }">
        <select 
          id="station" 
          v-model="selectedStation"
          @change="fetchPredictions"
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

    <div v-if="loading" class="loading">
      <div class="loading-spinner"></div>
      Loading...
    </div>

    <div v-else-if="predictions.length" class="predictions">
      <h3>Upcoming Trains</h3>
      <div class="prediction-grid">
        <div class="prediction-header">
          <span>Line</span>
          <span>Destination</span>
          <span>Minutes</span>
        </div>
        <div 
          v-for="prediction in predictions" 
          :key="prediction.DestinationCode + prediction.Min"
          class="prediction-row"
          :class="prediction.Line.toLowerCase()"
        >
          <span class="line-indicator">{{ prediction.Line }}</span>
          <span>{{ prediction.DestinationName }}</span>
          <span class="arrival-time">{{ prediction.Min }}</span>
        </div>
      </div>
    </div>

    <div v-else-if="selectedStation" class="no-trains">
      No trains currently predicted for this station.
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { wmataService } from '../services/wmataService'
import type { Station, Prediction } from '../services/wmataService'

const stations = ref<Station[]>([])
const predictions = ref<Prediction[]>([])
const selectedStation = ref('')
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

const fetchPredictions = async () => {
  if (!selectedStation.value) {
    predictions.value = []
    return
  }

  try {
    loading.value = true
    error.value = ''
    predictions.value = await wmataService.getPredictions(selectedStation.value)
  } catch (e) {
    error.value = 'Failed to load predictions. Please try again later.'
    predictions.value = []
  } finally {
    loading.value = false
  }
}

onMounted(fetchStations)
</script>