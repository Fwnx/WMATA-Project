/**
 * main.ts
 *
 * Bootstraps Vue application
 */

import { createApp } from 'vue'
import App from './App.vue'
import { registerPlugins } from '@/plugins'
import router from './router'

// Create app
const app = createApp(App)

// Register plugins (Vuetify)
registerPlugins(app)

// Use router
app.use(router)

// Mount app
app.mount('#app')
