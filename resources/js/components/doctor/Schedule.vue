<template>
    <Appbar/>
    <v-container>
        <v-breadcrumbs :items="breadCrumbsItems">
            <template v-slot:divider>
                <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
        </v-breadcrumbs>
    </v-container>
    <v-container class="mt-n3">
        <!-- Display fetched data -->
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>Fetched Data</v-card-title>
                        <v-card-text>
                            <!-- Iterate over data and display each item -->
                            <v-list>
                                <v-list-item-group v-if="data.length">
                                    <v-list-item v-for="(item, index) in data" :key="index">
                                        <v-list-item-content>
                                            <v-list-item-title>{{ item.name }}</v-list-item-title>
                                            <v-list-item-subtitle>{{ item.email }}</v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list-item-group>
                                <v-list-item v-else>
                                    <v-list-item-content>
                                        <v-list-item-title>No data available</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue'
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import dayjs from 'dayjs'

const breadCrumbsItems = ref([
    { title: 'Dashboard', href: '/doctor/dashboard', disabled: false },
    { title: 'Schedule', href: '/doctor/dashboard', disabled: true },
])

const data = ref([]) // Initialize as an empty array
const displayedData = ref([])
const searchQuery = ref(null)
const isDialogVisible = ref(false)
const isLoading = ref(false)
const snackbar = ref(false)
const intervalId = ref(null)
const isFetching = ref(false)
const toggle = ref(null)
const text = ref('')
const message = ref('Are you sure to remove this account from the system?')

const fetchData = async () => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('doctorToken');
        const response = await axios.get('/api/doctor/schedule', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        data.value = response.data.data.schedule;
        displayedData.value = getDisplayedData(); // Use ref properly

        console.log(data.value);
    } catch (error) {
        if (error.response && error.response.status === 401) {
            snackbar.value = true;
            text.value = "Error fetching your data. Please login again.";
            // localStorage.removeItem('doctorToken');
            // setTimeout(() => {
            //     location.reload();
            // }, 3000);
        }
    } finally {
        isLoading.value = false;
    }
};

const getDisplayedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    startResultIndex.value = start
    endResultIndex.value = Math.min(end, totalResults.value)
    return data.value.slice(start, end) // Use data.value
})

const formatDate = (dateTime) => {
    return dayjs(dateTime).format('dddd, MMMM D, YYYY h:mm A')
}

const setToggle = (value) => {
    toggle.value = toggle.value === value ? null : value;
}

const startResultIndex = ref(0)
const endResultIndex = ref(0)
const totalResults = ref(0)
const currentPage = ref(1)
const itemsPerPage = ref(10)

const totalPages = computed(() => {
    return Math.ceil(data.value.length / itemsPerPage.value)
})

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.fs-50 {
    font-size: 26px;
}
</style>
