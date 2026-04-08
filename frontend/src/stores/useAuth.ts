import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/auth.service'
import type { User, LoginCredentials, RegisterCredentials } from '@/types'

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const token = ref<string | null>(localStorage.getItem('auth_token'))
    const loading = ref(false)
    const error = ref<string | null>(null)

    const isAuthenticated = computed(() => !!token.value && !!user.value)

    function setAuth(userData: User, authToken: string) {
        user.value = userData
        token.value = authToken
        localStorage.setItem('auth_token', authToken)
    }

    function clearAuth() {
        user.value = null
        token.value = null
        localStorage.removeItem('auth_token')
    }

    async function login(credentials: LoginCredentials) {
        loading.value = true
        error.value = null
        try {
            const response = await authService.login(credentials)
            setAuth(response.user, response.token)
            return true
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Login failed'
            return false
        } finally {
            loading.value = false
        }
    }

    async function register(credentials: RegisterCredentials) {
        loading.value = true
        error.value = null
        try {
            const response = await authService.register(credentials)
            setAuth(response.user, response.token)
            return true
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Registration failed'
            return false
        } finally {
            loading.value = false
        }
    }

    async function logout() {
        try {
            await authService.logout()
        } finally {
            clearAuth()
        }
    }

    async function fetchUser() {
        if (!token.value) return
        try {
            const { data } = await authService.getMe()
            user.value = data
        } catch (err) {
            clearAuth()
        }
    }

    return {
        user,
        token,
        loading,
        error,
        isAuthenticated,
        login,
        register,
        logout,
        fetchUser
    }
})
