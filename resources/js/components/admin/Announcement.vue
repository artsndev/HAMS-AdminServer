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
                    <div class="text-end mb-8 mt-n7">
                        <v-btn class="text-capitalize" size="small" v-bind="activatorProps" prepend-icon="mdi-plus" color="primary" rounded="lg">Add Announcement</v-btn>
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
                <v-col xl="3" cols="12" v-for="(item, index) in data" :key="index">
                    <v-dialog v-model="item.viewAnnouncementDialog" width="600">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-card rounded="xl" class="mb-1" :ripple="false" elevation="5" height="150" v-bind="activatorProps" @click="viewDialog(item.id)">
                                <v-row class="mx-1 mt-1">
                                    <v-col cols="9">
                                        <v-chip size="x-small" color="grey-darken-3" text-uppercase>Announcement</v-chip>
                                    </v-col>
                                    <v-col cols="3" class="mt-n1 text-end">
                                        <v-dialog v-model="item.deleteDialog" width="500">
                                            <template v-slot:activator="{ props: activator }">
                                                <v-btn icon="mdi-trash-can-outline" density="comfortable" v-bind="activator" variant="flat"></v-btn>
                                            </template>
                                            <template v-slot:default="{ isActive }">
                                                <v-card rounded="xl">
                                                    <v-toolbar color="transparent">
                                                        <v-toolbar-title>Delete this announcement?</v-toolbar-title>
                                                        <v-btn icon dark @click="item.deleteDialog = false">
                                                            <v-icon>mdi-close</v-icon>
                                                        </v-btn>
                                                    </v-toolbar>
                                                    <v-card-text>
                                                        Are you sure you want to delete this announcement?
                                                    </v-card-text>
                                                    <v-card-actions>
                                                        <v-spacer></v-spacer>
                                                        <v-btn text="Cancel" @click="item.deleteDialog = false"></v-btn>
                                                        <v-btn text="Delete" :loading="isLoading" color="red" @click="deleteItem(item.id)"></v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </template>
                                        </v-dialog>
                                    </v-col>
                                </v-row>
                                <v-list lines="one">
                                    <v-list-item class="mt-n3 mb-3">
                                        <template v-slot:default>
                                            <v-list-item-title class="fs-26">{{ item.title }}</v-list-item-title>
                                            <v-list-item-subtitle class="fs-16">{{ item.body }}</v-list-item-subtitle>
                                        </template>
                                    </v-list-item>
                                </v-list>
                            </v-card>
                        </template>
                        <template v-slot:default="{ isActive }">
                            <v-card >
                                <v-toolbar color="transparent">
                                    <v-toolbar-title>View announcement</v-toolbar-title>
                                    <v-btn icon dark @click="isActive.value = false">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </v-toolbar>
                                <v-card-title class="fs-26 ms-1">{{ item.title }}</v-card-title>
                                <v-card-text>
                                    <p class="fs-16">{{ item.body }}</p>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn text="Close Dialog" @click="isActive.value = false"></v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                    </v-dialog>
                </v-col>
            </v-row>
        </v-container>
        <v-snackbar :timeout="5000" v-model="snackbar" :color="color">
            <v-icon :icon="icon" class="px-2"></v-icon>
            {{ text }}
        </v-snackbar>
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

const viewAnnouncementDialog = ref(false)

const viewDialog = (id) => {
    const selectedItem = data.value.find(item => item.id === id);
    if (selectedItem) {
        selectedItem.viewAnnouncementDialog = true;
    }
}
const isLoading = ref(false)
const snackbar = ref(false)
const text = ref('')
const color = ref('')
const icon = ref('')

const deleteItem = async (postId) => {
    try {
        isLoading.value = true;
        const token = localStorage.getItem('adminToken');
        const response = await axios.delete('/api/admin/post/' + postId, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        fetchData();
        snackbar.value = true
        color.value = 'success'
        icon.value = 'mdi-check'
        text.value = 'Deleted Successfully'
    } catch (error) {
        console.error('Error deleting post:', error);
    } finally {
        isLoading.value = false;
    }
};

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
