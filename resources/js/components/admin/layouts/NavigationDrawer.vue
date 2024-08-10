<template>
    <v-navigation-drawer dark border="0" :dark="$vuetify.theme.dark" :color="$vuetify.theme.dark ? 'blue-grey-darken-3' : 'blue-darken-4'" active>
        <template v-slot:append>
            <div class="text-center">
                <p class="text-grey fs-14">© 2024 QuirkyQuarks Squadrons</p>
                <p class="text-grey mb-2 fs-14"> All rights reserved.</p>
            </div>
        </template>
        <v-list density="compact" nav :color="$vuetify.theme.dark ? 'blue-darken-3' : 'blue-lighten-1'" class="mt-5">
            <v-list-subheader class="text-uppercase font-weight-black text-caption" :class="!$vuetify.theme.dark ? 'text-white' : 'text-white'">Menu</v-list-subheader>
            <v-list-item v-for="(item, i) in items" :key="i" :value="item" class="fs-5" :class="{'active': isRouteActive(item.routeName)}">
                <template v-slot:prepend>
                    <v-icon :icon="item.icon" size="33"></v-icon>
                </template>
                <router-link :to="{ name: item.routeName }" class="text-decoration-none text-dark">
                    <v-list-item-title v-text="item.text" :class="!$vuetify.theme.dark ? 'text-white' : 'text-white'"></v-list-item-title>
                </router-link>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item prepend-icon="mdi-location-exit" title="Sign out" value="logout" class="ms-1 mt-1 fs-5" @click="logout"></v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const items = ref([
    { icon: 'mdi-chart-donut', text: 'Dashboard', routeName: 'Admin Dashboard' },
    { icon: 'mdi-clipboard-text-multiple-outline', text: 'Appointments', routeName: 'Admin Appointment'},
    { icon: 'mdi-doctor', text: 'Doctors', routeName: 'Admin Doctor'},
    { icon: 'mdi-account-multiple-outline', text: 'Users', routeName: 'Admin User'},
    // { icon: 'mdi-clipboard-check-multiple-outline', text: 'Queued', routeName: 'Admin Queue'},
]);

const router = useRouter();
const isRouteActive = (routeName) => {
    return routeName === router.currentRoute.value.name;
};

const logout = async () => {
    try {
        const token = localStorage.getItem('adminToken');
        await axios.post('/api/admin/logout', null, {
            headers: {
                Authorization: `Bearer ${token}`
            },
        });
        localStorage.removeItem('adminToken');
        router.push({ name: 'Admin Login' });
    } catch (error) {
        console.error(error);
    }
};
</script>

<style>
.fs-14{
    font-size: 14px;
}
</style>
