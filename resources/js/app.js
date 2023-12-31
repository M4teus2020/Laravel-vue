import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.js'
import 'admin-lte/dist/js/adminlte.js'

import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Routes from './routes';
import Login from './pages/auth/Login.vue';

const app = createApp({});

const routes = createRouter({
    routes: Routes,
    history: createWebHistory(),
})

app.use(routes);
app.component('Login', Login);

app.mount('#App');