<script setup lang="ts">
import { Trash2, ShoppingCart, ArrowRight, Minus, Plus, Loader2 } from 'lucide-vue-next';
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { cartService, type CartItem } from '@/services/cart.service';

const router = useRouter();
const items = ref<CartItem[]>([]);
const loading = ref(true);
const updating = ref<string[]>([]); // Track items currently being updated

const total = computed(() => {
    return items.value.reduce((sum, item) => sum + (parseFloat(item.listing.price) * item.quantity), 0);
});

const loadCart = async () => {
    loading.value = true;
    try {
        const response = await cartService.getCart();
        items.value = response.items;
    } catch (error) {
        console.error('Failed to load cart:', error);
    } finally {
        loading.value = false;
    }
};

const updateQuantity = async (item: CartItem, change: number) => {
    const newQty = item.quantity + change;
    if (newQty < 1) return;
    
    updating.value.push(item.id);
    try {
        await cartService.updateQuantity(item.id, newQty);
        item.quantity = newQty;
    } catch (error) {
        console.error('Update failed:', error);
    } finally {
        updating.value = updating.value.filter(id => id !== item.id);
    }
};

const removeItem = async (item: CartItem) => {
    updating.value.push(item.id);
    try {
        await cartService.removeItem(item.id);
        items.value = items.value.filter(i => i.id !== item.id);
    } catch (error) {
        console.error('Removal failed:', error);
    } finally {
        updating.value = updating.value.filter(id => id !== item.id);
    }
};

const checkout = async () => {
    try {
        const response = await cartService.checkout();
        window.location.href = response.url;
    } catch (error) {
        console.error('Checkout failed:', error);
    }
};

onMounted(() => {
    loadCart();
});
</script>

<template>
  <div class="min-h-screen bg-background pb-32">
    <!-- Header -->
    <header class="h-14 flex items-center px-4 border-b border-white/5 sticky top-0 bg-[#1a1a18]/90 backdrop-blur-xl z-40">
        <h1 class="font-bold text-lg flex items-center gap-2 text-white">
            <ShoppingCart class="w-5 h-5 text-primary" />
            Your Cart
        </h1>
    </header>

    <div v-if="loading && items.length === 0" class="flex items-center justify-center p-20 mt-10">
        <Loader2 class="w-8 h-8 text-primary animate-spin" />
    </div>

    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center p-8 mt-20 text-center animate-in fade-in duration-500">
        <div class="w-24 h-24 bg-white/5 border border-white/10 rounded-full flex items-center justify-center mb-6">
            <ShoppingCart class="w-10 h-10 text-white/20" />
        </div>
        <h2 class="text-2xl font-black text-white mb-2">Your cart is empty</h2>
        <p class="text-white/40 mb-8 text-sm">Add some amazing deals before they're gone!</p>
        <button @click="router.push('/')" class="bg-primary text-primary-foreground font-bold py-3.5 px-8 rounded-full shadow-lg hover:opacity-90 active:scale-95 transition-all text-sm">
            Start Shopping
        </button>
    </div>

    <div v-else class="p-4 space-y-4">
        <div v-for="item in items" :key="item.id" class="flex gap-4 p-4 rounded-2xl bg-[#22221f] border border-white/5 shadow-sm animate-in slide-in-from-bottom-2 fade-in">
            <div @click="router.push(`/products/${item.listing.id}`)" class="w-24 h-32 rounded-xl bg-neutral-900 overflow-hidden shrink-0 cursor-pointer">
                <img v-if="item.listing.thumbnail_url" :src="item.listing.thumbnail_url.startsWith('http') ? item.listing.thumbnail_url : `/storage/${item.listing.thumbnail_url}`" class="w-full h-full object-cover"/>
            </div>
            <div class="flex flex-col flex-1 pb-1">
                <h3 @click="router.push(`/products/${item.listing.id}`)" class="font-bold text-white leading-tight line-clamp-2 mb-1 hover:underline cursor-pointer">
                    {{ item.listing.title_en }}
                </h3>
                <p class="text-[10px] text-white/40 mb-auto uppercase font-bold tracking-wider">Seller: {{ item.listing.user?.name || 'Unknown' }}</p>
                
                <div class="flex justify-between items-end mt-4">
                    <p class="text-primary font-black text-xl">{{ item.listing.price }} <span class="text-[10px] font-bold opacity-60 uppercase">{{ item.listing.currency }}</span></p>
                    
                    <div class="flex items-center space-x-3 bg-black/20 rounded-full p-1 border border-white/5">
                        <button 
                            @click="updateQuantity(item, -1)" 
                            class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center text-white disabled:opacity-20 active:bg-white/10 transition-colors" 
                            :disabled="item.quantity <= 1 || updating.includes(item.id)"
                        >
                            <Minus class="w-3.5 h-3.5" />
                        </button>
                        <span class="font-bold text-xs w-4 text-center tabular-nums text-white">{{ item.quantity }}</span>
                        <button 
                            @click="updateQuantity(item, 1)" 
                            class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center text-white active:bg-white/10 transition-colors"
                            :disabled="updating.includes(item.id)"
                        >
                            <Plus class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
            <button @click="removeItem(item)" class="self-start p-2 -mr-2 text-white/20 hover:text-red-500 hover:bg-red-500/10 rounded-full transition-colors" :disabled="updating.includes(item.id)">
                <Trash2 class="w-4 h-4" />
            </button>
        </div>
    </div>

    <!-- Checkout Footer -->
    <div v-if="items.length > 0" class="fixed bottom-16 left-0 w-full p-6 border-t border-white/5 bg-[#1a1a18]/90 backdrop-blur-xl pb-safe">
        <div class="flex justify-between items-center mb-6 px-2">
            <span class="text-white/40 font-bold uppercase tracking-wider text-xs">Total Amount</span>
            <span class="text-3xl font-black text-white">{{ total.toLocaleString() }} <span class="text-xs font-bold text-white/40 uppercase">{{ items[0]?.listing?.currency || 'SAR' }}</span></span>
        </div>
        <button @click="checkout" class="w-full bg-primary text-primary-foreground font-bold py-4 rounded-xl shadow-xl shadow-primary/10 flex justify-center items-center gap-2 text-sm active:scale-95 transition-transform">
            <span>Checkout Securely</span>
            <ArrowRight class="w-4 h-4" />
        </button>
    </div>
  </div>
</template>
