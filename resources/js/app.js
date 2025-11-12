// ==========================
// Imports principais
// ==========================
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { InertiaProgress } from '@inertiajs/progress';
import { route } from 'ziggy-js';

// ==========================
// Font Awesome
// ==========================
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Ícones que você vai usar
import {
  faHome,
  faUserDoctor,
  faHeartPulse,
  faCalendarCheck,
  faBuilding,
  faUserNurse,
  faWallet,
  faChartLine,
  faBars,
  faEnvelope,
  faBell,
} from '@fortawesome/free-solid-svg-icons';

// Adicionar à biblioteca
library.add(
  faHome,
  faUserDoctor,
  faHeartPulse,
  faCalendarCheck,
  faBuilding,
  faUserNurse,
  faWallet,
  faChartLine,
  faBars,
  faEnvelope,
  faBell
);

// ==========================
// Inertia Progress Bar
// ==========================
InertiaProgress.init({
    color: '#4B9CE2',
    showSpinner: false,
});

// ==========================
// Inicializa o Inertia App
// ==========================
createInertiaApp({
    resolve: name =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            // Registrar FontAwesome globalmente
            .component('font-awesome-icon', FontAwesomeIcon);

        // Ziggy global
        vueApp.config.globalProperties.$route = (name, params, absolute) =>
            route(name, params, absolute, window.Ziggy);

        return vueApp.mount(el);
    },
});
