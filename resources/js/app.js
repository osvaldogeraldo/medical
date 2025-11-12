import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { InertiaProgress } from '@inertiajs/progress';
import { route } from 'ziggy-js'; // ✅ Correto

InertiaProgress.init();

createInertiaApp({
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin);

        // Disponibiliza Ziggy no Vue
        vueApp.config.globalProperties.$route = (name, params, absolute) =>
            route(name, params, absolute, window.Ziggy);

        return vueApp.mount(el);
    },
});
