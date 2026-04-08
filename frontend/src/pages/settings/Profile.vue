<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { User, Mail, Shield, LogOut, ArrowLeft, Loader2 } from 'lucide-vue-next';
import api from '@/services/api';

const router = useRouter();
const user = ref<any>(null);
const loading = ref(true);

const fetchUser = async () => {
    try {
        const { data } = await api.get('/auth/me');
        user.value = data;
    } catch (error) {
        console.error('Failed to fetch user:', error);
        // If unauthenticated, redirect to login
        router.push('/login');
    } finally {
        loading.value = false;
    }
};

const handleLogout = async () => {
    try {
        await api.post('/auth/logout');
        router.push('/login');
    } catch (error) {
        console.error('Logout failed:', error);
    }
};

onMounted(() => {
    fetchUser();
});
</script>

<template>
    <div class="min-h-screen bg-background pb-32">
        <header class="h-16 flex items-center px-4 border-b border-white/5 sticky top-0 bg-[#1a1a18]/90 backdrop-blur-xl z-40">
            <button @click="router.back()" class="p-2 -ml-2 text-white/70 hover:text-white">
                <ArrowLeft class="w-6 h-6" />
            </button>
            <h1 class="font-bold text-lg text-white ml-2">Profile Settings</h1>
        </header>

        <div v-if="loading" class="flex items-center justify-center p-20">
            <Loader2 class="w-8 h-8 text-primary animate-spin" />
        </div>

        <div v-else-if="user" class="p-6 space-y-8 animate-in fade-in duration-500">
            <!-- User Info Card -->
            <div class="flex items-center space-x-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                <div class="w-20 h-20 rounded-full border-2 border-primary p-1">
                    <img 
                        :src="user.avatar_url || 'https://ui-avatars.com/api/?name=' + user.name" 
                        class="w-full h-full rounded-full object-cover"
                    />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ user.name }}</h2>
                    <p class="text-white/40 text-sm">{{ user.email }}</p>
                </div>
            </div>

            <!-- Settings Sections -->
            <div class="space-y-4">
                <div class="bg-white/5 rounded-2xl border border-white/5 overflow-hidden">
                    <button class="w-full flex items-center justify-between p-4 hover:bg-white/10 transition-colors text-white border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <User class="w-5 h-5 text-primary" />
                            <span class="font-medium">Edit Profile</span>
                        </div>
                        <ArrowLeft class="w-4 h-4 rotate-180 opacity-20" />
                    </button>
                    <button class="w-full flex items-center justify-between p-4 hover:bg-white/10 transition-colors text-white border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <Mail class="w-5 h-5 text-primary" />
                            <span class="font-medium">Email Preferences</span>
                        </div>
                        <ArrowLeft class="w-4 h-4 rotate-180 opacity-20" />
                    </button>
                    <button class="w-full flex items-center justify-between p-4 hover:bg-white/10 transition-colors text-white">
                        <div class="flex items-center gap-3">
                            <Shield class="w-5 h-5 text-primary" />
                            <span class="font-medium">Security</span>
                        </div>
                        <ArrowLeft class="w-4 h-4 rotate-180 opacity-20" />
                    </button>
                </div>

                <button 
                    @click="handleLogout" 
                    class="w-full flex items-center justify-center gap-2 p-4 rounded-2xl bg-red-500/10 text-red-500 font-bold hover:bg-red-500/20 transition-colors"
                >
                    <LogOut class="w-5 h-5" />
                    Logout
                </button>
            </div>
        </div>
    </div>
</template>
