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
                            <template v-slot:item.doctor.name="{ item }">{{ item.doctor.name }}</template>
                            <template v-slot:item.doctor.email="{ item }">{{ item.doctor.email }}</template>
                            <template v-slot:item.doctor.specialization="{ item }">{{ item.doctor.specialization }}</template>
                            <template v-slot:item.schedule_time="{ item }">
                                <span>
                                    <span v-for="(part, index) in item.highlightedScheduleTime" :key="index">
                                        <template v-if="part.startsWith('<mark>')">
                                            <mark>{{ part.replace(/<\/?mark>/g, '') }}</mark>
                                        </template>
                                        <template v-else>
                                            {{ part }}
                                        </template>
                                    </span>
                                </span>
                            </template>
                            <template v-slot:item.deleted_at="{ item }">
                                <v-chip color="warning" v-if="item.deleted_at">Occupied</v-chip>
                                <v-chip color="success" v-else>Available</v-chip>
                            </template>
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
                                <!-- Remove Dialog -->
                                <v-dialog v-if="!item.deleted_at" v-model="item.deleteDialog" max-width="500" persistent>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon @click="deleteItem(item)" variant="text" color="red-darken-3" v-bind="props">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:default="{ isActive }">
                                        <v-card >
                                            <v-toolbar color="primary">
                                                <v-toolbar-title>Delete this schedule?</v-toolbar-title>
                                                <v-btn icon dark @click="isActive.value = false">
                                                    <v-icon>mdi-close</v-icon>
                                                </v-btn>
                                            </v-toolbar>
                                            <v-card-text>{{ message }}</v-card-text>
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
const message = ref('Are you sure to remove schedule?');

const pagination = ref({
    rowsPerPage: 10,
    page: 1,
});

const headers = [
    { title: 'Name', value: 'doctor.name', align: 'center' },
    { title: 'Email', value: 'doctor.email', align: 'center' },
    { title: 'Specialization', value: 'doctor.specialization', align: 'center' },
    { title: 'Schedule Time', value: 'schedule_time', align: 'center' },
    { title: 'Status', value: 'deleted_at', align: 'center' },
    { title: 'Posted Date', value: 'created_at', align: 'center' },
    { title: 'Actions', value: 'actions', sortable: false, align: 'center' },
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

const viewItem = (item) => {
    console.log('View item:', item);
};

const deleteItem = async (id) => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('doctorToken');
        const response = await axios.delete('/api/doctor/schedule/' + id, {
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

const formatDate = (dateTime) => {
    return dayjs(dateTime).format('dddd, MMMM D, YYYY hh:mm A');
};

const searchQuery = ref('');

const filteredData = computed(() => {
    if (!data.value) return [];
    const search = searchQuery.value.toLowerCase();
    return data.value.filter(item => {
            const scheduleTime = formatDate(item.schedule_time).toLowerCase();
            return scheduleTime.includes(search);
        }).map(item => {
            const scheduleTime = formatDate(item.schedule_time);
            let highlightedScheduleTime = [scheduleTime];
            if (search) {
                const searchRegex = new RegExp(`(${search})`, 'gi');
                highlightedScheduleTime = scheduleTime.split(searchRegex).map(part =>
                    searchRegex.test(part) ? `<mark>${part}</mark>` : part
                );
            }
        return {
            ...item,
            highlightedScheduleTime
        };
    });
});

watch([searchQuery, pagination], () => {
    pagination.value.page = 1;
});

onMounted(fetchData);
</script>

<style scoped>
.fs-50 {
    font-size: 26px;
}
</style>
