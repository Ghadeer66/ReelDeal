<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Home, Search, ShoppingCart, Bookmark } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const currentPath = computed(() => page.url);

const tabs = [
    { name: 'Home', path: '/', icon: Home },
    { name: 'Search', path: '/search', icon: Search },
    { name: 'Cart', path: '/cart', icon: ShoppingCart },
    { name: 'Saved', path: '/bookmarks', icon: Bookmark },
];
</script>

<template>
    <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-background border-t border-border">
        <div class="grid h-full max-w-lg grid-cols-4 mx-auto font-medium">
            <Link 
                v-for="tab in tabs" 
                :key="tab.name"
                :href="tab.path"
                class="inline-flex flex-col items-center justify-center px-5 hover:bg-muted group transition-all duration-200"
                :class="{'text-accent border-t-2 border-accent': currentPath.startsWith(tab.path) && (tab.path !== '/' || currentPath === '/'), 'text-muted-foreground': !(currentPath.startsWith(tab.path) && (tab.path !== '/' || currentPath === '/'))}"
            >
                <component 
                    :is="tab.icon" 
                    class="w-6 h-6 mb-1 transition-transform group-hover:scale-110" 
                    :class="{'text-accent': currentPath.startsWith(tab.path) && (tab.path !== '/' || currentPath === '/')}" 
                />
                <span class="text-xs">{{ tab.name }}</span>
            </Link>
        </div>
    </div>
</template>
