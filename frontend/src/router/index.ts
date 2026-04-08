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
        },
        {
            path: '/search',
            name: 'search',
            component: () => import('../pages/Search.vue')
        },
        {
            path: '/cart',
            name: 'cart',
            component: () => import('../pages/Cart.vue')
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../pages/settings/Profile.vue')
        },
        {
            path: '/saves',
            name: 'saves',
            component: () => import('../pages/Bookmarks.vue')
        }
    ]
})

export default router
