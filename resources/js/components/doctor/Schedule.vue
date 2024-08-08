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
                                    <v-toolbar-title>My Schedule</v-toolbar-title>
                                    <v-spacer></v-spacer>
                                    <v-spacer></v-spacer>
                                    <v-text-field rounded color="primary" variant="outlined" v-model="searchQuery"  density="compact" label="Search by Time, Date and Year at Schedule Time" single-line hide-details/>
                                </v-toolbar>
                            </template>
                            <template v-slot:item.schedule_time="{ item }">{{ formatDate(item.schedule_time) }}</template>
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
                                        <v-card title="View Schedule" prepend-icon="mdi-calendar-cursor">
                                            <v-card-text>{{ formatDate(item.schedule_time) }}</v-card-text>
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
                                        <v-card title="Edit Schedule" prepend-icon="mdi-calendar-edit">
                                            <v-card-text>{{ formatDate(item.schedule_time) }}</v-card-text>
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
                                        <v-card title="Remove Schedule" prepend-icon="mdi-calendar-remove">
                                            <v-card-text>{{ formatDate(item.schedule_time) }}</v-card-text>
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
                            <div class="text-end">
                                <!-- Add Data Dialog -->
                                <v-dialog v-model="addScheduledialog" max-width="500" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn class="text-capitalize" size="small" color="primary" prepend-icon="mdi-plus" v-bind="props">Add Schedule</v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card title="Add a Schedule" prepend-icon="mdi-calendar-plus">
                                            <v-card-text>Add Schedule</v-card-text>
                                            <v-container>
                                                <v-form @submit.prevent="addSchedule">
                                                    <v-text-field :error-messages="schedule_time_error" type="datetime-local" v-model="form.schedule_time" density="compact" label="Schedule" variant="outlined"></v-text-field>
                                                    <v-btn type="submit" color="primary" class="text-capitalize mt-2">Submit</v-btn>
                                                </v-form>
                                            </v-container>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </template>
                                </v-dialog>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue';
import { ref, reactive, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';

const breadCrumbsItems = ref([
    { title: 'Dashboard', href: '/doctor/dashboard', disabled: false },
    { title: 'Schedule', href: '/doctor/dashboard', disabled: true },
]);

const data = ref([]);

const isLoading = ref(false);
const snackbar = ref(false);
const text = ref('');
const message = ref('Are you sure to remove this account from the system?');

const pagination = ref({
    rowsPerPage: 10,
    page: 1,
});

const headers = [
    { title: 'Schedule Time', value: 'schedule_time', align: 'center' },
    { title: 'Posted Date', value: 'created_at', align: 'center' },
    { title: 'Actions', value: 'actions', sortable: false, align: 'center' }, // Added actions column
];

const schedule_time_error = ref('')

const timer = ref(null)

const form = reactive({
    schedule_time: ''
})

const addScheduledialog = ref(false)

const addSchedule = async () => {
    try {
        const formData = new FormData();
        formData.append('schedule_time', form.schedule_time);
        const token = localStorage.getItem('doctorToken')
        const response = await axios.post('/api/doctor/schedule', formData, {
            headers: {
                'Authorization': `Bearer ${token}`
            },
        })
        const clearValidationError = () => {
            schedule_time_error.value = ''
        }
        const setValidationError = () => {
            clearValidationError()
            timer.value = setTimeout(() => {
                schedule_time_error.value = response.data.errors.schedule_time
            }, 1)
            setTimeout(() => {
                clearValidationError();
            }, 10000);
        }
        if (response.data.success) {
            addScheduledialog.value = false
            fetchData();
        } else {
            setValidationError()
        }
    } catch (error) {
        console.log(error)
    }
}


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

const formatDate = (dateTime) => {
    return dayjs(dateTime).format('dddd, MMMM D, YYYY hh:mm A');
};

const searchQuery = ref('');

const filteredData = computed(() => {
    if (!data.value) return [];
    const search = searchQuery.value.toLowerCase();
    return data.value.filter(item =>
        formatDate(item.schedule_time).toLowerCase().includes(search)
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
