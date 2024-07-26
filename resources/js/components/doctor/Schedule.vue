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
        <v-row>
            <v-col xl="6" cols="6" class="text-start">
                <v-autocomplete v-model="searchQuery" class="mx-4" density="compact" menu-icon="" placeholder="Search Name or Email of a User" prepend-inner-icon="mdi-magnify" style="max-width: 500px;" theme="light" color="primary" variant="outlined" auto-select-first item-props></v-autocomplete>
            </v-col>
            <v-col xl="6" cols="6" class="mt-n2">
                <div class="text-end mx-4">
                    <v-btn-toggle v-model="toggle" variant="outlined" divided density="comfortable" class="mt-2">
                        <v-btn @click="setToggle('month')" class="px-3">
                            <v-icon v-if="toggle === 'month'" class="mx-2 fs-4" color="success">mdi-check</v-icon>
                                This Month
                        </v-btn>
                        <v-btn @click="setToggle('week')" class="px-3">
                            <v-icon v-if="toggle === 'week'" class="mx-2 fs-4" color="success">mdi-check</v-icon>
                                This Week
                        </v-btn>
                        <v-btn @click="setToggle('day')" class="px-3">
                            <v-icon v-if="toggle === 'day'" class="mx-2 fs-4" color="success">mdi-check</v-icon>
                                This Day
                        </v-btn>
                    </v-btn-toggle>
                </div>
            </v-col>
        </v-row>
        <template v-if="isFetching" >
            <div class="text-center" >
                <v-progress-circular  :size="70" :width="7" color="primary" :indeterminate="isFetching" justify="center" align-items="center" :active="isFetching"></v-progress-circular>
                <p class="text-h5">One moment please...</p>
            </div>
        </template>
        <template v-else>
            <template v-if="data !== null && data.length > 0">
                <v-table height="600" fixed-header class="mt-n5" >
                    <thead>
                        <template v-if="displayedData.length > 0">
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Start Time</th>
                                <th class="text-center">End Time</th>
                                <th class="text-center">Joined</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </template>
                    </thead>
                    <tbody class="text-center">
                        <template v-if="displayedData.length > 0">
                            <tr v-for="(item, index) in displayedData" :key="index" >
                                <td class="text-start">
                                    <v-list-item lines="two" prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg" class="" title="Al-Fhaigar Usman" subtitle="rubickking04@gmail.com"></v-list-item>
                                </td>
                                <td class="text-center">{{ formatDate(item.created_at) }}</td>
                                <td class="text-center">1:00 pm</td>
                                <td class="text-center">2:00 pm</td>
                                <td class="text-center">{{ formatDate(item.created_at) }}</td>
                                <td class="text-center">
                                    <v-dialog v-model="item.viewDialog" width="500" >
                                        <template v-slot:activator="{ props }">
                                            <v-btn icon="mdi-eye-outline" variant="text" color="blue" density="comfortable" v-bind="props"></v-btn>
                                        </template>
                                        <v-card class="rounded-xl">
                                            <v-toolbar color="white">
                                                <v-toolbar-title></v-toolbar-title>
                                                <v-btn icon dark @click="item.viewDialog = false">
                                                    <v-icon>mdi-close</v-icon>
                                                </v-btn>
                                            </v-toolbar>
                                            <v-container>
                                                <div class="text-center">
                                                    <v-avatar size="150">
                                                        <v-img src="/assets/images/avatar/avatar.jpg" alt="John"></v-img>
                                                    </v-avatar>
                                                </div>
                                            <v-card-title class="text-h5 text-center">{{ item.name }}</v-card-title>
                                            <v-card-text class="text-center">Email | {{ item.email }}</v-card-text>
                                            </v-container>
                                            <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <router-link :to="'/admin/students/profile/'+item._id">
                                                <v-btn color="green-darken-1" variant="text">View more</v-btn>
                                            </router-link>
                                            <v-btn color="red-darken-1" variant="text" @click="item.viewDialog = false">Close</v-btn>
                                            <v-spacer></v-spacer>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                    <v-dialog :width="500" v-model="item.isDialogVisible">
                                        <template v-slot:activator="{ props, on }">
                                            <v-btn v-bind="props" icon="mdi-delete-outline" color="danger" variant="text" density="comfortable"></v-btn>
                                        </template>
                                        <template v-slot:default="{ isActive }">
                                            <v-card :disabled="isLoading">
                                                <v-toolbar color="primary">
                                                    <v-toolbar-title>Delete this account?</v-toolbar-title>
                                                    <v-btn icon dark @click="item.isDialogVisible = false">
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
                                                    <v-btn text="Yes" prepend-icon="mdi-check" class="text-none" color="green-darken-1" :loading="isLoading" @click="deleteUsers(item.id)"></v-btn>
                                                    <v-btn text="No" prepend-icon="mdi-close" color="red-darken-1" class="text-none" @click="item.isDialogVisible = false"></v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </template>
                                    </v-dialog>
                                    <v-snackbar v-model="snackbar" color="success">{{ text }}</v-snackbar>
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <div class="col-lg-12 mb-3 " :class="isFetching ? 'd-none' : ''">
                                <div class="mb-3 py-4">
                                    <div class="text-center display-1">
                                        <v-icon>mdi-account-off-outline</v-icon>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fs-3 text-center">No Clients found yet.</h5>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </tbody>
                </v-table>
            </template>
            <template v-else >
                <div class="col-lg-12 mb-3 " :class="isFetching ? 'd-none' : ''">
                    <div class="mb-3 py-4">
                        <div class="text-center display-1">
                            <v-icon>mdi-account-off-outline</v-icon>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fs-3 text-center">No Users found today.</h5>
                        </div>
                    </div>
                </div>
            </template>
        </template>
        <v-row  class="mt-n5">
            <v-col cols="5">
                <p class="mt-6 text-disabled">Showing <span class="font-weight-bold">{{ startResultIndex + 1 }}</span> to <span class="font-weight-bold">{{ endResultIndex }}</span> of <span class="font-weight-bold">{{ totalResults }}</span> results</p>
            </v-col>
            <v-col cols="2">
                <div class="text-start mt-5">
                    <v-pagination rounded="circle" density="compact" v-model="currentPage" :length="totalPages" prev-icon="mdi-chevron-left" next-icon="mdi-chevron-right"></v-pagination>
                </div>
            </v-col>
            <v-col cols="5" class="text-end mt-6">
                <v-btn prepend-icon="mdi-plus" class="text-capitalize" color="primary">Add Schedule</v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import Appbar from './layouts/Appbar.vue'
