<script setup lang="ts">
import BottomTabBar from '@/components/BottomTabBar.vue';
</script>

<template>
    <div class="min-h-screen bg-background text-foreground pb-16 safe-area-bottom selection:bg-accent selection:text-accent-foreground">
        <main class="h-full">
            <slot />
        </main>
        <BottomTabBar />
    </div>
</template>
