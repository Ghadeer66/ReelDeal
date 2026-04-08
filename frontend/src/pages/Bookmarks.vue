<template>
  <div class="h-full w-full bg-background overflow-hidden flex flex-col pt-14 lg:pt-0">
    
    <!-- Header overlay for mobile -->
    <div class="absolute top-0 left-0 w-full z-20 flex items-center px-4 py-4 bg-gradient-to-b from-black/50 to-transparent">
      <button @click="router.back()" class="p-2 -ml-2 text-white/70 hover:text-white transition-colors">
        <ArrowLeft class="w-6 h-6" />
      </button>
      <h1 class="text-white font-bold text-lg ml-2 drop-shadow shadow-black">Saved Reels</h1>
    </div>
    
    <!-- Scrollable container -->
    <div 
      class="h-full w-full overflow-y-scroll snap-y snap-mandatory scroll-smooth hide-scrollbar flex flex-col pb-16"
      @scroll="handleScroll"
      ref="scrollContainer"
    >
      <ReelCard 
        v-for="(listing, index) in listings" 
        :key="listing.id"
        :listing="listing"
        :isActive="activeIndex === index"
        class="h-full w-full max-w-lg mx-auto"
      />
      
      <!-- Empty state -->
      <div v-if="listings.length === 0 && !loading" class="h-screen w-full flex flex-col items-center justify-center p-8 text-center">
        <div class="w-24 h-24 bg-white/5 border border-white/10 rounded-full flex items-center justify-center mb-6 text-white/20">
            <Bookmark class="w-10 h-10" />
        </div>
        <h2 class="text-2xl font-black text-white mb-2">Your collection is empty</h2>
        <p class="text-white/40 text-sm max-w-xs leading-relaxed">Reels you bookmark while browsing will appear here for quick access later.</p>
        <button @click="router.push('/')" class="mt-8 bg-primary text-primary-foreground font-bold py-3 px-8 rounded-full text-sm active:scale-95 transition-all">
            Find Something New
        </button>
      </div>

      <!-- Loading indicator bounds -->
      <div v-if="loading" class="h-screen w-full flex items-center justify-center snap-start flex-shrink-0">
        <Loader2 class="animate-spin h-8 w-8 text-primary" />
      </div>
      
      <!-- End of feed -->
      <div v-else-if="!hasMore && listings.length > 0" class="h-40 w-full flex flex-col items-center justify-center snap-start flex-shrink-0">
        <p class="text-white/20 text-[10px] font-black uppercase tracking-[0.2em]">End of Collection</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ArrowLeft, Bookmark, Loader2 } from 'lucide-vue-next'
import ReelCard from '@/components/feed/ReelCard.vue'
import { feedService, type ListingFeedItem } from '@/services/feed.service'

const router = useRouter()
const listings = ref<ListingFeedItem[]>([])
const loading = ref(false)
const hasMore = ref(true)
const nextCursor = ref<string | null>(null)
const activeIndex = ref(0)
const scrollContainer = ref<HTMLElement | null>(null)

let isFetching = false

const loadSaves = async () => {
  if (isFetching || !hasMore.value) return
  isFetching = true
  loading.value = true
  
  try {
    const response = await feedService.getSaves(nextCursor.value)
    listings.value = [...listings.value, ...response.data]
    
    if (response.meta.next_cursor) {
      nextCursor.value = response.meta.next_cursor
    } else {
      hasMore.value = false
    }
  } catch (error) {
    console.error('Failed to load saves:', error)
  } finally {
    loading.value = false
    isFetching = false
  }
}

const handleScroll = () => {
  if (!scrollContainer.value) return
  
  const { scrollTop, clientHeight, scrollHeight } = scrollContainer.value
  
  const newActiveIndex = Math.round(scrollTop / clientHeight)
  if (newActiveIndex !== activeIndex.value && newActiveIndex < listings.value.length) {
    activeIndex.value = newActiveIndex
  }
  
  if (hasMore.value && !isFetching && scrollHeight - (scrollTop + clientHeight) < clientHeight * 2) {
    loadSaves()
  }
}

onMounted(() => {
  loadSaves()
})
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
