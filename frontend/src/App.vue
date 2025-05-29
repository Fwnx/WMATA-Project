<template>
  <div class="app">
    <header class="header">
      <h1>WMATA Train Tracker</h1>
    </header>
    <main>
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup lang="ts">
// No imports needed
</script>

<style>
:root {
  --primary-color: #1a73e8;
  --background-color: #f8f9fa;
  --text-color: #2c3e50;
}

body {
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  background-color: var(--background-color);
  color: var(--text-color);
}

.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.header {
  background-color: var(--primary-color);
  color: white;
  padding: 1rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header h1 {
  margin: 0;
  font-size: 1.5rem;
}

main {
  flex: 1;
  display: flex;
  flex-direction: column;
}
</style>
