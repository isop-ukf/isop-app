<script setup lang="ts">
import type { User } from '~/types/user';

useSeoMeta({
    title: "Portál študenta | ISOP",
    ogTitle: "Portál študenta",
    description: "Portál študenta ISOP",
    ogDescription: "Portál študenta",
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