<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Heart, Bookmark, Share2, Info, Plus } from 'lucide-vue-next';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps<{
    product: any;
}>();

const emit = defineEmits(['openedDetails', 'addedToCart']);

const videoRef = ref<HTMLVideoElement | null>(null);
const isPlaying = ref(true);
const localLikes = ref(props.product.likes_count || 0);
const localBookmarked = ref(props.product.is_bookmarked || false);
const isLiked = ref(false); 

const primaryMedia = props.product.media?.find((m: any) => m.is_primary) || props.product.media?.[0];
const isVideo = primaryMedia?.type === 'video';

// Handle autoplay/pause based on visibility in the feed
let observer: IntersectionObserver | null = null;

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!videoRef.value) return;
            
            if (entry.isIntersecting) {
                videoRef.value.play().catch(() => {});
                isPlaying.value = true;
            } else {
                videoRef.value.pause();
                isPlaying.value = false;
            }
        });
    }, { threshold: 0.6 });

    if (videoRef.value) {
        observer.observe(videoRef.value);
    }
});

onUnmounted(() => {
    if (videoRef.value && observer) {
        observer.unobserve(videoRef.value);
    }
});

const togglePlay = () => {
    if (!videoRef.value) return;
    if (isPlaying.value) {
        videoRef.value.pause();
    } else {
        videoRef.value.play();
    }
    isPlaying.value = !isPlaying.value;
};

const handleLike = async () => {
    if (isLiked.value) return;
    isLiked.value = true;
    localLikes.value++;
    try {
        await axios.post(`/products/${props.product.id}/like`);
    } catch (e) {
        isLiked.value = false;
        localLikes.value--;
    }
};

const handleBookmark = async () => {
    localBookmarked.value = !localBookmarked.value;
    try {
        await axios.post(`/bookmarks/${props.product.id}`);
    } catch (e) {
        localBookmarked.value = !localBookmarked.value;
        if ((e as any).response?.status === 401) {
            router.get('/login');
        }
    }
};

const addToCart = () => {
    emit('addedToCart', props.product);
};

const openDetails = () => {
    emit('openedDetails', props.product);
};
</script>

<template>
    <div class="relative w-full h-full bg-black snap-start snap-always shrink-0 flex items-center justify-center">
        <!-- Media Background -->
        <div class="absolute inset-0 z-0 h-full w-full" @click="togglePlay">
            <video 
                v-if="isVideo" 
                ref="videoRef"
                :src="primaryMedia.path.startsWith('http') ? primaryMedia.path : `/storage/${primaryMedia.path}`" 
                class="object-cover w-full h-full"
                loop
                playsinline
                muted
            ></video>
            <img 
                v-else-if="primaryMedia" 
                :src="primaryMedia.path.startsWith('http') ? primaryMedia.path : `/storage/${primaryMedia.path}`" 
                class="object-cover w-full h-full" 
            />
            <div v-else class="flex flex-col items-center justify-center w-full h-full bg-gray-900 text-gray-500">
                <div class="w-16 h-16 bg-gray-800 rounded-full mb-4 animate-pulse"></div>
                No Media Available
            </div>
        </div>

        <!-- Overlays -->
        <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-t from-black/90 via-transparent to-black/20">
            <div class="flex flex-col justify-end w-full h-full p-4 pb-20 pointer-events-auto">
                <div class="flex justify-between items-end w-full">
                    <!-- Product Info -->
                    <div class="flex-1 pr-12 text-white">
                        <Link :href="`/products/${product.id}`">
                            <h2 class="text-xl font-bold line-clamp-2 mb-1 hover:underline drop-shadow-md">{{ product.title }}</h2>
                        </Link>
                        <p class="text-accent font-extrabold text-xl mb-3 drop-shadow-md">{{ product.price }} {{ product.currency }}</p>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur flex items-center justify-center font-bold text-sm">
                                {{ product.user?.name?.charAt(0) || '?' }}
                            </div>
                            <span class="text-sm font-medium drop-shadow-md">{{ product.user?.name || 'Unknown Seller' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Actions -->
        <div class="absolute bottom-24 right-4 z-20 flex flex-col items-center space-y-6">
             <button @click="handleLike" class="flex flex-col items-center group">
                 <div class="p-3 bg-black/40 backdrop-blur-md rounded-full transition-transform group-hover:scale-110" :class="{'text-accent': isLiked, 'text-white': !isLiked}">
                     <Heart class="w-7 h-7" :fill="isLiked ? 'currentColor' : 'none'" />
                 </div>
                 <span class="text-white text-xs font-bold mt-1 shadow-sm">{{ localLikes }}</span>
             </button>

             <button @click="handleBookmark" class="flex flex-col items-center group">
                 <div class="p-3 bg-black/40 backdrop-blur-md rounded-full transition-transform group-hover:scale-110" :class="{'text-secondary': localBookmarked, 'text-white': !localBookmarked}">
                     <Bookmark class="w-7 h-7" :fill="localBookmarked ? 'currentColor' : 'none'" />
                 </div>
                 <span class="text-white text-xs font-bold mt-1 shadow-sm">Save</span>
             </button>

             <button @click="openDetails" class="flex flex-col items-center group">
                 <div class="p-3 bg-black/40 backdrop-blur-md rounded-full transition-transform group-hover:scale-110 text-white">
                     <Info class="w-7 h-7" />
                 </div>
                 <span class="text-white text-xs font-bold mt-1 shadow-sm">Details</span>
             </button>
             
             <button @click="addToCart" class="flex flex-col items-center group mt-4">
                 <div class="p-3 bg-accent text-accent-foreground backdrop-blur-md rounded-full shadow-lg transition-transform group-active:scale-95">
                     <Plus class="w-7 h-7" />
                 </div>
                 <span class="text-white text-xs font-bold mt-1 shadow-sm">Cart</span>
             </button>
        </div>
    </div>
</template>
