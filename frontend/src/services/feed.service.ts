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

    async searchFeed(query: string, cursor?: string | null): Promise<FeedResponse> {
        const params = new URLSearchParams()
        params.append('q', query)
        if (cursor) {
            params.append('cursor', cursor)
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
    }
}
