<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Lock, Sparkles } from 'lucide-vue-next';

defineProps<{
    isOpen: boolean;
}>();
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/80 backdrop-blur-md animate-in fade-in duration-500">
        <div class="bg-card w-full max-w-sm p-8 rounded-3xl shadow-2xl flex flex-col items-center text-center border border-border animate-in zoom-in-95 duration-500 delay-100 relative overflow-hidden">
            <!-- Decorative background glow -->
            <div class="absolute -top-20 -left-20 w-40 h-40 bg-accent/20 rounded-full blur-[50px]"></div>
            <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-secondary/20 rounded-full blur-[50px]"></div>

            <div class="relative z-10 w-20 h-20 bg-muted border border-border rounded-full flex justify-center items-center mb-6 shadow-inner">
                <Lock class="w-10 h-10 text-foreground" />
                <Sparkles class="w-5 h-5 text-accent absolute -top-1 -right-2 animate-bounce" />
            </div>
            
            <h2 class="relative z-10 text-3xl font-black mb-3">Keep Scrolling!</h2>
            <p class="relative z-10 text-muted-foreground font-medium mb-8 leading-relaxed text-[15px]">
                You've reached the end of the guest preview. Create a free account to unlock unlimited reels, save favorites, and buy instantly.
            </p>
            
            <Link href="/register" class="relative z-10 w-full bg-accent text-accent-foreground font-black tracking-wide py-4.5 rounded-full shadow-lg shadow-accent/20 active:scale-95 transition-transform mb-3 border border-accent">
                Create Free Account
            </Link>
            
            <Link href="/login" class="relative z-10 w-full bg-muted/80 text-foreground font-bold py-4 rounded-full border border-border active:scale-95 transition-colors">
                I already have an account
            </Link>
        </div>
    </div>
</template>
