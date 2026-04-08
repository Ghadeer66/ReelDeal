export interface User {
    id: string
    name: string
    email: string
    user_type: 'guest' | 'customer' | 'seller' | 'merchant' | 'admin'
    preferred_language: 'en' | 'ar'
    avatar_url: string | null
    stripe_account_id: string | null
    is_active: boolean
    created_at: string
}

export interface AuthResponse {
    user: User
    token: string
}

export interface LoginCredentials {
    email: string
    password?: string
}

export interface RegisterCredentials {
    name: string
    email: string
    password?: string
    password_confirmation?: string
    user_type?: string
    preferred_language?: string
}
