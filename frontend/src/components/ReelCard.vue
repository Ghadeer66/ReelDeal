import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { Heart, Bookmark, Share2, Info, Plus } from 'lucide-vue-next';
import axios from 'axios';

const router = useRouter();

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
            router.push('/login');
        }
    }
};

const addToCart = () => {
    emit('addedToCart', props.product);
};

const openDetails = () => {
    emit('openedDetails', props.product);
};

const handleShare = async () => {
    const shareData = {
        title: props.product.title,
        text: `Check out ${props.product.title} on ReelDeal!`,
        url: `${window.location.origin}/products/${props.product.id}`,
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
            <div v-else class="flex flex-col items-center justify-center w-full h-full bg-neutral-900 text-neutral-500">
                <div class="w-16 h-16 bg-neutral-800 rounded-full mb-4 animate-pulse"></div>
                No Media Available
            </div>
        </div>

        <!-- Overlays -->
        <div class="absolute inset-0 z-10 pointer-events-none bg-gradient-to-t from-black/95 via-transparent to-black/30">
            <div class="flex flex-col justify-end w-full h-full p-6 pb-24 pointer-events-auto max-w-lg mx-auto">
                <div class="flex flex-col items-start w-full text-white">
                    <RouterLink :to="`/products/${product.id}`">
                        <h2 class="text-xl font-extrabold line-clamp-2 mb-1 hover:underline drop-shadow-xl">{{ product.title }}</h2>
                    </RouterLink>
                    <p class="text-primary font-black text-2xl mb-4 drop-shadow-xl">{{ product.price }} {{ product.currency }}</p>
                    <p v-if="product.description" class="text-white/60 text-xs line-clamp-2 mb-6 max-w-[85%] leading-relaxed">
                        {{ product.description }}
                    </p>
                    
                    <button @click="addToCart" class="w-full bg-primary text-primary-foreground py-3.5 rounded-xl font-bold text-sm shadow-xl shadow-primary/10 flex items-center justify-center gap-2 transition-transform active:scale-95">
                        <Plus class="w-5 h-5" :stroke-width="3" />
                        Add to cart
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Side Actions -->
        <div class="absolute bottom-32 right-4 z-20 flex flex-col items-center space-y-5">
             <button @click="handleLike" class="flex flex-col items-center group">
                 <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90" :class="{'text-red-500 bg-red-500/10': isLiked, 'text-white': !isLiked}">
                     <Heart class="w-6 h-6" :fill="isLiked ? 'currentColor' : 'none'" :stroke-width="isLiked ? 0 : 2" />
                 </div>
                 <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">{{ localLikes }}</span>
             </button>

             <button @click="handleBookmark" class="flex flex-col items-center group">
                 <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90" :class="{'text-primary bg-primary/10': localBookmarked, 'text-white': !localBookmarked}">
                     <Bookmark class="w-6 h-6" :fill="localBookmarked ? 'currentColor' : 'none'" :stroke-width="localBookmarked ? 0 : 2" />
                 </div>
                 <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">Save</span>
             </button>

             <button @click="handleShare" class="flex flex-col items-center group">
                 <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90 text-white">
                     <Share2 class="w-6 h-6" />
                 </div>
                 <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">Share</span>
             </button>

             <button @click="openDetails" class="flex flex-col items-center group">
                 <div class="p-3 bg-white/10 backdrop-blur-md border border-white/5 rounded-full transition-all group-hover:bg-white/20 group-hover:scale-110 active:scale-90 text-white">
                     <Info class="w-6 h-6" />
                 </div>
                 <span class="text-white/50 text-[10px] font-bold mt-1.5 uppercase tracking-tighter">Details</span>
             </button>
        </div>
    </div>
</template>
