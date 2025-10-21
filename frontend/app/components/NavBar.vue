<script setup lang="ts">
import { Role } from '~/types/role';
import type { User } from '~/types/user';

const { logout } = useSanctumAuth();
const user = useSanctumUser<User>();

const color = computed(() => {
    switch (user.value?.role) {
        case Role.ADMIN:
            return 'rgb(211, 47, 47)';
        case Role.STUDENT:
            return 'rgb(46, 125, 50)';
        case Role.EMPLOYER:
            return 'rgb(25, 118, 210)';
        default:
            return 'rgb(46, 125, 50)';
    }
});
</script>

<template>
    <div>
        <v-app-bar :color="color" style="color: white" :elevation="2">
            <v-btn variant="text" dense to="/">Domov</v-btn>

            <v-btn v-show="user === null" variant="text" dense to="/register">Register</v-btn>
            <v-btn v-show="user === null" variant="text" dense to="/login">Login</v-btn>

            <v-btn v-show="user" variant="text" dense to="/dashboard">Dashboard</v-btn>
            <v-btn v-show="user" variant="text" dense @click="logout">Logout</v-btn>
            <v-spacer></v-spacer>
        </v-app-bar>
    </div>
</template>