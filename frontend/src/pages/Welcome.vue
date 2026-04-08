<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Play, ShoppingBag, ShieldCheck, Sparkles } from 'lucide-vue-next';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <Head title="Welcome to ReelDeal" />

    <div class="min-h-screen bg-background text-foreground flex flex-col font-sans selection:bg-accent selection:text-accent-foreground overflow-hidden">
        <!-- Navigation -->
        <header class="w-full py-6 px-6 sm:px-12 flex justify-between items-center z-50 absolute top-0 left-0">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-accent rounded-full flex justify-center items-center shadow-lg shadow-accent/20">
                    <Play fill="currentColor" class="w-4 h-4 ml-0.5 text-accent-foreground" />
                </div>
                <span class="text-xl font-black tracking-tight text-white drop-shadow-md">ReelDeal</span>
            </div>
            
            <nav v-if="canLogin" class="flex flex-1 justify-end space-x-6 items-center">
                <Link
                    v-if="$page.props.auth.user"
                    href="/dashboard"
                    class="font-bold border-2 border-white/20 text-white px-5 py-2 hover:bg-white/10 rounded-full transition-colors backdrop-blur-md"
                >
                    Dashboard
                </Link>

                <template v-else>
                    <Link
                        href="/login"
                        class="font-bold text-white/90 hover:text-white transition-colors drop-shadow"
                    >
                        Log in
                    </Link>

                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="font-bold bg-white text-black px-6 py-2.5 rounded-full hover:bg-white/90 transition-colors shadow-xl"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="relative flex-1 flex flex-col items-center justify-center pt-32 pb-20 px-6 sm:px-12 text-center bg-black min-h-screen">
            <!-- Background Video/Gradient -->
            <div class="absolute inset-0 w-full h-full overflow-hidden pointer-events-none opacity-40">
                <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-black to-black z-10"></div>
                <!-- Abstract glowing balls for background style -->
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-accent/30 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative z-20 max-w-4xl mx-auto flex flex-col items-center">
                <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-md border border-white/10 px-4 py-2 rounded-full mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                    <span class="flex h-2 w-2 rounded-full bg-accent animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-white">V1 Early Access Live</span>
                </div>

                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white leading-[1.1] mb-8 tracking-tighter animate-in fade-in slide-in-from-bottom-8 duration-700 delay-150">
                    Scroll. Discover. <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-accent to-[#ff8c42]">Buy instantly.</span>
                </h1>
                
                <p class="text-lg md:text-2xl text-gray-300 font-medium max-w-2xl mb-12 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-300 leading-relaxed">
                    Experience shopping through short-form video reels. The most addictive way to discover unique items and sell your own.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-4 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-500 w-full justify-center">
                    <Link href="/" class="w-full sm:w-auto bg-accent text-accent-foreground px-8 py-4.5 rounded-full font-black text-lg flex items-center justify-center space-x-3 hover:scale-105 transition-transform shadow-2xl shadow-accent/20">
                        <ShoppingBag class="w-5 h-5 pb-0.5" />
                        <span>Start Browsing Reels</span>
                    </Link>
                    <Link href="/products/create" class="w-full sm:w-auto bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-4.5 rounded-full font-bold text-lg flex items-center justify-center space-x-3 hover:bg-white/20 transition-colors">
                        <div class="w-6 h-6 rounded-full border-2 border-white flex justify-center items-center">
                            <Play fill="currentColor" class="w-2.5 h-2.5 ml-0.5" />
                        </div>
                        <span>Sell Your Products</span>
                    </Link>
                </div>
            </div>
        </main>

        <!-- Features -->
        <section class="py-24 bg-background px-6 sm:px-12 border-t border-border/50">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-4xl font-black mb-16 tracking-tight">Why ReelDeal?</h2>
                <div class="grid md:grid-cols-3 gap-12">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-muted border border-border rounded-2xl flex items-center justify-center mb-6 shadow-sm shadow-muted-foreground/10 rotate-3">
                            <Play class="w-10 h-10 text-foreground" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">Reel Shopping</h3>
                        <p class="text-muted-foreground font-medium leading-relaxed text-center px-4">See products in action before you buy. Infinite scroll through high-quality vertical videos.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-muted border border-border rounded-2xl flex items-center justify-center mb-6 shadow-sm shadow-muted-foreground/10 -rotate-3">
                            <ShieldCheck class="w-10 h-10 text-foreground" />
                        </div>
                        <h3 class="text-xl font-bold mb-3">Secure Escrow</h3>
                        <p class="text-muted-foreground font-medium leading-relaxed text-center px-4">Your money is safe. We hold funds securely until you receive and approve the physical item.</p>
                    </div>
                    <div class="flex flex-col items-center relative">
                        <div class="w-20 h-20 bg-accent/10 border border-accent/20 rounded-2xl flex items-center justify-center mb-6 shadow-xl shadow-accent/5 rotate-6 relative">
                            <div class="absolute -top-1 -right-1 flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-accent"></span>
                            </div>
                            <Sparkles class="w-10 h-10 text-accent" />
                        </div>
                        <h3 class="text-xl font-bold mb-3 flex items-center gap-2">
                            AI Videos 
                            <span class="text-[10px] font-black uppercase tracking-widest bg-accent text-accent-foreground px-2 py-1 rounded-full">Soon</span>
                        </h3>
                        <p class="text-muted-foreground font-medium leading-relaxed text-center px-4">Upload photos and let AI generate stunning music-synced reels for your listings automatically.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-muted text-center border-t border-border">
            <div class="flex justify-center items-center gap-2 mb-6 opacity-30">
                <div class="w-8 h-8 bg-foreground rounded-full flex justify-center items-center">
                    <Play fill="currentColor" class="w-3 h-3 ml-0.5 text-background" />
                </div>
                <span class="text-2xl font-black tracking-tight text-foreground">ReelDeal</span>
            </div>
            <p class="text-muted-foreground font-bold text-sm uppercase tracking-widest">V1 &middot; Built with Laravel 13 & Vue 3</p>
            <div class="flex justify-center space-x-6 mt-6 text-sm font-medium text-muted-foreground/60">
                <span class="hover:text-foreground transition-colors">Laravel v{{ laravelVersion || '13.x' }}</span>
                <span>&bull;</span>
                <span class="hover:text-foreground transition-colors">PHP v{{ phpVersion || '8.2+' }}</span>
            </div>
        </footer>
    </div>
</template>
