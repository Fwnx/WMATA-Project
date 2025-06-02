<template>
  <div class="loading">
    <div class="loading-spinner"></div>
    <span v-if="showText">Loading...</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  showText?: boolean
}>()
</script>

<style scoped>
.loading {
  text-align: center;
  padding: 2rem;
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.loading-spinner {
  width: 30px;
  height: 30px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 