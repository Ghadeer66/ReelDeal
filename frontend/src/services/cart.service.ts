import api from './api'

export interface CartItem {
    id: string
    quantity: number
    listing_id: string
    listing: any
}

export interface CartResponse {
    items: CartItem[]
    total: number
    count: number
}

export const cartService = {
    async getCart(): Promise<CartResponse> {
        const { data } = await api.get<CartResponse>('/cart')
        return data
    },

    async addToCart(listingId: string, quantity: number = 1): Promise<void> {
        await api.post('/cart', { listing_id: listingId, quantity })
    },

    async updateQuantity(itemId: string, quantity: number): Promise<void> {
        await api.put(`/cart/${itemId}`, { quantity })
    },

    async removeItem(itemId: string): Promise<void> {
        await api.delete(`/cart/${itemId}`)
    },

    async checkout(): Promise<{ sessionId: string, url: string }> {
        const { data } = await api.post('/checkout')
        return data
    }
}
