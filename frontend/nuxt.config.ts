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
        compressPublicAssets: true,
    },

    sanctum: {
        baseUrl: 'http://localhost:8000',
        redirect: {
            onLogin: '/dashboard',
            onLogout: "/",
            onAuthOnly: '/login',
            keepRequestedRoute: false,
            onGuestOnly: false,
        },
        redirectIfAuthenticated: true
    },

    typescript: {
        strict: true,
        typeCheck: true,
    },

    runtimeConfig: {
        public: {
            demo: {
                enabled: false, // NUXT_PUBLIC_DEMO_ENABLED
                student: {
                    email: "student@ukf.sk", // NUXT_PUBLIC_DEMO_STUDENT_EMAIL
                    password: "password" // NUXT_PUBLIC_DEMO_STUDENT_PASSWORD
                },
                company: {
                    email: "employer@company.com",
                    password: "password"
                },
                admin: {
                    email: "admin@ukf.sk",
                    password: "password"
                }
            }
        }
    }
});