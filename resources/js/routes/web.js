import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/admin/home',
            name: 'Admin Home',
            component: () => import('../components/admin/Home.vue'),
        }
    ]
})

export default router
