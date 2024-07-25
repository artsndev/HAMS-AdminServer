<template>
    <Appbar/>
    <v-container>
        <v-breadcrumbs :items="breadCrumbsItems">
            <!-- <template v-slot:prepend>
                <v-icon size="x-large" icon="mdi-home-outline"></v-icon>
            </template> -->
            <template v-slot:divider>
                <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
        </v-breadcrumbs>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue';
import { ref } from 'vue'

const breadCrumbsItems = ref([
    { title: 'Home', href: '/admin/dashboard', disabled: false },
    { title: 'Dashboard', href: '/admin/dashboard', disabled: true },
])
</script>
