<script setup lang="ts">
import { Search as SearchIcon, Play, ArrowLeft, SlidersHorizontal, X, MapPin } from 'lucide-vue-next';
import { ref, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { feedService, type ListingFeedItem } from '@/services/feed.service';
import api from '@/services/api';

const router = useRouter();
const route = useRoute();

const searchQuery = ref((route.query.q as string) || '');
const results = ref<ListingFeedItem[]>([]);
const loading = ref(false);
const categories = ref<any[]>([]);
const selectedCategoryId = ref<string | null>((route.query.category_id as string) || null);

const showFilters = ref(false);
const minPrice = ref<number | null>(route.query.min_price ? Number(route.query.min_price) : null);
const maxPrice = ref<number | null>(route.query.max_price ? Number(route.query.max_price) : null);
const selectedCondition = ref<'new' | 'used' | null>((route.query.condition as 'new' | 'used') || null);
const locationQuery = ref((route.query.location as string) || '');

let searchTimeout: any = null;
let locationTimeout: any = null;

const fetchCategories = async () => {
    try {
        const { data } = await api.get('/categories');
        categories.value = data.categories;
    } catch (error) {
        console.error('Failed to fetch categories:', error);
    }
};

const performSearch = async () => {
    loading.value = true;
    try {
        const filters = {
            category_id: selectedCategoryId.value || undefined,
            min_price: minPrice.value || undefined,
            max_price: maxPrice.value || undefined,
            condition: selectedCondition.value || undefined,
            location: locationQuery.value || undefined
        };
        const response = await feedService.searchFeed(searchQuery.value, null, filters);
        results.value = response.data;
        
        // Update URL
        const query: any = {};
        if (searchQuery.value) query.q = searchQuery.value;
        if (selectedCategoryId.value) query.category_id = selectedCategoryId.value;
        if (minPrice.value) query.min_price = minPrice.value;
        if (maxPrice.value) query.max_price = maxPrice.value;
        if (selectedCondition.value) query.condition = selectedCondition.value;
        if (locationQuery.value) query.location = locationQuery.value;
        
        router.replace({ query });
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        loading.value = false;
    }
};

const handleDebouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 300);
};

const handleDebouncedLocation = () => {
    clearTimeout(locationTimeout);
    locationTimeout = setTimeout(() => {
        performSearch();
    }, 500);
};

watch(searchQuery, handleDebouncedSearch);
watch(locationQuery, handleDebouncedLocation);
watch([selectedCategoryId, minPrice, maxPrice, selectedCondition], performSearch);

onMounted(() => {
    fetchCategories();
    if (searchQuery.value || selectedCategoryId.value || minPrice.value || maxPrice.value || selectedCondition.value || locationQuery.value) {
        performSearch();
    }
});

const toggleCategory = (id: string) => {
    selectedCategoryId.value = selectedCategoryId.value === id ? null : id;
};

const clearFilters = () => {
    selectedCategoryId.value = null;
    minPrice.value = null;
    maxPrice.value = null;
    selectedCondition.value = null;
    locationQuery.value = '';
    showFilters.value = false;
};

const goBack = () => {
    router.back();
};

const openResult = (listingId: string) => {
    router.push(`/products/${listingId}`);
};
</script>

