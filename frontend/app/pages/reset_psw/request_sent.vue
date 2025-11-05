<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:guest'],
});

useSeoMeta({
    title: "Reset hesla | ISOP",
    ogTitle: "Reset hesla",
    description: "Reset hesla ISOP",
    ogDescription: "Reset hesla",
});
</script>

<template>
    <v-container fluid class="page-container form-wrap">
        <v-card id="page-container-card">
            <h2 class="page-title">Reset hesla</h2>

            <InfoAlert title="Reset hesla" text="Nové heslo vám bolo zaslané na e-mail" />
        </v-card>
    </v-container>
</template>

<style scoped>
.page-container {
    max-width: 1120px;
    margin: 0 auto;
    padding-left: 24px;
    padding-right: 24px;
}

#page-container-card {
    padding: 10px;
}

.form-wrap {
    max-width: 560px;
}

.page-title {
    font-size: 24px;
    line-height: 1.2;
    font-weight: 700;
    margin: 24px 0 16px;
    color: #1f1f1f;
}

.mb-3 {
    margin-bottom: 12px;
}
</style>
