<template>
    <Appbar/>
    <v-container>
        <v-breadcrumbs :items="breadCrumbsItems">
            <template v-slot:divider>
            <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
        </v-breadcrumbs>
    </v-container>
    <v-container class="mt-n16">
        <v-container>
            <v-dialog v-model="add" max-width="550">
                <template v-slot:activator="{ props: activatorProps }">
                    <div class="text-end mb-8 mt-n8">
                        <v-btn class="text-capitalize" v-bind="activatorProps" prepend-icon="mdi-plus" color="primary" rounded="lg">Add Announcement</v-btn>
                    </div>
                </template>
                <template v-slot:default="{ isActive }">
                    <v-card rounded="lg">
                        <v-toolbar color="white">
                            <v-toolbar-title>Add New Announcement</v-toolbar-title>
                            <v-btn icon dark @click="isActive.value = false">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-toolbar>
                        <v-card-text>
                            <v-form @submit.prevent="addAnnouncement">
                                <v-text-field v-model="form.title" :error-messages="title_error" label="Title" variant="outlined" color="primary" density="compact"></v-text-field>
                                <v-textarea v-model="form.body" :error-messages="body_error" label="What's on your mind?" rows="3" auto-grow variant="outlined" color="primary" density="compact"></v-textarea>
                                <v-btn type="submit" color="primary">Submit</v-btn>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </template>
            </v-dialog>
            <v-row>
                <v-col xl="3" v-for="(item, index) in data" :key="index">
                    <v-card rounded="xl" class="mb-1" elevation="5" height="150">
                        <!-- <v-img height="150" src="https://cdn.vuetifyjs.com/images/cards/cooking.png" gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)" cover></v-img> -->
                        <v-row class="mx-1 mt-1">
                            <v-col cols="9">
                                <v-chip  size="x-small" color="grey-darken-3 text-uppercase">Announcement</v-chip>
                            </v-col>
                            <v-col cols="3" class="mt-n1 text-end">
                                <v-btn icon="mdi-trash-can-outline" density="comfortable" variant="flat"></v-btn>
                            </v-col>
                        </v-row>
                        <v-list lines="one">
                            <v-list-item class="mt-n3 mb-3">
                                <template v-slot:default>
                                    <v-list-item-content>
                                        <v-list-item-title class="fs-26">{{ item.title }}</v-list-item-title>
                                        <v-list-item-subtitle class="fs-16">{{ item.body }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-container>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import Appbar from './layouts/Appbar.vue'

const breadCrumbsItems = ref([
    { title: 'Admin Dashboard', href: '/admin/dashboard', disabled: false },
    { title: 'Announcement', href: '/doctor/announcement', disabled: true },
]);
const add = ref(false)

const data = ref([])

const fetchData = async () => {
    try {
        const token = localStorage.getItem('adminToken')
        const response = await axios.get('/api/admin/post', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        data.value = response.data.data
    } catch (error) {
        console.log(error)
    }
}

const title_error = ref('')
const body_error = ref('')

const timer = ref(null)

const form = reactive({
    title: '',
    body: '',
})

const addAnnouncement = async () => {
    try {
        const formData = new FormData()
        formData.append('title', form.title)
        formData.append('body', form.body)
        const token = localStorage.getItem('adminToken')
        const response = await axios.post('/api/admin/post', formData, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        const clearValidationError = () => {
            title_error.value = ''
            body_error.value = ''
        }
        const setValidationError = () => {
            clearValidationError()
            timer.value = setTimeout(() => {
                title_error.value = response.data.errors.title
                body_error.value = response.data.errors.body
            }, 1)
            setTimeout(() => {
                clearValidationError();
            }, 10000);
        }
        if (response.data.success) {
            add.value = false
            fetchData();
        } else {
            setValidationError()
        }
    } catch (error) {
        console.log(error)
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.fs-26{
    font-size: 26px;
}
.fs-16{
    font-size: 16px;
}
</style>
