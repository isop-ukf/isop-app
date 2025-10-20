<script setup lang="ts">
useSeoMeta({
    title: "ISOP - Systém na evidenciu študentskej praxe",
    ogTitle: "Systém na evidenciu študentskej praxe",
    description: "Domovská stránka systému na evidenciu študentskej praxe",
    ogDescription: "Domovská stránka",
});
</script>

<template>
    <v-container fluid>
        <v-card>
            <v-card-text>
                <v-row>
                    <PageCard title="Informácie o odbornej praxi pre študentov"
                        description="Všetky informácie o predmete Oborná prax, ako ho absolvovať, pravidlá a zmluvy."
                        link="/info/student" />

                    <PageCard title="Informácie o odbornej praxi pre firmy"
                        description="Všetky informácie pre zamestnávateľov o zmluvách a pravidlách absolvovania odbornej praxe."
                        link="/info/company" />

                    <PageCard title="O aplikácii" description="Informácie o systéme ISOP." link="/about" />
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>
