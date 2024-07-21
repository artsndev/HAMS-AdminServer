<template>
    <v-app-bar app :dark="$vuetify.theme.dark" :color="$vuetify.theme.dark ? 'blue-grey-darken-3' : 'blue-darken-4'" flat>
        <v-app-bar-nav-icon @click="toggleMenu"></v-app-bar-nav-icon>
        <v-toolbar-title :class="$vuetify.theme.dark ? 'd-none' : ' '">
            <v-img v-if="!$vuetify.theme.dark" class="me-auto" max-height="40" max-width="180" src="/assets/images/logo/dark_lettermark.png"></v-img>
        </v-toolbar-title>
        <v-toolbar-title :class="$vuetify.theme.dark ? '' : ' d-none'">
            <v-img v-if="$vuetify.theme.dark" class="me-auto" max-height="40" max-width="180" src="/assets/images/logo/dark_lettermark.png"></v-img>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="toggleTheme" :class="{ 'theme-animation': themeAnimating }" class="mx-1">
            <v-icon v-if="$vuetify.theme.dark" key="dark">mdi-weather-night</v-icon>
            <v-icon v-else key="light">mdi-weather-sunny</v-icon>
        </v-btn>
        <v-divider vertical inset class="d-none d-sm-flex"></v-divider>
        <v-list-item lines="two" prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg" class="mx-1 d-none d-sm-flex" title="John Doe" subtitle="johndoe@gmail.com">
            <template v-slot:append class="mx-n5">
                <v-icon icon="mdi-menu-down"></v-icon>
            </template>
        </v-list-item>
    </v-app-bar>
    <NavigationDrawer v-model="drawer" />
</template>

<script setup>
import { ref } from 'vue';
import NavigationDrawer from './NavigationDrawer.vue';

const drawer = ref(false);
const dark = ref(true);
const themeAnimating = ref(true);

const toggleTheme = () => {
    if (!themeAnimating.value) {
        themeAnimating.value = true;
        setTimeout(() => {
            dark.value = !dark.value;
            themeAnimating.value = false;
        }, 200);
    }
}

const toggleMenu = () => {
    drawer.value = !drawer.value;
};
</script>
