<template>
    <Appbar />
    <v-container>
        <v-breadcrumbs :items="breadCrumbsItems">
            <template v-slot:divider>
            <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
        </v-breadcrumbs>
    </v-container>
    <v-container class="mt-n5">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-card>
                    <v-card-text>
                        <v-data-table :headers="headers" :items="filteredData" :items-per-page="pagination.rowsPerPage" :page.sync="pagination.page" :server-items-length="totalResults" class="elevation-1" :loading="isLoading">
                        <template v-slot:top>
                            <v-toolbar flat>
                                <v-toolbar-title>Schedule</v-toolbar-title>
                                <v-spacer></v-spacer>
                                <v-spacer></v-spacer>
                                <v-text-field class="me-5" rounded color="primary" variant="outlined" v-model="searchQuery"  density="compact" label="Search by Time, Date and Year" single-line hide-details/>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.date="{ item }">{{ formatDate(item.date) }}</template>
                        <template v-slot:item.start_time="{ item }">{{ formatDate(item.start_time) }}</template>
                        <template v-slot:item.end_time="{ item }">{{ item.end_time }}</template>
                        <template v-slot:item.created_at="{ item }">{{ formatDate(item.created_at) }}</template>
                        <template v-slot:item.actions="{ item }">
                            <v-dialog v-model="item.viewDialog" max-width="500">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon @click="viewItem(item)" variant="text" color="primary" v-bind="props">
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                </template>
                                <template v-slot:default="{ isActive }">
                                    <v-card title="Dialog">
                                        <v-card-text>{{ formatDate(item.date) }}</v-card-text>
                                        <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </template>
                            </v-dialog>
                            <v-btn icon @click="editItem(item)" variant="text" color="warning">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="deleteItem(item)" variant="text" color="red-darken-3">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        <template v-slot:no-data>
                            <v-alert type="info" :value="true">No data available</v-alert>
                        </template>
                        </v-data-table>
                    </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';

const breadCrumbsItems = ref([
    { title: 'Dashboard', href: '/doctor/dashboard', disabled: false },
    { title: 'Schedule', href: '/doctor/dashboard', disabled: true },
]);

const data = ref([]); // Initialize as an empty array
const searchQuery = ref('');
const isLoading = ref(false);
const snackbar = ref(false);
const text = ref('');
const message = ref('Are you sure to remove this account from the system?');

const pagination = ref({
    rowsPerPage: 10,
    page: 1,
});

const headers = [
    { text: 'Date', value: 'date' },
    { text: 'Start Time', value: 'start_time' },
    { text: 'End Time', value: 'end_time' },
    { text: 'Start Date', value: 'created_at' },
    { text: 'Actions', value: 'actions', sortable: false }, // Added actions column
];

const fetchData = async () => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('doctorToken');
        const response = await axios.get('/api/doctor/schedule', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        data.value = response.data.data
        console.log(data.value)
    } catch (error) {
        console.error('Error fetching data:', error);
        if (error.response && error.response.status === 401) {
            snackbar.value = true;
            text.value = 'Error fetching your data. Please try again.';
            localStorage.removeItem('doctorToken');
            setTimeout(() => {
                location.reload();
            }, 3000);
        }

    } finally {
        isLoading.value = false;
    }
};

const formatDate = (dateTime) => {
    return dayjs(dateTime).format('dddd, MMMM D, YYYY hh:mm A');
};

// dayjs.extend(localizedFormat)

const formatTime = (time) => {
    return dayjs(time).format('LT');
};

const editItem = (item) => {
    console.log('Edit item:', item);
  // Implement edit functionality here
};

const viewItem = (item) => {
    console.log('View item:', item);
  // Implement view functionality here
};

const deleteItem = (item) => {
    console.log('Delete item:', item);
  // Implement delete functionality here
};

const totalResults = computed(() => {
    return data.value.length;
});

const filteredData = computed(() => {
  if (!data.value) return []; // Ensure data is defined before filtering
    const search = searchQuery.value.toLowerCase();
    return data.value.filter(item =>
        item.date.toLowerCase().includes(search) ||
        item.start_time.toLowerCase().includes(search) ||
        item.end_time.toLowerCase().includes(search) ||
        item.created_at.toLowerCase().includes(search)
    );
});

watch([searchQuery, pagination], () => {
  pagination.value.page = 1; // Reset to the first page on search
});

onMounted(fetchData);
</script>

<style scoped>
.fs-50 {
    font-size: 26px;
}
</style>
