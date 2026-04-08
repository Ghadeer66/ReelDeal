<template>
  <div class="relative w-full h-screen max-w-md mx-auto snap-center bg-black overflow-hidden flex-shrink-0" ref="cardRef">
    <!-- Media Layer -->
    <div class="absolute inset-0 z-0">
      <video
        v-if="listing.media_type === 'video' && listing.video_url"
        ref="videoRef"
        :src="listing.video_url"
        class="w-full h-full object-cover"
        loop
        muted
        playsinline
        :poster="listing.thumbnail_url || ''"
      ></video>
      <img
        v-else-if="listing.images && listing.images.length > 0"
        :src="listing.images[0].url"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full bg-gray-900 flex items-center justify-center">
        <span class="text-gray-500">No media available</span>
      </div>
      
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
    </div>

    <!-- UI Overlay Layer -->
    <div class="absolute inset-0 z-10 flex flex-col justify-end p-4 pointer-events-none">
      
      <div class="flex justify-between items-end">
        <!-- Info details (Bottom Left) -->
        <div class="flex-1 pr-12 pointer-events-auto">
          <div class="flex items-center space-x-2 mb-3">
            <img 
              :src="listing.user.avatar_url || 'https://ui-avatars.com/api/?name=' + listing.user.name" 
              class="w-10 h-10 rounded-full border-2 border-green-400"
            />
            <span class="text-white font-bold drop-shadow-md">@{{ listing.user.name }}</span>
          </div>
          
          <h3 class="text-white font-bold text-lg leading-tight mb-1 drop-shadow-md">{{ listing.title_en }}</h3>
          <p class="text-gray-200 text-sm line-clamp-2 mb-2 drop-shadow-md">{{ listing.description_en }}</p>
          
          <div class="inline-flex items-center px-3 py-1 bg-green-500 rounded-full text-black font-bold text-sm">
            {{ listing.price }} {{ listing.currency }}
          </div>
        </div>

        <!-- Controls (Bottom Right) -->
        <div class="flex flex-col items-center space-y-6 pb-4 pointer-events-auto">
          <button @click="handleLike" class="flex flex-col items-center group">
            <div class="p-3 bg-black/40 rounded-full group-hover:bg-black/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white transition-colors" :class="{'text-red-500 fill-red-500': isLiked}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </div>
            <span class="text-white text-xs mt-1 font-medium drop-shadow-md">{{ listing.likes_count }}</span>
          </button>

          <button @click="handleSave" class="flex flex-col items-center group">
            <div class="p-3 bg-black/40 rounded-full group-hover:bg-black/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white transition-colors" :class="{'text-yellow-400 fill-yellow-400': isSaved}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
              </svg>
            </div>
            <span class="text-white text-xs mt-1 font-medium drop-shadow-md">Save</span>
          </button>
          
          <button class="flex flex-col items-center group mt-2">
            <div class="p-3 bg-green-500 text-black rounded-full animate-bounce shadow-lg shadow-green-500/50 hover:bg-green-400 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import type { ListingFeedItem } from '@/services/feed.service'
import { feedService } from '@/services/feed.service'
import { useIntersectionObserver } from '@vueuse/core'

const props = defineProps<{
  listing: ListingFeedItem
  isActive: boolean
}>()

const videoRef = ref<HTMLVideoElement | null>(null)
const cardRef = ref<HTMLElement | null>(null)

const isLiked = ref(false)
const isSaved = ref(false)
let viewTracked = false

// Autoplay logic bounded by isActive prop passed from parent Feed scroll observation
watch(() => props.isActive, (active) => {
  if (videoRef.value) {
    if (active) {
      videoRef.value.play().catch(e => console.warn('Autoplay prevented', e))
    } else {
      videoRef.value.pause()
      videoRef.value.currentTime = 0
    }
  }
})

// View tracking intersection observer implementation
const { stop } = useIntersectionObserver(
  cardRef,
  ([{ isIntersecting }]) => {
    if (isIntersecting && !viewTracked) {
      setTimeout(() => {
        if (props.isActive && !viewTracked) {
          viewTracked = true
          feedService.trackView(props.listing.id).catch(e => console.error("Could not track view", e))
        }
      }, 3000)
    }
  },
  { threshold: 0.6 }
)

const router = useRouter()

const handleLike = async () => {
  try {
    await feedService.toggleLike(props.listing.id)
    isLiked.value = !isLiked.value
  } catch (error: any) {
    if (error?.response?.status === 401) {
      router.push('/login')
    }
  }
}

const handleSave = async () => {
  try {
    await feedService.toggleSave(props.listing.id)
    isSaved.value = !isSaved.value
  } catch (error: any) {
    if (error?.response?.status === 401) {
      router.push('/login')
    }
  }
}

onUnmounted(() => {
  stop()
})
</script>
