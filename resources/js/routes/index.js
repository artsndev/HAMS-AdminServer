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
        component: () => import('../components/admin/auth/Login.vue')
    },
    {
        path: '/admin/dashboard',
        name: 'Admin Dashboard',
        component: () => import('../components/admin/Dashboard.vue')
    },
    {
        path: '/admin/appointments',
        name: 'Admin Appointment',
        component: () => import('../components/admin/Appointment.vue')
    },
    {
        path: '/admin/doctors',
        name: 'Admin Doctor',
        component: () => import('../components/admin/Doctor.vue')
    },
    {
        path: '/admin/users',
        name: 'Admin User',
        component: () => import('../components/admin/User.vue')
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

export default router
