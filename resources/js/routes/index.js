import { createRouter, createWebHistory } from 'vue-router'

// Create your Client-Side Routing here
const routes = [
    {
        path: '/',
        redirect: {
            name: 'Admin Dashboard',
        }
    },
    // Redirects the user to 404 Not Found Page
    {
        path: '/:pathMatch(.*)*',
        component: () => import('../components/errors/NotFoundPage.vue')
    },
    // Admin Web Routes
    {
        path: '/admin/login',
        name: 'Admin Login',
        component: () => import('../components/admin/auth/Login.vue'),
        meta: {
            admin: true
        }
    },
    {
        path: '/admin/dashboard',
        name: 'Admin Dashboard',
        component: () => import('../components/admin/Dashboard.vue'),
        meta: {
            requiresAdminAuth: true,
        }
    },
    {
        path: '/admin/appointments',
        name: 'Admin Appointment',
        component: () => import('../components/admin/Appointment.vue'),
        meta: {
            requiresAdminAuth: true,
        }
    },
    {
        path: '/admin/doctors',
        name: 'Admin Doctor',
        component: () => import('../components/admin/Doctor.vue'),
        meta: {
            requiresAdminAuth: true,
        }
    },
    {
        path: '/admin/users',
        name: 'Admin User',
        component: () => import('../components/admin/User.vue'),
        meta: {
            requiresAdminAuth: true,
        }
    },
    {
        path: '/admin/queues',
        name: 'Admin Queue',
        component: () => import('../components/admin/Queue.vue'),
        meta: {
            requiresAdminAuth: true,
        }
    },

    // Doctor Web Routes
    {
        path: '/doctor/login',
        name: 'Doctor Login',
        component: () => import('../components/doctor/auth/Login.vue')
    },
    {
        path: '/doctor/register',
        name: 'Doctor Register',
        component: () => import('../components/doctor/auth/Register.vue')
    },
    {
        path: '/doctor/dashboard',
        name: 'Doctor Dashboard',
        component: () => import('../components/doctor/Dashboard.vue'),
        meta: {
            requiresDoctorAuth: true,
        }
    },
    {
        path: '/doctor/appointment',
        name: 'Doctor Appointment',
        component: () => import('../components/doctor/Appointment.vue'),
        meta: {
            requiresDoctorAuth: true,
        }
    },
    {
        path: '/doctor/schedule',
        name: 'Doctor Schedule',
        component: () => import('../components/doctor/Schedule.vue'),
        meta: {
            requiresDoctorAuth: true,
        }
    },
    {
        path: '/doctor/queues',
        name: 'Doctor Queue',
        component: () => import('../components/doctor/Queue.vue'),
        meta: {
            requiresDoctorAuth: true,
        }
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Admin Auth Middleware
router.beforeEach((to, from, next) => {
    const requiresAdminAuth = to.matched.some(record => record.meta.requiresAdminAuth);
    const admin = to.matched.some(record => record.meta.admin);
    const isAdminLoggedIn = localStorage.getItem('adminToken');
    if (requiresAdminAuth && !isAdminLoggedIn) {
        next({ name: 'Admin Login' });
    } else if (admin && isAdminLoggedIn) {
        next({ name: 'Admin Dashboard' });
    } else {
        next();
    }
});

// Doctor Auth Middleware
router.beforeEach((to, from, next) => {
    const requiresDoctorAuth = to.matched.some(record => record.meta.requiresDoctorAuth);
    const doctor = to.matched.some(record => record.meta.doctor);
    const isDoctorLoggedIn = localStorage.getItem('doctorToken');
    if (requiresDoctorAuth && !isDoctorLoggedIn) {
        next({ name: 'Doctor Login' });
    } else if (doctor && isDoctorLoggedIn) {
        next({ name: 'Doctor Dashboard' });
    } else {
        next();
    }
});

export default router