import { ref, onMounted, watch, computed } from 'vue'
import dayjs from 'dayjs'

const breadCrumbsItems = ref([
    { title: 'Dashboard', href: '/doctor/dashboard', disabled: false },
    { title: 'Schedule', href: '/doctor/dashboard', disabled: true },
])

const data = ref([''])
const displayedData = ref([''])
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
        isLoading.value = true
        const token = localStorage.getItem('doctorToken')
        const response = await axios.get('/api/doctor/schedule', {
            header: {
                Authorization: `Bearer ${token}`
            }
        })
        data.value = response.data
        if (toggle.value === 'month') {
            data.value = data.value.filter(
                schedule => dayjs(schedule.created_at).isSame(new Date(), 'month')
            )
        } else if (toggle.value === 'week') {
            data.value = data.value.filter(
                schedule => dayjs(schedule.created_at).isSame(new Date(), 'week')
            )
        } else if (toggle.value === 'day') {
            data.value = data.value.filter(
                schedule => dayjs(schedule.created_at).isSame(new Date(), 'day')
            )
        }
        if (data.value.length === 0) {
            totalResults.value = data.value.length
            currentPage.value = 1
            displayedData = getDisplayedData()
        } else {
            totalResults.value = data.value.length
            currentPage.value = 1
            displayedData = getDisplayedData()
        }
        if (searchQuery.value) {
            const searchLower = searchQuery.value.toLowerCase()
            data.value = data.value.filter(
                schedule => {
                    return schedule.name.toLowerCase().includes(searchLower)
                }
            )
        }
        console.log(data.value)
    } catch (error) {
        if (error.response && error.response.status === 401) {
            snackbar.value = true
            text.value = "Error fetching your data. Please login again."
            localStorage.removeItem('doctorToken')
            setTimeout(() => {
                location.reload();
            }, 3000);
        }
    } finally {
        isFetching.value = false
    }
}

const getDisplayedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage
    startResultIndex.value = start
    endResultIndex.value = Math.min(end, totalResults.value)
    return users.slice(start, end)
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
