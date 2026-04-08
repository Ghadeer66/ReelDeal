<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { Search as SearchIcon, Play } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    products: any;
    query: string | null;
    categoryId: string | null;
}>();

const searchQuery = ref(props.query || '');

// Quick debounce implementation to avoid lodash dependency if not installed
let timeout: any = null;
const performSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/search', { q: searchQuery.value }, { preserveState: true, replace: true });
    }, 500);
};

watch(searchQuery, () => {
    performSearch();
});
</script>

<template>
  <Head title="Search" />
  <div class="min-h-screen bg-background pb-20">
    <!-- Header with Search Input -->
    <header class="h-20 flex items-end pb-3 px-4 border-b border-border sticky top-0 bg-background/90 backdrop-blur-xl z-40">
        <div class="relative w-full shadow-sm rounded-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <SearchIcon class="h-5 w-5 text-muted-foreground" />
            </div>
            <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Search products, brands..." 
                class="block w-full pl-12 pr-4 py-3.5 bg-muted/80 border border-border rounded-full text-foreground font-bold placeholder-muted-foreground focus:ring-2 focus:ring-accent focus:bg-background outline-none transition-all shadow-inner"
                autofocus
            />
        </div>
    </header>

    <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center p-8 mt-24 text-center animate-in zoom-in-95 fade-in duration-500">
        <div class="w-24 h-24 bg-muted border border-border shadow-inner rounded-full flex items-center justify-center mb-6">
            <SearchIcon class="w-10 h-10 text-muted-foreground" />
        </div>
        <h2 class="text-2xl font-black mb-2">No results found</h2>
        <p class="text-muted-foreground text-lg">Try searching for something else or browse the feed.</p>
    </div>

    <!-- Search Results Grid (Reels view) -->
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1 p-1 mt-1">
        <Link 
            v-for="product in products.data" 
            :key="product.id" 
            :href="`/products/${product.id}`"
            class="relative aspect-[3/4] bg-muted overflow-hidden group animate-in zoom-in-95 fade-in duration-300"
            :style="{ animationDelay: `${(product.id % 5) * 50}ms` }"
        >
            <img v-if="product.media?.[0]" :src="product.media[0].path.startsWith('http') ? product.media[0].path : `/storage/${product.media[0].path}`" class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-700" />
            
            <div v-if="product.media?.[0]?.type === 'video'" class="absolute top-2 right-2 flex items-center space-x-1 bg-black/60 backdrop-blur-md px-2 py-1 rounded shadow-sm">
                 <Play class="w-3 h-3 text-white" fill="currentColor" />
                 <span class="text-white text-xs font-bold">{{ product.views_count || 0 }}</span>
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-100 flex flex-col justify-end p-3">
                <p class="text-white font-bold line-clamp-2 text-sm drop-shadow-md mb-0.5">{{ product.title }}</p>
                <p class="text-accent font-extrabold text-sm drop-shadow-sm">{{ product.price }} {{ product.currency }}</p>
            </div>
        </Link>
    </div>
  </div>
</template>
