<template>
  <div class="predictions">
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
</template>

<script setup lang="ts">
import type { Prediction } from '../../services/wmataService'

defineProps<{
  predictions: Prediction[]
}>()
</script> 