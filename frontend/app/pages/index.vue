<template>
    <v-container fluid>
        <v-card>
            <v-card-text>
                <v-row>
                    <PageCard title="Informácie o odbornej praxi pre študentov"
                        description="Všetky informácie o predmete Oborná prax, ako ho absolvovať, pravidlá a zmluvy."
                        link="/infoStudent" />

                    <PageCard title="Informácie o odbornej praxi pre firmy"
                        description="Všetky informácie pre zamestnávateľov o zmluvách a pravidlách absolvovania odbornej praxe."
                        link="/infoCompany" />

                    <PageCard title="O aplikácii" description="Informácie o systéme ISOP." link="/about" />

                    <PageCard title="Iné" description="Iný obsah." link="/" />
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>
