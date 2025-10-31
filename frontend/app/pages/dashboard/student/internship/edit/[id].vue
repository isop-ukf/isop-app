<script setup lang="ts">
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth', 'student-only'],
});

useSeoMeta({
    title: "Edit praxe | ISOP",
    ogTitle: "Edit praxe",
    description: "Edit praxe ISOP",
    ogDescription: "Edit praxe",
});

const user = useSanctumUser<User>();
</script>

<template>
    <v-container fluid>
        <v-card id="page-container-card">
            <p>...</p>
        </v-card>
    </v-container>
</template>

<style scoped>
#page-container-card {
    padding-left: 10px;
    padding-right: 10px;
}
</style>