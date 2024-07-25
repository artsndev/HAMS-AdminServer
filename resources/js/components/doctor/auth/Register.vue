<template>
    <v-container class="mt-16">
        <v-row justify="center" align="center" class="min-vh-100 mt-16">
            <v-col xl="3">
                <v-card>
                    <v-card-text class="font-weight-bold fs-30 mb-n5">Register Here</v-card-text>
                    <v-card-item>
                        <v-alert v-if="error" icon="mdi-alert" v-model="alert"  variant="tonal" class="mb-5"  color="red-darken-4">
                            {{ error }}
                        </v-alert>
                        <v-form @submit.prevent="login" class="mt-3">
                            <v-text-field density="compact" prepend-inner-icon="mdi-account-outline" v-model="form.name" label="Full Name" placeholder="Juan Dela Cruz" variant="outlined" class="mb-1" :error-messages="name_error" clearable color="primary"></v-text-field>
                            <v-text-field density="compact" prepend-inner-icon="mdi-email-outline" v-model="form.email" label="Email Address" placeholder="rubickking04@gmail.com" variant="outlined" class="mb-1" :error-messages="email_error" clearable color="primary"></v-text-field>
                            <v-text-field density="compact" prepend-inner-icon="mdi-lock-outline" v-model="form.password" :type="showPassword ? 'text' : 'password'" label="Password" class="mb-1" hint="Must be 8-20 characters long." clearable variant="outlined" :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="togglePasswordVisibility" :error-messages="password_error" color="primary"></v-text-field>
                            <v-text-field density="compact" prepend-inner-icon="mdi-lock-outline" v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" label="Confirm Password" class="mb-2" hint="Must be matched with your password." clearable variant="outlined" :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="toggleConfirmPasswordVisibility" :error-messages="confirm_password_error" color="primary"></v-text-field>
                            <v-row class="text-center mb-1">
                                <v-col>
                                    <v-btn type="submit" :disabled="isLoading" :loading="isLoading" color="primary" class="mb-2 text-capitalize" block rounded="xs">
                                    Sign in
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                        <div class="text-center">
                            <p class="text-muted">Already have an account? <RouterLink to="/doctor/login" class="text-decoration-none"><span>Login here</span></RouterLink></p>
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
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const error = ref('')
const name_error = ref('')
const email_error = ref('')
const password_error = ref('')
const confirm_password_error = ref('')
const isLoading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const timer = ref(null)
// const snackbar = ref(false)

const login = async () => {
    try {
        isLoading.value = true;
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('email', form.email);
        formData.append('password', form.password);
        formData.append('password_confirmation', form.password_confirmation);
        const response = await axios.post('/api/doctor/register', formData);

        const clearValidationErrors = () => {
            name_error.value = '';
            email_error.value = '';
            password_error.value = '';
        }

        const clearErrorValidation = () => {
            error.value = '';
        }

        const setValidationError = () => {
            clearValidationErrors();
            timer.value = setTimeout(() => {
                name_error.value = response.data.errors.name;
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

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
}
</script>


<style>
.fs-30 {
    font-size: 30px;
}

</style>
