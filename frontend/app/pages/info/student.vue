<script setup lang="ts">
useSeoMeta({
    title: "Informácie pre študenta | ISOP",
    ogTitle: "Informácie pre študenta",
    description: "Informácie pre študenta ISOP",
    ogDescription: "Informácie pre študenta",
});
</script>

<template>
    <v-container fluid>
        <v-card>
            <h2 class="page-container page-title">Informácie o odbornej praxi pre študentov</h2>
            <p class="page-container page-lead">
                Kompletné informácie o odbornej praxi – priebeh, hodnotenie, zmluvy a pokyny pre študentov.
            </p>

            <br />

            <h3 class="page-container section-title">Podmienky absolvovania predmetu</h3>

            <v-row class="pc" align="stretch" justify="start">
                <InfoCard title="Rozsah a účasť"
                    description="Absolvovanie praxe v minimálnom rozsahu 130 hodín a povinná účasť na úvodnom stretnutí"
                    icon="mdi-clock-time-five-outline" />
                <InfoCard title="Denník praxe"
                    description="Priebežné vedenie denníka praxe podľa predpísanej štruktúry a jeho odovzdanie na konci obdobia."
                    icon="mdi-notebook-edit-outline" />
                <InfoCard title="Poster a prezentácia"
                    description="Príprava a prezentácia posteru. Hodnotí sa obsah, vizuálna stránka a kvalita prezentácie."
                    icon="mdi-presentation" />
                <InfoCard title="Bodové hodnotenie"
                    description="Max. 100 bodov (50b firma, 50b poster a prezentácia). Minimum pre absolvovanie je 51 bodov."
                    icon="mdi-checkbox-marked-outline" />
                <InfoCard title="Kredity" description="Po úspešnom absolvovaní predmetu študent získa 5 kreditov"
                    icon="mdi-school-outline" />
            </v-row>
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

.page-title {
    font-size: 32px;
    line-height: 1.2;
    font-weight: 700;
    margin: 16px 0 8px;
    color: #1f1f1f;
}

.page-lead {
    margin: 0 0 24px 0;
    color: #6b6b6b;
}

.pc {
    margin: 0 0 24px 0;
}

.section-title {
    font-size: 22px;
    font-weight: 700;
    margin: 16px 0 12px;
    color: #1f1f1f;
}
</style>
