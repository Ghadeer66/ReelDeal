<script setup lang="ts">
import { ref, watch } from 'vue';
import { X, Minus, Plus, ShoppingCart } from 'lucide-vue-next';

const props = defineProps<{
    product: any;
    isOpen: boolean;
}>();

const emit = defineEmits(['close', 'addToCart']);
const quantity = ref(1);

watch(() => props.isOpen, (newVal) => {
    if (newVal) {
        quantity.value = 1;
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

const increment = () => quantity.value++;
const decrement = () => quantity.value > 1 ? quantity.value-- : 1;

const submitCart = () => {
    emit('addToCart', props.product, quantity.value);
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex flex-col justify-end w-full max-w-md mx-auto">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="emit('close')"></div>
        
        <!-- Drawer -->
        <div class="relative bg-background w-full max-h-[85vh] rounded-t-3xl shadow-2xl flex flex-col overflow-hidden animate-in slide-in-from-bottom-full duration-300 ease-out border-t border-border">
            <div class="flex justify-center w-full py-4 bg-background cursor-pointer" @click="emit('close')">
                <div class="w-12 h-1.5 rounded-full bg-muted-foreground/30"></div>
            </div>
            
            <div class="flex-1 overflow-y-auto px-6 pb-28">
                <div v-if="product">
                    <h2 class="text-2xl font-bold mb-2">{{ product.title }}</h2>
                    <p class="text-accent font-extrabold text-2xl mb-6">{{ product.price }} {{ product.currency }}</p>
                    
                    <div class="flex items-center space-x-3 mb-6 p-4 rounded-2xl bg-muted/50 border border-border">
                        <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center font-bold text-primary-foreground text-lg shadow-sm">
                            {{ product.user?.name?.charAt(0) || '?' }}
                        </div>
                        <div>
                            <p class="hidden text-xs text-muted-foreground uppercase tracking-wider mb-0.5">Listed by</p>
                            <p class="font-bold text-lg leading-tight">{{ product.user?.name || 'Unknown Seller' }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-muted-foreground uppercase tracking-wide mb-3">Description</h3>
                        <p class="text-foreground leading-relaxed whitespace-pre-wrap">{{ product.description || 'No description provided.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div class="p-4 bg-muted/30 border border-border rounded-2xl">
                            <p class="text-xs text-muted-foreground font-semibold uppercase mb-1">Condition</p>
                            <p class="font-bold capitalize">{{ product.condition || 'Used' }}</p>
                        </div>
                        <div class="p-4 bg-muted/30 border border-border rounded-2xl">
                            <p class="text-xs text-muted-foreground font-semibold uppercase mb-1">Category</p>
                            <p class="font-bold truncate">{{ product.category?.name_en || 'General' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom action bar inside drawer -->
            <div class="absolute bottom-0 left-0 w-full p-5 bg-background/90 backdrop-blur-md border-t border-border flex items-center justify-between pb-safe">
                <div class="flex items-center space-x-4 bg-muted/50 rounded-full p-1 border border-border">
                    <button @click="decrement" class="w-10 h-10 rounded-full bg-white dark:bg-zinc-800 flex items-center justify-center disabled:opacity-50 shadow-sm" :disabled="quantity <= 1">
                        <Minus class="w-5 h-5 text-foreground" />
                    </button>
                    <span class="text-lg font-bold w-6 text-center tabular-nums">{{ quantity }}</span>
                    <button @click="increment" class="w-10 h-10 rounded-full bg-white dark:bg-zinc-800 flex items-center justify-center shadow-sm">
                        <Plus class="w-5 h-5 text-foreground" />
                    </button>
                </div>
                
                <button @click="submitCart" class="flex-1 ml-4 bg-accent text-accent-foreground py-3.5 px-4 rounded-full font-bold shadow-lg shadow-accent/25 flex items-center justify-center space-x-2 active:scale-95 transition-transform">
                    <ShoppingCart class="w-5 h-5" />
                    <span>Add to Cart</span>
                </button>
            </div>
        </div>
    </div>
</template>
