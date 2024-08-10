<template>
    <Appbar/>
    <v-container>
        <v-breadcrumbs :items="breadCrumbsItems">
            <template v-slot:divider>
                <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
        </v-breadcrumbs>
        <v-row>
            <v-col xl="3" cols="6" v-for="(item, index) in cardItems" :key="index">
                <v-card elevation="2" rounded="lg" color="blue-grey-lighten-5">
                    <template v-slot:title>
                        <span class="font-weight-black" color="blue">{{ item.title }}</span>
                    </template>
                    <!-- <v-card-title>{{ item.title }}</v-card-title> -->
                    <v-card-text class="py-0 mb-4">
                        <vue-number class="fs-50" v-model="item.number" v-bind="number"></vue-number>
                        <!-- <v-icon color="blue-darken-4" icon="mdi-chevron-right" class="mt-n10" size="40"></v-icon> -->
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue'
import { component as VueNumber } from '@coders-tm/vue-number-format'
import { ref } from 'vue'

const breadCrumbsItems = ref([
    { title: 'Doctor', href: '/doctor/dashboard', disabled: false },
    { title: 'Dashboard', href: '/doctor/dashboard', disabled: true },
])

const number = {
    decimal: '.',
    separator: ',',
    // prefix: '$',
    precision: 2,
    masked: false,
}
const count_patients = ref(10)
const count_appointments = ref(30)
const count_future_appointments = ref(5)
const count_completed_appointments = ref(24)

const cardItems = ref([
    { title: 'Total Schedule', number: count_patients.value},
    { title: ' Appointments', number: count_appointments.value},
    { title: 'Pending Appointments', number: count_future_appointments.value},
    { title: 'Completed Appointments', number: count_completed_appointments.value},
])
</script>

<style scoped>
.fs-50 {
    font-size: 26px;
}
</style>
