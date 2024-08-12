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
                    <v-card-text class="py-0 mb-4">
                        <vue-number class="fs-50" v-model="item.number" v-bind="number"></vue-number>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue'
import { component as VueNumber } from '@coders-tm/vue-number-format'
import axios from 'axios';
import { onMounted, ref } from 'vue'

const breadCrumbsItems = ref([
    { title: 'Admin', href: '/admin/dashboard', disabled: false },
    { title: 'Dashboard', href: '/admin/dashboard', disabled: true },
])

const number = {
    decimal: '.',
    separator: ',',
    precision: 2,
    masked: false,
    disabled: true,
}
const count_patients = ref('')
const count_doctors = ref('')
const count_appointments = ref('')
const count_queues = ref('')

const cardItems = ref([
    { title: 'Total Patients', number: count_patients},
    { title: 'Total Doctors', number: count_doctors},
    { title: 'Total Appointments', number: count_appointments},
    { title: 'Completed Appointments', number: count_queues},
])

const countData = async () => {
    try {
        const token = localStorage.getItem('adminToken')
        const response = await axios.get('/api/admin/count', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        const data = response.data.data
        count_patients.value = data.user_count
        count_doctors.value = data.doctor_count
        count_appointments.value = data.appointment_count
        count_queues.value = data.queue_count
        console.log(data)
    } catch (error) {
        console.log(error)
    }
}

onMounted(() => {
    countData()
})
</script>

<style scoped>
.fs-50 {
    font-size: 26px;
}
</style>
