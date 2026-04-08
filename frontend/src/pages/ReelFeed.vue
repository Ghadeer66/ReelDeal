<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import ReelCard from '@/components/ReelCard.vue';
import ProductDetailsDrawer from '@/components/ProductDetailsDrawer.vue';
import GuestGate from '@/components/GuestGate.vue';

const props = defineProps<{
    initialProducts: any;
}>();

const products = ref<any[]>(props.initialProducts.data || []);
const nextCursor = ref<string | null>(props.initialProducts.next_cursor || null);
const isLoading = ref(false);
const feedContainer = ref<HTMLElement | null>(null);

const activeProduct = ref<any | null>(null);
const isDrawerOpen = ref(false);
const showGuestGate = ref(false);

const page = usePage();
const isGuest = computed(() => !page.props.auth?.user);

const loadMoreProducts = async () => {
    if (isLoading.value || !nextCursor.value) return;
    
    isLoading.value = true;
    try {
        const response = await axios.get('/feed/more', {
            params: { cursor: nextCursor.value }
        });
        
        if (response.data.products && response.data.products.length > 0) {
            products.value = [...products.value, ...response.data.products];
        }
        nextCursor.value = response.data.next_cursor;
    } catch (error) {
        console.error("Failed to load more products", error);
    } finally {
        isLoading.value = false;
    }
};

const handleScroll = () => {
    if (!feedContainer.value) return;
    
    const { scrollTop, scrollHeight, clientHeight } = feedContainer.value;
    
    // Check Guest Limit
    if (isGuest.value) {
        const scrolledReelsCount = Math.floor(scrollTop / clientHeight);
        if (scrolledReelsCount >= 5) {
            showGuestGate.value = true;
            // Prevent further scrolling natively by keeping it snapped
            feedContainer.value.scrollTop = 5 * clientHeight;
            return;
        }
    }
    
    // Load more when user scrolls to the 2nd to last reel
    if (scrollTop + clientHeight * 2 >= scrollHeight) {
        loadMoreProducts();
    }
};

const handleOpenDetails = (product: any) => {
    activeProduct.value = product;
    isDrawerOpen.value = true;
};

const handleAddToCart = async (product: any, quantity = 1) => {
    try {
        // Will be picked up by global inertia handlers or toast systems
        await axios.post('/cart', {
            product_id: product.id,
            quantity: quantity
        });
        isDrawerOpen.value = false;
        // Optionally show toast success here
        alert(`Added ${quantity} of ${product.title} to cart`);
    } catch (e) {
        if ((e as any).response?.status === 401) {
            window.location.href = '/login';
        }
    }
};
</script>

<template>
    <Head title="Feed" />
    
    <div class="h-screen w-full bg-black overflow-hidden relative" style="height: calc(100vh - 64px);">
        <!-- Reel Feed -->
        <div 
            ref="feedContainer"
            @scroll="handleScroll"
            class="h-full w-full overflow-y-scroll snap-y snap-mandatory scroll-smooth hide-scrollbar transition-transform" 
            style="-ms-overflow-style: none; scrollbar-width: none;"
        >
            <ReelCard 
                v-for="product in products" 
                :key="product.id" 
                :product="product" 
                @openedDetails="handleOpenDetails"
                @addedToCart="handleAddToCart(product)"
            />
            
            <div v-if="isLoading" class="h-full w-full snap-start flex items-center justify-center bg-black">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-accent"></div>
            </div>
            
            <div v-if="!nextCursor && products.length > 0" class="h-full w-full snap-start flex items-center justify-center bg-black text-white px-8 text-center flex-col">
                <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center mb-4 text-2xl border border-secondary">
                    🚀
                </div>
                <h3 class="text-xl font-bold mb-2">You're all caught up!</h3>
                <p class="text-gray-400">Come back later for more deals.</p>
            </div>
        </div>

        <!-- Product Details Drawer Component -->
        <ProductDetailsDrawer 
            :product="activeProduct" 
            :isOpen="isDrawerOpen" 
            @close="isDrawerOpen = false"
            @addToCart="handleAddToCart"
        />

        <!-- Guest Gate Overlay -->
        <GuestGate :isOpen="showGuestGate" />
    </div>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
</style>
