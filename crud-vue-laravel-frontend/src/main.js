import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { VueTheMask } from 'vue-the-mask' // Importa a biblioteca de máscaras

const app = createApp(App)

app.use(router)
app.use(VueTheMask) // Usa a biblioteca de máscaras globalmente

axios.defaults.baseURL = 'http://localhost/crud-vue-laravel-backend/public/api/';

app.mount('#app')