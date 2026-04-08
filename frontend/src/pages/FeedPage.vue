<template>
  <div class="h-screen w-full bg-black overflow-hidden flex flex-col pt-14 lg:pt-0">
    
    <!-- Header overlay for mobile -->
    <div class="absolute top-0 left-0 w-full z-20 flex justify-center py-4 pointer-events-none">
      <div class="flex space-x-4 pointer-events-auto">
        <button class="text-white font-bold text-lg drop-shadow shadow-black">Following</button>
        <span class="text-gray-400 font-bold text-lg">|</span>
        <button class="text-white font-bold text-lg underline decoration-green-400 decoration-4 underline-offset-8 drop-shadow shadow-black">For You</button>
      </div>
    </div>
    
    <!-- Scrollable container -->
    <div 
      class="h-full w-full overflow-y-scroll snap-y snap-mandatory scroll-smooth hide-scrollbar flex flex-col"
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
      
      <!-- Loading indicator bounds -->
      <div v-if="loading" class="h-screen w-full flex items-center justify-center snap-start flex-shrink-0">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500"></div>
      </div>
      
      <!-- End of feed -->
      <div v-else-if="!hasMore && listings.length > 0" class="h-40 w-full flex items-center justify-center snap-start flex-shrink-0">
        <p class="text-gray-500 text-sm">You are all caught up!</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ReelCard from '@/components/feed/ReelCard.vue'
import { feedService, type ListingFeedItem } from '@/services/feed.service'

const listings = ref<ListingFeedItem[]>([])
const loading = ref(false)
const hasMore = ref(true)
const nextCursor = ref<string | null>(null)
const activeIndex = ref(0)
const scrollContainer = ref<HTMLElement | null>(null)

let isFetching = false

const loadFeed = async () => {
  if (isFetching || !hasMore.value) return
  isFetching = true
  loading.value = true
  
  try {
    const response = await feedService.getFeed(nextCursor.value)
    listings.value = [...listings.value, ...response.data]
    
    if (response.meta.next_cursor) {
      nextCursor.value = response.meta.next_cursor
    } else {
      hasMore.value = false
    }
  } catch (error) {
    console.error('Failed to load feed:', error)
  } finally {
    loading.value = false
    isFetching = false
  }
}

// Calculate which reel is currently in view
const handleScroll = () => {
  if (!scrollContainer.value) return
  
  const { scrollTop, clientHeight, scrollHeight } = scrollContainer.value
  
  // Update active index (snap threshold detection)
  const newActiveIndex = Math.round(scrollTop / clientHeight)
  if (newActiveIndex !== activeIndex.value && newActiveIndex < listings.value.length) {
    activeIndex.value = newActiveIndex
  }
  
  // Trigger loading threshold (if within 2 viewports from end)
  if (hasMore.value && !isFetching && scrollHeight - (scrollTop + clientHeight) < clientHeight * 2) {
    loadFeed()
  }
}

onMounted(() => {
  loadFeed()
})
</script>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
