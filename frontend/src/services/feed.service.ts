import api from './api'

export interface ListingImage {
    url: string
    sort: number
}

export interface ListingFeedItem {
    id: string
    title_en: string
    title_ar: string
    description_en: string
    description_ar: string
    price: string
    currency: string
    video_url: string | null
    thumbnail_url: string | null
    media_type: string
    likes_count: number
    saves_count: number
    views_count: number
    user: {
        id: string
        name: string
        avatar_url: string | null
    }
    category: {
        id: string
        name_en: string
        name_ar: string
    }
    images: ListingImage[]
    created_at: string
}

export interface FeedResponse {
    data: ListingFeedItem[]
    meta: {
        next_cursor: string | null
        prev_cursor: string | null
        path: string
        per_page: number
    }
}

export const feedService = {
    async getFeed(cursor?: string | null): Promise<FeedResponse> {
        const params = new URLSearchParams()
        if (cursor) {
            params.append('cursor', cursor)
        }
        const { data } = await api.get<FeedResponse>(`/feed?${params.toString()}`)
        return data
    },

    async searchFeed(query: string, cursor?: string | null, filters?: {
        category_id?: string,
        min_price?: number,
        max_price?: number,
        condition?: 'new' | 'used',
        location?: string
    }): Promise<FeedResponse> {
        const params = new URLSearchParams()
        params.append('q', query)
        if (cursor) {
            params.append('cursor', cursor)
        }
        if (filters?.category_id) {
            params.append('category_id', filters.category_id)
        }
        if (filters?.min_price) {
            params.append('min_price', filters.min_price.toString())
        }
        if (filters?.max_price) {
            params.append('max_price', filters.max_price.toString())
        }
        if (filters?.condition) {
            params.append('condition', filters.condition)
        }
        if (filters?.location) {
            params.append('location', filters.location)
        }
        const { data } = await api.get<FeedResponse>(`/feed/search?${params.toString()}`)
        return data
    },

    async trackView(listingId: string): Promise<void> {
        await api.post(`/listings/${listingId}/view`)
    },

    async toggleLike(listingId: string): Promise<void> {
        await api.post(`/listings/${listingId}/like`)
    },

    async toggleSave(listingId: string): Promise<void> {
        await api.post(`/listings/${listingId}/save`)
    },

    async addToCart(listingId: string, quantity: number = 1): Promise<void> {
        await api.post('/cart', { listing_id: listingId, quantity })
    },

    async getSaves(cursor?: string | null): Promise<FeedResponse> {
        const params = new URLSearchParams()
        if (cursor) {
            params.append('cursor', cursor)
        }
        const { data } = await api.get<FeedResponse>(`/saves?${params.toString()}`)
        return data
    }
}
