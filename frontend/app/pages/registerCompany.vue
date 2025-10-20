<template>
    <v-container fluid>
        <v-card id="footer-card">
            <h1>O aplikácii</h1>

            <br />

            <p>Táto aplikácia slúži na zaznamenávanie a správu odbornej praxe študentov.</p>
            <p>Systém spravuje Fakulta prírodných vied a informatiky, UKF v Nitre.</p>

            <br />

            <p>Autori aplikácie:</p>
            <ul id="authors-list">
                <li>Bc. Sofia Reháková</li>
                <li>Bc. Veronika Fehérvíziová</li>
                <li>Bc. Fábián Varga</li>
                <li>Bc. Dávid Kecskés</li>
                <li>Bc. Andrej Kraslan</li>
            </ul>
        </v-card>
    </v-container>
</template>

<style scoped>
#footer-card {
    padding-left: 10px;
    padding-bottom: 10px;
}

#authors-list {
    margin-left: 20px;
}

#authors-list li {
    display: list-item;
    list-style-type: disc;
}
</style>
