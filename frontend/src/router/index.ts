import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'feed',
            component: () => import('../pages/FeedPage.vue')
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../pages/LoginPage.vue')
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../pages/RegisterPage.vue')
        },
        {
            path: '/register/interests',
            name: 'interests',
            component: () => import('../pages/InterestsPage.vue')
        }
    ]
})

export default router
