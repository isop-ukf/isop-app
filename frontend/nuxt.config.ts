// https://nuxt.com/docs/api/configuration/nuxt-config

export default defineNuxtConfig({
    // interné pre nuxt, nemeniť
    compatibilityDate: '2025-07-15',

    // debugovacie nástroje v prehliadači
    devtools: { enabled: true },

    // externé moduly
    modules: [
        '@nuxt/image', // na obrázky
        'vuetify-nuxt-module', // Vuetify
        'nuxt-auth-sanctum' // Laravel Sanctum auth
    ],

    nitro: {
        // statický obsah vyrenderujeme vopred
        prerender: {
            routes: ["/", "/info/student", "/info/company", "/register", "/about"],
        },
    },

    sanctum: {
        redirect: {
            onLogin: '/dashboard',
            onLogout: "/",
            onAuthOnly: '/login',
            keepRequestedRoute: false,
            onGuestOnly: false,
        },
        redirectIfAuthenticated: true
    },

    runtimeConfig: {
        public: {
            sanctum: {
                baseUrl: 'http://localhost:8080', // NUXT_PUBLIC_SANCTUM_BASE_URL
                origin: 'http://localhost:8080', // NUXT_PUBLIC_SANCTUM_ORIGIN
            },
        },
    },

    typescript: {
        strict: true,
        typeCheck: true,
    }
});