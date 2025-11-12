import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

// =========================
// FontAwesome Configuration
// =========================
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// Ícones (adicione ou remova conforme necessário)
import {
    faBars, faChevronLeft, faChevronRight, faChevronDown, faSearch,
    faHome, faUserMd, faTachometerAlt, faList, faUserPlus, faHeartbeat,
    faCalendarCheck, faBuilding, faUserNurse, faWallet, faChartLine,
    faUser, faCog, faQuestionCircle, faSignOutAlt, faUserCheck, faDatabase, faBell, faEnvelope
} from '@fortawesome/free-solid-svg-icons'

// Registrar ícones na biblioteca global
library.add(
    faBars, faChevronLeft, faChevronRight, faChevronDown, faSearch,
    faHome, faUserMd, faTachometerAlt, faList, faUserPlus, faHeartbeat,
    faCalendarCheck, faBuilding, faUserNurse, faWallet, faChartLine,
    faUser, faCog, faQuestionCircle, faSignOutAlt, faUserCheck, faDatabase, faBell, faEnvelope
)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// =========================
// Inertia App Bootstrap
// =========================
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
        vueApp.use(plugin)
        vueApp.use(ZiggyVue)

        // Registrar componente global do FontAwesome
        vueApp.component('font-awesome-icon', FontAwesomeIcon)

        vueApp.mount(el)
        return vueApp
    },
    progress: {
        color: '#4B5563',
    },
})
