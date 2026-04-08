<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { Trash2, ShoppingCart, ArrowRight, Minus, Plus } from 'lucide-vue-next';

const props = defineProps<{
    cartItems: any[];
    total: number;
}>();

const updateQuantity = (cartItem: any, change: number) => {
    if (cartItem.quantity + change < 1) return;
    router.put(`/cart/${cartItem.id}`, {
        quantity: cartItem.quantity + change
    }, { preserveScroll: true });
};

const removeItem = (cartItem: any) => {
    router.delete(`/cart/${cartItem.id}`, { preserveScroll: true });
};

const checkout = () => {
    router.post('/checkout');
};
</script>

<template>
  <Head title="Cart" />
  <div class="min-h-screen bg-background pb-32">
    <!-- Header -->
    <header class="h-14 flex items-center px-4 border-b border-border sticky top-0 bg-background/90 backdrop-blur-md z-40">
        <h1 class="font-bold text-xl flex items-center gap-2">
            <ShoppingCart class="w-5 h-5 text-accent" />
            Your Cart
        </h1>
    </header>

    <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center p-8 mt-20 text-center animate-in fade-in duration-500">
        <div class="w-24 h-24 bg-muted rounded-full flex items-center justify-center mb-6">
            <ShoppingCart class="w-10 h-10 text-muted-foreground" />
        </div>
        <h2 class="text-2xl font-bold mb-2">Your cart is empty</h2>
        <p class="text-muted-foreground mb-8 text-lg">Add some amazing deals to your cart before they're gone!</p>
        <Link href="/" class="bg-primary text-primary-foreground font-bold py-3.5 px-8 rounded-full shadow-lg hover:opacity-90 transition-opacity active:scale-95">
            Start Shopping
        </Link>
    </div>

    <div v-else class="p-4 space-y-4">
        <div v-for="item in cartItems" :key="item.id" class="flex gap-4 p-4 rounded-2xl bg-card border border-border shadow-sm animate-in slide-in-from-bottom-2 fade-in">
            <div class="w-24 h-32 rounded-xl bg-muted overflow-hidden shrink-0">
                <img v-if="item.product?.media?.[0]" :src="item.product.media[0].path.startsWith('http') ? item.product.media[0].path : `/storage/${item.product.media[0].path}`" class="w-full h-full object-cover"/>
            </div>
            <div class="flex flex-col flex-1 pb-1">
                <Link :href="`/products/${item.product.id}`" class="font-bold text-lg leading-tight line-clamp-2 mb-1 hover:underline">
                    {{ item.product?.title }}
                </Link>
                <p class="text-xs text-muted-foreground mb-auto">Seller: <span class="font-semibold">{{ item.product?.user?.name || 'Unknown' }}</span></p>
                <div class="flex justify-between items-end mt-4">
                    <p class="text-accent font-extrabold text-xl">{{ item.product?.price }} <span class="text-sm font-semibold">{{ item.product?.currency }}</span></p>
                    
                    <div class="flex items-center space-x-3 bg-muted rounded-full p-1 border border-border shadow-inner">
                        <button @click="updateQuantity(item, -1)" class="w-8 h-8 rounded-full bg-background flex items-center justify-center disabled:opacity-50 shadow-sm" :disabled="item.quantity <= 1">
                            <Minus class="w-4 h-4" />
                        </button>
                        <span class="font-bold text-sm w-4 text-center tabular-nums">{{ item.quantity }}</span>
                        <button @click="updateQuantity(item, 1)" class="w-8 h-8 rounded-full bg-background flex items-center justify-center shadow-sm">
                            <Plus class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
            <button @click="removeItem(item)" class="self-start p-2 -mr-2 mt-1 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-full transition-colors">
                <Trash2 class="w-5 h-5" />
            </button>
        </div>
    </div>

    <!-- Checkout Footer -->
    <div v-if="cartItems.length > 0" class="fixed bottom-16 left-0 w-full p-4 border-t border-border bg-background/90 backdrop-blur-xl pb-safe shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.1)] dark:shadow-none">
        <div class="flex justify-between items-center mb-4 px-2">
            <span class="text-muted-foreground font-semibold text-lg">Total</span>
            <span class="text-3xl font-black">{{ total }} <span class="text-lg font-bold text-muted-foreground">{{ cartItems[0]?.product?.currency || 'SAR' }}</span></span>
        </div>
        <button @click="checkout" class="w-full bg-accent text-accent-foreground font-bold py-4 rounded-full shadow-xl shadow-accent/20 flex justify-center items-center gap-2 text-lg active:scale-95 transition-transform">
            <span>Checkout Securely</span>
            <ArrowRight class="w-5 h-5" />
        </button>
    </div>
  </div>
</template>
