<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, ShoppingCart, Heart, Bookmark } from 'lucide-vue-next';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps<{
    product: any;
}>();

const localLikes = ref(props.product.likes_count || 0);
const localBookmarked = ref(props.product.is_bookmarked || false);
const isLiked = ref(false); 
const quantity = ref(1);
const primaryMedia = props.product.media?.find((m: any) => m.is_primary) || props.product.media?.[0];

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
    }
};

const handleAddToCart = async () => {
    try {
        await axios.post('/cart', {
            product_id: props.product.id,
            quantity: quantity.value
        });
        alert(`Added to cart successfully`);
    } catch (e) {
        if ((e as any).response?.status === 401) {
            router.get('/login');
        }
    }
};
</script>

<template>
  <Head :title="product.title" />
  <div class="pb-28 min-h-screen bg-background">
    <!-- Header -->
    <header class="h-14 flex items-center px-4 border-b border-border sticky top-0 bg-background/90 backdrop-blur-md z-40">
        <Link href="/" class="p-2 -ml-2 rounded-full hover:bg-muted transition-colors">
            <ArrowLeft class="w-6 h-6" />
        </Link>
        <span class="ml-4 font-bold truncate text-lg">Details</span>
    </header>
    
    <div v-if="product.media?.length > 0" class="w-full aspect-[4/5] bg-black relative">
        <img v-if="primaryMedia.type === 'image'" :src="primaryMedia.path.startsWith('http') ? primaryMedia.path : `/storage/${primaryMedia.path}`" class="w-full h-full object-cover"/>
        <video v-else :src="primaryMedia.path.startsWith('http') ? primaryMedia.path : `/storage/${primaryMedia.path}`" class="w-full h-full object-cover" controls autoplay loop playsinline></video>
        
        <div class="absolute bottom-4 right-4 flex flex-col gap-4">
             <button @click="handleLike" class="p-3 bg-black/40 backdrop-blur-md rounded-full shadow-lg" :class="{'text-accent': isLiked, 'text-white': !isLiked}">
                 <Heart class="w-6 h-6" :fill="isLiked ? 'currentColor' : 'none'" />
             </button>
             <button @click="handleBookmark" class="p-3 bg-black/40 backdrop-blur-md rounded-full shadow-lg" :class="{'text-secondary': localBookmarked, 'text-white': !localBookmarked}">
                 <Bookmark class="w-6 h-6" :fill="localBookmarked ? 'currentColor' : 'none'" />
             </button>
        </div>
    </div>
    
    <div class="p-5">
        <h1 class="text-2xl font-bold mb-2">{{ product.title }}</h1>
        <p class="text-accent font-extrabold text-3xl mb-6">{{ product.price }} {{ product.currency }}</p>
        
        <!-- Seller Info -->
        <div class="flex items-center space-x-3 mb-6 p-4 rounded-2xl bg-muted/30 border border-border">
            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center font-bold text-primary-foreground text-lg shadow-sm">
                {{ product.user?.name?.charAt(0) || '?' }}
            </div>
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider mb-0.5">Seller</p>
                <p class="font-bold text-lg">{{ product.user?.name || 'Unknown Controller' }}</p>
            </div>
        </div>
        
        <div class="mb-8">
            <h3 class="text-sm font-bold text-muted-foreground uppercase tracking-wide mb-3">Description</h3>
            <p class="text-foreground leading-relaxed">{{ product.description || 'No description provided.' }}</p>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="p-4 bg-muted/50 rounded-2xl border border-border">
                <span class="text-xs uppercase text-muted-foreground font-semibold block mb-1">Condition</span>
                <span class="font-bold text-lg capitalize">{{ product.condition || 'Used' }}</span>
            </div>
            <div class="p-4 bg-muted/50 rounded-2xl border border-border">
                <span class="text-xs uppercase text-muted-foreground font-semibold block mb-1">Category</span>
                <span class="font-bold text-lg">{{ product.category?.name_en || 'Misc' }}</span>
            </div>
        </div>
    </div>
    
    <!-- Fixed CTA -->
    <div class="fixed bottom-0 left-0 w-full p-4 border-t border-border bg-background/90 backdrop-blur-md pb-safe">
        <button @click="handleAddToCart" class="w-full bg-accent text-accent-foreground font-bold py-4 rounded-full shadow-lg shadow-accent/20 flex justify-center items-center gap-2 text-lg active:scale-95 transition-transform">
            <ShoppingCart class="w-6 h-6" />
            <span>Add to Cart</span>
        </button>
    </div>
  </div>
</template>
