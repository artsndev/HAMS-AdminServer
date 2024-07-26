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
        <v-list-item lines="two" prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg" class="mx-1 d-none d-sm-flex" :title="name" :subtitle="email">
            <template v-slot:append class="mx-n5">
                <v-icon icon="mdi-menu-down"></v-icon>
            </template>
        </v-list-item>
    </v-app-bar>
    <NavigationDrawer v-model="drawer" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import NavigationDrawer from './NavigationDrawer.vue';

const drawer = ref(true);
const toggleMenu = () => {
    drawer.value = !drawer.value;
}

const name = ref('')
const email = ref('')
const getUser = async () => {
    try {
        const token = localStorage.getItem('doctorToken')
        const response = await axios.get('/api/doctor/data', {
            headers: {
                Authorization: `Bearer ${token}`
            },
        })
        name.value = response.data.name
        email.value = response.data.email
    } catch (error) {
        console.log(error)
    }
}
onMounted(() => {
    getUser()
})
</script>
