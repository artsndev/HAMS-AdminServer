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
                                    <v-toolbar-title>List of Users</v-toolbar-title>
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
                                        <v-card title="View Patient's Profile" prepend-icon="mdi-badge-account-horizontal-outline">
                                            <v-card-text>
                                                <div class="text-center">
                                                    <v-avatar size="100" class="mx-auto">
                                                        <img src="https://randomuser.me/api/portraits/women/85.jpg" alt="Avatar" style="object-fit: cover; width: 100%; height: 100%;">
                                                    </v-avatar>
                                                    <h2 class="mx-auto font-weight-regular mt-3">{{ item.name }}</h2>
                                                    <p class="mx-auto text-grey font-weight-regular">{{ item.email }}</p>
                                                </div>
                                                <v-list  nav >
                                                    <v-list-item>
                                                        <p class="fs-10 mb-5">Details</p>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                            <v-icon>mdi-at</v-icon>
                                                            <span class="mx-2">{{ item.email }}</span>
                                                        </v-list-item-title>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                                <v-icon>mdi-cake-variant-outline</v-icon>
                                                            <span class="mx-2">{{ item.birthdate }}</span>
                                                        </v-list-item-title>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                            <v-icon>mdi-phone-outline</v-icon>
                                                            <span class="mx-2">{{ item.phone_number }}</span>
                                                        </v-list-item-title>
                                                        <v-list-item-title class="font-weight-medium fs-10 mb-2">
                                                            <v-icon>mdi-map-marker-radius-outline</v-icon>
                                                            <span class="mx-2 text-wrap mb-2">{{ item.address }}</span>
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
                                        <v-card >
                                            <v-toolbar color="primary">
                                                <v-toolbar-title>Delete this account?</v-toolbar-title>
                                                <v-btn icon dark @click="isActive.value = false">
                                                    <v-icon>mdi-close</v-icon>
                                                </v-btn>
                                            </v-toolbar>
                                            <v-card-text>{{ message }}</v-card-text>
                                            <v-card-text>
                                                <v-alert v-model="item.alert" border="start" variant="tonal" color="blue-grey-darken-1" class="text-medium-emphasis text-caption mb-2">
                                                Note: If you will delete this account, all of his data will be removed from the system.
                                                </v-alert>
                                            </v-card-text>
                                            <v-divider></v-divider>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text="Yes" prepend-icon="mdi-check" class="text-none" color="green-darken-1" :loading="isLoading" @click="deleteItem(item.id)"></v-btn>
                                                <v-btn text="No" prepend-icon="mdi-close" color="red-darken-1" class="text-none" @click="isActive.value = false"></v-btn>
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
        <v-snackbar :timeout="5000" v-model="snackbar" color="success">
            <v-icon icon="mdi-check" class="px-2"></v-icon>
            {{ text }}
        </v-snackbar>
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
    { title: 'User', href: '/doctor/dashboard', disabled: true },
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
        const response = await axios.get('/api/admin/user', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        data.value = response.data.data
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

const deleteItem = async (userId) => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('adminToken');
        const response = await axios.delete('/api/admin/user/' + userId, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        snackbar.value = true
        text.value = 'Deleted Successfully'
        fetchData();
    } catch (error) {
        console.error('Error deleting user:', error);
    } finally {
        isLoading.value = false;
    }
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
