<script setup lang="ts">
import { RouterLink, useRoute } from 'vue-router';
import { Home, Search, ShoppingCart, Bookmark } from 'lucide-vue-next';
import { computed } from 'vue';

const route = useRoute();
const currentPath = computed(() => route.path);

const tabs = [
    { name: 'Home', path: '/', icon: Home },
    { name: 'Search', path: '/search', icon: Search },
    { name: 'Cart', path: '/cart', icon: ShoppingCart },
    { name: 'Saves', path: '/saves', icon: Bookmark },
];
</script>

<template>
    <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-[#1a1a18] border-t border-white/5 safe-area-bottom">
        <div class="grid h-full max-w-lg grid-cols-4 mx-auto">
            <RouterLink 
                v-for="tab in tabs" 
                :key="tab.name"
                :to="tab.path"
                class="inline-flex flex-col items-center justify-center group"
            >
                <div class="flex flex-col items-center transition-all duration-300" 
                     :class="{'text-primary': currentPath === tab.path, 'text-white/40': currentPath !== tab.path}">
                    <component 
                        :is="tab.icon" 
                        class="w-6 h-6 mb-1 transition-transform group-active:scale-90" 
                        :stroke-width="currentPath === tab.path ? 2.5 : 2"
                    />
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] transition-colors">{{ tab.name }}</span>
                </div>
                <div v-if="currentPath === tab.path" class="absolute bottom-1 w-1 h-1 bg-primary rounded-full"></div>
            </RouterLink>
        </div>
    </div>
</template>
