<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Bookmark, Play } from 'lucide-vue-next';

const props = defineProps<{
    products: any[];
}>();
</script>

<template>
  <Head title="Saved Reels" />
  <div class="min-h-screen bg-background pb-20">
    <header class="h-14 flex items-center px-4 border-b border-border sticky top-0 bg-background/90 backdrop-blur-md z-40">
        <h1 class="font-bold text-xl flex items-center gap-2">
            <Bookmark class="w-5 h-5 text-secondary" fill="currentColor" />
            Saved Reels
        </h1>
    </header>

    <div v-if="products.length === 0" class="flex flex-col items-center justify-center p-8 mt-20 text-center animate-in fade-in">
        <div class="w-24 h-24 bg-muted rounded-full flex items-center justify-center mb-6">
            <Bookmark class="w-10 h-10 text-muted-foreground animate-pulse" />
        </div>
        <h2 class="text-2xl font-bold mb-2">No saved reels yet</h2>
        <p class="text-muted-foreground text-lg">Save reels you like to view them later.</p>
    </div>

    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1 p-1">
        <Link 
            v-for="product in products" 
            :key="product.id" 
            :href="`/products/${product.id}`"
            class="relative aspect-[3/4] bg-muted overflow-hidden group animate-in zoom-in-95 fade-in duration-300"
            :style="{ animationDelay: `${(product.id % 5) * 50}ms` }"
        >
            <img v-if="product.media?.[0]" :src="product.media[0].path.startsWith('http') ? product.media[0].path : `/storage/${product.media[0].path}`" class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-500" />
            
            <!-- Play Indicator for Video -->
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
