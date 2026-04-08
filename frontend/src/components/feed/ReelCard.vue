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
    <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-t from-black/95 via-transparent to-black/30">
      <div class="flex flex-col justify-end w-full h-full p-6 pb-24 pointer-events-auto max-w-lg mx-auto">
        <div class="flex justify-between items-end w-full">
          <!-- Info details (Bottom Left) -->
          <div class="flex flex-col items-start w-full text-white">
            <div class="flex items-center space-x-2 mb-4">
              <img 
                :src="listing.user.avatar_url || 'https://ui-avatars.com/api/?name=' + listing.user.name" 
                class="w-9 h-9 rounded-full border-2 border-primary"
              />
              <span class="text-white font-bold text-sm drop-shadow-md">@{{ listing.user.name }}</span>
            </div>
            
            <h3 class="text-xl font-extrabold leading-tight mb-1 drop-shadow-xl">{{ listing.title_en }}</h3>
            <p class="text-primary font-black text-2xl mb-4 drop-shadow-xl">{{ listing.price }} {{ listing.currency }}</p>
            <p class="text-white/60 text-xs line-clamp-2 mb-6 max-w-[85%] leading-relaxed drop-shadow-md">{{ listing.description_en }}</p>
            
            <button @click="handleAddToCart" class="w-full bg-primary text-primary-foreground py-3.5 rounded-xl font-bold text-sm shadow-xl shadow-primary/10 flex items-center justify-center transition-transform active:scale-95">
                + Add to cart
            </button>
          </div>

          <!-- Side Actions (Right) -->
          <div class="absolute bottom-32 right-4 flex flex-col items-center space-y-5">
            <button @click="handleLike" class="flex flex-col items-center group">
              <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90" :class="{'text-red-500 bg-red-500/10': isLiked, 'text-white': !isLiked}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-colors" :class="{'fill-red-500 stroke-0': isLiked}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </div>
              <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">{{ listing.likes_count }}</span>
            </button>

            <button @click="handleSave" class="flex flex-col items-center group">
              <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90" :class="{'text-primary bg-primary/10': isSaved, 'text-white': !isSaved}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-colors" :class="{'fill-primary stroke-0': isSaved}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
              </div>
              <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">Save</span>
            </button>

            <button @click="handleShare" class="flex flex-col items-center group">
              <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
              </div>
              <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">Share</span>
            </button>

            <button @click="handleDetails" class="flex flex-col items-center group">
              <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">Details</span>
            </button>
          </div>
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

const handleDetails = () => {
    // Navigate to product details page
    router.push(`/products/${props.listing.id}`);
}

const handleShare = async () => {
    const shareData = {
        title: props.listing.title_en,
        text: `Check out ${props.listing.title_en} on ReelDeal!`,
        url: `${window.location.origin}/products/${props.listing.id}`,
    };

    try {
        if (navigator.share) {
            await navigator.share(shareData);
        } else {
            await navigator.clipboard.writeText(shareData.url);
            alert('Link copied to clipboard!');
        }
    } catch (err) {
        console.error('Error sharing:', err);
    }
}

const handleAddToCart = async () => {
    try {
        await feedService.addToCart(props.listing.id, 1);
        alert('Added to cart!');
    } catch (error: any) {
        if (error?.response?.status === 401) {
            router.push('/login');
        }
    }
}

onUnmounted(() => {
  stop()
})
</script>
