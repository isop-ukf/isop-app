<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:guest'],
});

useSeoMeta({
    title: "Registrácia | ISOP",
    ogTitle: "Registrácia",
    description: "Registrácia ISOP",
    ogDescription: "Registrácia",
});
</script>

<template>
    <v-container fluid>
        <v-card>
            <v-row class="pc" align="stretch" justify="start">
                <PageCard title="Registrácia študenta" description="Zaregistruj sa a začni svoju odbornú prax."
                    link="/register/student" icon="mdi mdi-account" />
                <PageCard title="Registrácia firmy"
                    description="Staň sa partnerskou firmou a pomôž študentom získať prax vo svojom odbore."
                    link="/register/company" icon="mdi mdi-office-building" />
            </v-row>
        </v-card>
    </v-container>
</template>

<style scoped>
.pc {
    margin: 0 0 24px 0;
}
</style>