<template>
  <div class="min-h-screen bg-background pb-32">
    <!-- Header with Search Input -->
    <header class="sticky top-0 bg-[#1a1a18]/90 backdrop-blur-xl z-40 border-b border-white/5">
        <div class="flex items-center w-full gap-3 p-4 pb-2">
            <button @click="goBack" class="p-2 -ml-2 text-white/70 hover:text-white transition-colors">
                <ArrowLeft class="w-6 h-6 " />
            </button>
            <div class="relative flex-1 shadow-sm rounded-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <SearchIcon class="h-4 w-4 text-white/40" />
                </div>
                <input 
                    v-model="searchQuery" 
                    type="text" 
                    placeholder="Search reels..." 
                    class="block w-full pl-10 pr-10 py-2.5 bg-white/5 border border-white/10 rounded-full text-white font-bold placeholder-white/20 focus:ring-2 focus:ring-primary focus:bg-white/10 outline-none transition-all shadow-inner text-sm"
                    autofocus
                />
                <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-3 flex items-center text-white/20 hover:text-white/50">
                    <X class="w-4 h-4" />
                </button>
            </div>
            <button 
                @click="showFilters = !showFilters" 
                class="p-2.5 rounded-full border border-white/10 transition-colors relative"
                :class="showFilters || selectedCategoryId || minPrice || maxPrice || selectedCondition || locationQuery ? 'bg-primary text-primary-foreground border-primary' : 'bg-white/5 text-white/70'"
            >
                <SlidersHorizontal class="w-5 h-5" />
                <div v-if="selectedCategoryId || minPrice || maxPrice || selectedCondition || locationQuery" class="absolute -top-1 -right-1 w-3 h-3 bg-white rounded-full border-2 border-primary"></div>
            </button>
        </div>

        <!-- Categories horizontal scroll -->
        <div class="w-full overflow-x-auto hide-scrollbar flex items-center space-x-2 px-4 py-3">
            <button 
                v-for="cat in categories" 
                :key="cat.id"
                @click="toggleCategory(cat.id)"
                class="whitespace-nowrap px-4 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider transition-all border"
                :class="selectedCategoryId === cat.id 
                    ? 'bg-primary border-primary text-primary-foreground scale-105 shadow-lg shadow-primary/20' 
                    : 'bg-white/5 border-white/10 text-white/40 hover:text-white hover:border-white/20'"
            >
                {{ cat.name_en }}
            </button>
        </div>

        <!-- Advanced Filters Drawer -->
        <div v-if="showFilters" class="px-4 py-6 bg-white/5 border-t border-white/5 animate-in slide-in-from-top duration-300">
            <div class="flex flex-col space-y-6 max-w-lg mx-auto">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Advanced Filters</h3>
                    <button @click="clearFilters" class="text-[10px] font-bold text-primary uppercase hover:underline">Reset All</button>
                </div>

                <!-- Condition Toggle -->
                <div class="space-y-3">
                    <span class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em]">Product Condition</span>
                    <div class="flex p-1 bg-black/40 rounded-xl border border-white/10">
                        <button 
                            @click="selectedCondition = selectedCondition === 'new' ? null : 'new'"
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
                            :class="selectedCondition === 'new' ? 'bg-primary text-primary-foreground shadow-lg' : 'text-white/40 hover:text-white'"
                        >
                            New
                        </button>
                        <button 
                            @click="selectedCondition = selectedCondition === 'used' ? null : 'used'"
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all"
                            :class="selectedCondition === 'used' ? 'bg-primary text-primary-foreground shadow-lg' : 'text-white/40 hover:text-white'"
                        >
                            Used
                        </button>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="space-y-3">
                    <span class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em]">Price Range</span>
                    <div class="flex items-center gap-3">
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-white/10 uppercase">Min</span>
                            <input 
                                v-model="minPrice" 
                                type="number" 
                                placeholder="0" 
                                class="w-full bg-black/40 border border-white/10 rounded-xl px-10 py-2.5 text-white font-bold outline-none focus:border-primary transition-colors text-sm"
                            />
                        </div>
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-white/10 uppercase">Max</span>
                            <input 
                                v-model="maxPrice" 
                                type="number" 
                                placeholder="Any" 
                                class="w-full bg-black/40 border border-white/10 rounded-xl px-10 py-2.5 text-white font-bold outline-none focus:border-primary transition-colors text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="space-y-3">
                    <span class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em]">Location</span>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <MapPin class="h-4 w-4 text-white/20" />
                        </div>
                        <input 
                            v-model="locationQuery" 
                            type="text" 
                            placeholder="City, region, or neighborhood..." 
                            class="block w-full pl-10 pr-4 py-2.5 bg-black/40 border border-white/10 rounded-xl text-white font-bold placeholder-white/10 focus:ring-2 focus:ring-primary outline-none transition-all text-sm"
                        />
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div v-if="loading && results.length === 0" class="flex flex-col items-center justify-center p-8 mt-24">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
    </div>

    <!-- Empty and No Results States -->
    <div v-else-if="(searchQuery || selectedCategoryId || minPrice || maxPrice || selectedCondition || locationQuery) && results.length === 0 && !loading" class="flex flex-col items-center justify-center p-8 mt-24 text-center animate-in zoom-in-95 duration-500">
        <div class="w-20 h-20 bg-white/5 border border-white/10 rounded-full flex items-center justify-center mb-6">
            <SearchIcon class="w-8 h-8 text-white/20" />
        </div>
        <h2 class="text-xl font-bold text-white mb-2">No matching reels</h2>
        <p class="text-white/40 text-sm">Try adjusting your filters or search terms.</p>
        <button @click="clearFilters" class="mt-6 text-primary font-bold text-sm uppercase">Reset Filters</button>
    </div>

    <div v-else-if="!searchQuery && !selectedCategoryId && !minPrice && !maxPrice && !selectedCondition && !locationQuery" class="flex flex-col items-center justify-center p-8 mt-24 text-center animate-in fade-in duration-700">
        <div class="w-24 h-24 bg-white/5 border border-white/10 rounded-full flex items-center justify-center mb-6 text-white/20 relative">
            <Play class="w-10 h-10 ml-1.5" />
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-primary rounded-full animate-pulse"></div>
        </div>
        <h2 class="text-2xl font-black text-white mb-2">Discover Reels</h2>
        <p class="text-white/40 text-sm max-w-[200px] leading-relaxed">Search or apply filters to start your discovery experience.</p>
    </div>

    <!-- Search Results Grid -->
    <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-1 p-1">
        <div 
            v-for="product in results" 
            :key="product.id" 
            @click="openResult(product.id)"
            class="relative aspect-[3/4] bg-neutral-900 overflow-hidden group cursor-pointer animate-in fade-in duration-500"
        >
            <img 
                v-if="product.thumbnail_url" 
                :src="product.thumbnail_url.startsWith('http') ? product.thumbnail_url : `/storage/${product.thumbnail_url}`" 
                class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-700" 
            />
            
            <div v-if="product.media_type === 'video'" class="absolute top-2 right-2 flex items-center space-x-1 bg-black/40 backdrop-blur-md px-1.5 py-0.5 rounded text-[10px]">
                 <Play class="w-2.5 h-2.5 text-white" fill="currentColor" />
                 <span class="text-white font-bold">{{ product.views_count || 0 }}</span>
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-100 flex flex-col justify-end p-3">
                <p class="text-white font-bold line-clamp-1 text-xs mb-0.5">{{ product.title_en }}</p>
                <div class="flex items-center justify-between">
                    <p class="text-primary font-black text-sm">{{ product.price }} {{ product.currency }}</p>
                    <span class="text-[9px] text-white/40 font-bold uppercase tracking-tighter">{{ product.condition }}</span>
                </div>
                <div v-if="product.location" class="flex items-center mt-1 text-[8px] text-white/30 uppercase tracking-widest">
                    <MapPin class="w-2 h-2 mr-1" />
                    {{ product.location }}
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>
