// ----------------------------
// CSS GLOBAIS E DE BIBLIOTECAS
// ----------------------------
import "bootstrap/dist/css/bootstrap.min.css"; // se quiser manter o grid do bootstrap
import "primeflex/primeflex.css";
import "primeicons/primeicons.css";

// ----------------------------
// VUE, INERTIA E ZIGGY
// ----------------------------
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// ----------------------------
// PRIMEVUE CONFIGURAÇÃO
// ----------------------------
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import DialogService from "primevue/dialogservice";
import ConfirmationService from "primevue/confirmationservice";

// Tema moderno (novo sistema de temas)
import Aura from "@primeuix/themes/aura";
import { definePreset } from "@primeuix/themes";

// Define um preset customizado baseado em "Aura"
const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: "{indigo.50}",
            100: "{indigo.100}",
            200: "{indigo.200}",
            300: "{indigo.300}",
            400: "{indigo.400}",
            500: "{indigo.500}",
            600: "{indigo.600}",
            700: "{indigo.700}",
            800: "{indigo.800}",
            900: "{indigo.900}",
            950: "{indigo.950}",
        },
    },
});

// ----------------------------
// COMPONENTES PRIMEVUE GLOBAIS
// ----------------------------
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";
import Toast from "primevue/toast";
import FileUpload from "primevue/fileupload";
import ConfirmDialog from "primevue/confirmdialog";

// ----------------------------
// APP CONFIGURAÇÃO
// ----------------------------
const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp.use(plugin);
        vueApp.use(ZiggyVue);
        vueApp.use(ToastService);
        vueApp.use(DialogService);
        vueApp.use(ConfirmationService);
        vueApp.use(PrimeVue, {
            ripple: true,
            theme: {
                preset: MyPreset,
                options: {
                    darkModeSelector: ".dark-mode", // caso use modo escuro
                },
            },
        });

        // Registrar componentes globais PrimeVue
        vueApp.component("Button", Button);
        vueApp.component("InputText", InputText);
        vueApp.component("DataTable", DataTable);
        vueApp.component("Column", Column);
        vueApp.component("Dialog", Dialog);
        vueApp.component("Toast", Toast);
        vueApp.component("FileUpload", FileUpload);
        vueApp.component("ConfirmDialog", ConfirmDialog);

        vueApp.mount(el);
        return vueApp;
    },
    progress: {
        color: "#4B5563",
    },
});
