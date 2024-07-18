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
        path: '/admin/dashboard',
        name: 'Admin Dashboard',
        component: () => import('../components/admin/Home.vue')
    },

]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
