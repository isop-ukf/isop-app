<script setup lang="ts">
import type { User } from '~/types/user';

useSeoMeta({
    title: "Portál administrátora | ISOP",
    ogTitle: "Portál administrátora",
    description: "Portál administrátora ISOP",
    ogDescription: "Portál administrátora",
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="footer-card">
            <h1>Vitajte, {{ user?.name }}</h1>


        </v-card>
    </v-container>
</template>