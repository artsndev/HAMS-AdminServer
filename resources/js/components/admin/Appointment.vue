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
                                    <v-toolbar-title>List of Pending Appointments</v-toolbar-title>
                                    <v-spacer></v-spacer>
                                    <v-spacer></v-spacer>
                                    <v-text-field rounded color="primary" variant="outlined" v-model="searchQuery"  density="compact" label="Search by Name or Email" single-line hide-details/>
                                </v-toolbar>
                            </template>
                            <template v-slot:item.user.name="{ item }">{{ item.user.name }}</template>
                            <template v-slot:item.user.email="{ item }">{{ item.user.email }}</template>
                            <template v-slot:item.user.phone_number="{ item }">{{ item.user.phone_number }}</template>
                            <template v-slot:item.appointment_time="{ item }">{{ formatDate(item.appointment_time) }}</template>
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
                                        <v-card title="View Patient's Profile" prepend-icon="mdi-badge-account-horizontal-outline">
                                            <v-card-text>
                                                <div class="text-center">
                                                    <v-avatar size="100" class="mx-auto">
                                                        <img src="https://randomuser.me/api/portraits/women/85.jpg" alt="Avatar" style="object-fit: cover; width: 100%; height: 100%;">
                                                    </v-avatar>
                                                    <h2 class="mx-auto font-weight-regular mt-3">{{ item.user.name }}</h2>
                                                    <p class="mx-auto text-grey font-weight-regular">{{ item.user.email }}</p>
                                                </div>
                                                <v-list  nav >
                                                    <v-list-item>
                                                        <p class="fs-10 mb-5">Details</p>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                            <v-icon>mdi-at</v-icon>
                                                            <span class="mx-2">{{ item.user.email }}</span>
                                                        </v-list-item-title>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                                <v-icon>mdi-cake-variant-outline</v-icon>
                                                            <span class="mx-2">{{ item.user.birthdate }}</span>
                                                        </v-list-item-title>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                            <v-icon>mdi-phone-outline</v-icon>
                                                            <span class="mx-2">{{ item.user.phone_number }}</span>
                                                        </v-list-item-title>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                            <v-icon>mdi-map-marker-radius-outline</v-icon>
                                                            <span class="mx-2 text-wrap mb-2">{{ item.user.address }}</span>
                                                        </v-list-item-title>
                                                    </v-list-item>
                                                </v-list>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog>
                                <!-- Edit Dialog -->
                                <v-dialog v-model="item.editDialog" max-width="550" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon @click="editItem(item)" variant="text" color="warning" v-bind="props">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card title="View Appointment Details" prepend-icon=" mdi-clipboard-text-multiple-outline">
                                            <v-card-text>
                                                <v-form>
                                                    <v-row>
                                                        <v-col xl="6">
                                                            <v-text-field readonly="true" density="compact" color="primary" :model-value="item.user.name" variant="outlined" label="Patient's Name"></v-text-field>
                                                        </v-col>
                                                        <v-col xl="6">
                                                            <v-text-field readonly="true" density="compact" color="primary" :model-value="item.user.email" variant="outlined" label="Patient's Email"></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                    <v-text-field readonly="true" density="compact" color="primary" :model-value="item.user.address" variant="outlined" label="Patient's Address"></v-text-field>
                                                    <v-row>
                                                        <v-col xl="6">
                                                            <v-text-field readonly="true" density="compact" color="primary" :model-value="item.user.phone_number" variant="outlined" label="Patient's Phone Number"></v-text-field>
                                                        </v-col>
                                                        <v-col xl="6">
                                                            <v-text-field readonly="true" density="compact" color="primary" :model-value="item.user.birthdate" variant="outlined" label="Patient's Birth of Date"></v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                    <v-text-field readonly="true" density="compact" color="primary" :model-value="formatDate(item.appointment_time)" variant="outlined" label="Appointment Schedule"></v-text-field>
                                                    <v-textarea readonly="true" density="compact" color="primary" :model-value="item.session_of_appointment" variant="outlined" rows="2" label="Session of Appointment"></v-textarea>
                                                    <v-textarea readonly="true" density="compact" color="primary" :model-value="item.purpose_of_appointment" variant="outlined" rows="2" label="Purpose of Appointment"></v-textarea>
                                                </v-form>
                                            </v-card-text>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog>
                                <!-- Remove Dialog -->
                                <!-- <v-dialog v-model="item.deleteDialog" max-width="500" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon @click="deleteItem(item)" variant="text" color="red-darken-3" v-bind="props">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card title="Remove Doctor's Profile" prepend-icon="mdi-calendar-remove">
                                            <v-card-text>{{ item.user.name }}</v-card-text>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn size="small" class="text-capitalize" text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog> -->
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

const breadCrumbsItems = ref([
    { title: 'Admin Dashboard', href: '/admin/dashboard', disabled: false },
    { title: 'Appointment', href: '/doctor/appointment', disabled: true },
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
    { title: 'Name', value: 'user.name', align: 'center' },
    { title: 'Email', value: 'user.email', align: 'center' },
    { title: 'Phone Number', value: 'user.phone_number', align: 'center' },
    { title: 'Schedule', value: 'appointment_time', align: 'center' },
    { title: 'Created', value: 'created_at', align: 'center' },
    { title: 'Actions', value: 'actions', sortable: false, align: 'center' }, // Added actions column
];

const fetchData = async () => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('adminToken');
        const response = await axios.get('/api/admin/appointment', {
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

// const deleteItem = (item) => {
//     console.log('Delete item:', item);
// };

const totalResults = computed(() => {
    return data.value.length;
});

const filteredData = computed(() => {
    if (!data.value) return []; // Ensure data is defined before filtering
    const search = searchQuery.value.toLowerCase();

    return data.value.filter(item =>
        item.appointment_time.toLowerCase().includes(search) ||
        item.purpose_of_appointment.toLowerCase().includes(search) ||
        (item.user && (
            item.user.name.toLowerCase().includes(search) ||
            item.user.email.toLowerCase().includes(search)
        ))
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
