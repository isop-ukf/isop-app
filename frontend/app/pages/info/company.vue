<template>
    <h2 class="page-container page-title">Detaily a pravidlá odbornej praxe pre firmy</h2>
    <p class="page-container page-lead">
        Kompletné informácie o odbornej praxi pre firmy
    </p>

    <br />

    <h3 class="page-container section-title">Zmluvné podmienky</h3>

    <v-row class="pc" align="stretch" justify="start">
        <PageCard title="Zmluva o odbornej praxi" description="Medzi firmou a študentom sa uzatvára Zmluva o odbornej praxi, ktorá definuje práva a povinnosti oboch strán. 
      Zmluva musí byť v súlade s legislatívou a internými predpismi školy." link="/"
            icon="mdi mdi-file-document-edit-outline" />
    </v-row>

    <h3 class="page-container section-title">Pravidlá a povinnost počas praxe</h3>

    <v-row class="pc" align="stretch" justify="start">
        <PageCard title="Pridelenie mentora"
            description="Každému študentovi musí byť pridelený zodpovedný zamestnanec (mentor), ktorý ho bude viesť, zadávať mu úlohy a poskytovať spätnú väzbu."
            link="/" icon="mdi mdi-account-multiple-outline" />
        <PageCard title="Relevantné úlohy"
            description="Náplň práce študenta musí zodpovedať jeho študijnému odboru a umožniť mu získať praktické zručnosti a vedomosti."
            link="/" icon="mdi mdi-notebook-check-outline" />
        <PageCard title="Bezpečnosť pri práci"
            description="Firma je povinná zabezpečiť preškolenie o bezpečnosti a ochrane zdravia pri práci (BOZP) a poskytnúť potrebné ochranné pomôcky."
            link="/" icon="mdi mdi-shield-outline" />
    </v-row>

    <h3 class="page-container section-title">Hodnotenie a ukončenie praxe</h3>

    <div class="ac">
        <v-expansion-panels variant="accordion" class="faq" elevation="0" rounded="lg">
            <v-expansion-panel>
                <v-expansion-panel-title>
                    Ako prebieha hodnotenie študenta?
                </v-expansion-panel-title>
                <v-expansion-panel-text>
                    Študenta hodnotí mentor vo firme a zodpovedná osoba na škole podľa
                    stanovených kritérií (účasť, splnené úlohy, kvalita výstupov a pod.).
                </v-expansion-panel-text>
            </v-expansion-panel>

            <v-expansion-panel>
                <v-expansion-panel-title>
                    Aké dokumenty je potrebné vystaviť po ukončení praxe?
                </v-expansion-panel-title>
                <v-expansion-panel-text>
                    Typicky potvrdenie o absolvovaní praxe a hodnotiaci formulár
                    podpísaný mentorom. Presný zoznam dodá škola.
                </v-expansion-panel-text>
            </v-expansion-panel>
        </v-expansion-panels>
    </div>


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

.ac {
    margin-left: 24px;
    margin-bottom: 50px;
    max-width: 700px;
}

.faq :deep(.v-expansion-panel) {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
}

.faq :deep(.v-expansion-panel:not(:first-child)) {
    margin-top: 12px;
}

.faq :deep(.v-expansion-panel-title) {
    font-weight: 600;
}
</style>
