<template>
    <v-container class="mt-16">
        <v-row justify="center" align="center" class="min-vh-100 mt-16">
            <v-col xl="3">
                <v-card>
                    <v-card-text class="font-weight-bold fs-30 mb-n5">Login Here</v-card-text>
                    <v-card-item>
                        <v-alert v-if="error" icon="mdi-alert" v-model="alert"  variant="tonal" class="mb-5"  color="red-darken-4">
                            {{ error }}
                        </v-alert>
                        <v-form @submit.prevent="login" class="mt-3">
                            <v-text-field density="compact" v-model="form.email" label="Email Address" placeholder="Example: rubickking04@gmail.com" variant="outlined" class="mb-2" :error-messages="email_error" clearable color="primary"></v-text-field>
                            <v-text-field density="compact" v-model="form.password" :type="showPassword ? 'text' : 'password'" label="Password" hint="Must be 8-20 characters long." clearable variant="outlined" :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="togglePasswordVisibility" :error-messages="password_error" color="primary"></v-text-field>
                            <div class="text-start mt-n2">
                                <v-checkbox v-model="form.remember" label="Remember me" color="primary" hide-details></v-checkbox>
                            </div>
                            <v-row class="text-center mb-1">
                                <v-col>
                                    <v-btn type="submit" :disabled="isLoading" :loading="isLoading" color="primary" class="mb-2 text-capitalize" block rounded="xs">
                                    Sign in
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                        <div class="text-center">
                            <p class="text-muted">Doesn't have an account? <RouterLink to="/doctor/register" class="text-decoration-none text-primary"><span>Register here</span></RouterLink></p>
                        </div>
                    </v-card-item>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router';

const router = useRouter();

const form = reactive({
    email: '',
    password: ''
});

const error = ref('')
const email_error = ref('')
const password_error = ref('')
const isLoading = ref(false)
const showPassword = ref(false)
const timer = ref(null)

const login = async () => {
    try {
        isLoading.value = true;
        const formData = new FormData();
        formData.append('email', form.email);
        formData.append('password', form.password);
        const response = await axios.post('/api/doctor/login', formData);

        const clearValidationErrors = () => {
            email_error.value = '';
            password_error.value = '';
        }

        const clearErrorValidation = () => {
            error.value = '';
        }

        const setValidationError = () => {
            clearValidationErrors();
            timer.value = setTimeout(() => {
                email_error.value = response.data.errors.email;
                password_error.value = response.data.errors.password;
            }, 1);
            setTimeout(() => {
                clearValidationErrors();
            }, 10000);
        }

        const setErrorValidation = () => {
            clearErrorValidation();
            setTimeout(() => {
                error.value = response.data.message;
            }, 1);
            setTimeout(() => {
                clearErrorValidation();
            }, 10000);
        }
        if(response.data.success){
            localStorage.setItem('doctorToken', response.data.data.doctorToken);
            router.push('/doctor/dashboard');
        } else {
            if (response.data.errors) {
                setValidationError();
            } else {
                setErrorValidation();
            }
        }
    } catch (error) {
        console.error("Error Data: ", error);
    } finally {
        isLoading.value = false;
    }
}

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
}
</script>


<style>
.fs-30 {
    font-size: 30px;
}
</style>
