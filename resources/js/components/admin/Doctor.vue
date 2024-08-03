<template>
    <Appbar />
    <v-container>
        <v-breadcrumbs :items="breadCrumbsItems">
            <template v-slot:divider>
            <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
        </v-breadcrumbs>
    </v-container>
    <v-container class="mt-n10">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-data-table :headers="headers" loading-text="Loading... Please wait" :items="filteredData" :items-per-page="pagination.rowsPerPage" :page.sync="pagination.page" :server-items-length="totalResults" class="elevation-0" :loading="isLoading">
                            <template v-slot:top>
                                <v-toolbar flat color="transparent">
                                    <v-toolbar-title>List of Doctors</v-toolbar-title>
                                    <v-spacer></v-spacer>
                                    <v-spacer></v-spacer>
                                    <v-text-field rounded color="primary" variant="outlined" v-model="searchQuery"  density="compact" label="Search by Name or Email" single-line hide-details/>
                                </v-toolbar>
                            </template>
                            <template v-slot:item.date="{ item }">{{ item.name }}</template>
                            <template v-slot:item.start_time="{ item }">{{ item.email }}</template>
                            <template v-slot:item.end_time="{ item }">+63 995 8111 348</template>
                            <template v-slot:item.created_at="{ item }">{{ formatDate(item.created_at) }}</template>
                            <template v-slot:item.actions="{ item }">
                                <!-- View Dialog -->
                                <v-dialog v-model="item.viewDialog" max-width="500" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon @click="viewItem(item)" variant="text" color="primary" v-bind="props">
                                            <v-icon>mdi-eye</v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card title="View Doctor's Profile" prepend-icon="mdi-calendar-cursor">
                                            <v-card-text>{{ item.name }}</v-card-text>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog>
                                <!-- Edit Dialog -->
                                <v-dialog v-model="item.editDialog" max-width="500" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon @click="editItem(item)" variant="text" color="warning" v-bind="props">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card title="Edit Doctor's Profile" prepend-icon="mdi-calendar-edit">
                                            <v-card-text>{{ item.name }}</v-card-text>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog>
                                <!-- Remove Dialog -->
                                <v-dialog v-model="item.deleteDialog" max-width="500" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon @click="deleteItem(item)" variant="text" color="red-darken-3" v-bind="props">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card title="Remove Doctor's Profile" prepend-icon="mdi-calendar-remove">
                                            <v-card-text>{{ item.name }}</v-card-text>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn size="small" class="text-capitalize" text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog>
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
    { title: 'Admin Dashboard', href: '/doctor/dashboard', disabled: false },
    { title: 'Doctor', href: '/doctor/dashboard', disabled: true },
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
    { title: 'Name', value: 'name', align: 'center' },
    { title: 'Email', value: 'email', align: 'center' },
    { title: 'Phone Number', value: 'phone_number', align: 'center' },
    { title: 'Joined', value: 'created_at', align: 'center' },
    { title: 'Actions', value: 'actions', sortable: false, align: 'center' }, // Added actions column
];

const fetchData = async () => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('adminToken');
        const response = await axios.get('/api/admin/doctor', {
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
            localStorage.removeItem('adminToken');
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
        item.name.toLowerCase().includes(search) ||
        item.email.toLowerCase().includes(search)
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
