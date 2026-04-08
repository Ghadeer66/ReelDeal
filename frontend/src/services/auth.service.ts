import api from './api'
import type { LoginCredentials, RegisterCredentials, AuthResponse, User } from '@/types'

export const authService = {
    async register(credentials: RegisterCredentials): Promise<AuthResponse> {
        const { data } = await api.post<AuthResponse>('/auth/register', credentials)
        return data
    },

    async login(credentials: LoginCredentials): Promise<AuthResponse> {
        const { data } = await api.post<AuthResponse>('/auth/login', credentials)
        return data
    },

    async logout(): Promise<void> {
        await api.post('/auth/logout')
    },

    async getMe(): Promise<{ data: User }> {
        const { data } = await api.get<{ data: User }>('/auth/me')
        return data
    }
}
