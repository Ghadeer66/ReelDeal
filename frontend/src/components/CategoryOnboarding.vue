<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Check } from 'lucide-vue-next';

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits(['complete']);

const categories = ref<any[]>([]);
const selectedIds = ref<number[]>([]);
const isLoading = ref(true);
const isSaving = ref(false);

const toggleCategory = (id: number) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(i => i !== id);
    } else {
        selectedIds.value.push(id);
    }
};

onMounted(async () => {
    if (props.isOpen) {
        try {
            const { data } = await axios.get('/categories');
            categories.value = data.categories;
        } catch (e) {
            console.error(e);
        } finally {
            isLoading.value = false;
        }
    }
});

const saveInterests = async () => {
    isSaving.value = true;
    try {
        await axios.patch('/profile', {
            interests: JSON.stringify(selectedIds.value)
        });
        emit('complete');
    } catch (e) {
        console.error(e);
        // Error handling fallback
        emit('complete'); 
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-background/95 backdrop-blur-xl">
        <div class="w-full max-w-lg overflow-hidden flex flex-col items-center animate-in zoom-in-95 duration-700 h-full max-h-[85vh] pt-12">
            <h1 class="text-4xl font-black mb-3 text-center tracking-tight">What are you into?</h1>
            <p class="text-muted-foreground font-medium text-lg text-center mb-8 px-4 leading-relaxed">
                Select at least 3 categories so we can personalize your Reel feed perfectly.
            </p>
            
            <div v-if="isLoading" class="flex justify-center my-12">
                <div class="w-12 h-12 border-4 border-muted border-t-accent rounded-full animate-spin"></div>
            </div>
            
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-3 w-full mb-8 overflow-y-auto hide-scrollbar pb-12 px-2 flex-1 items-start">
                <button 
                    v-for="cat in categories" 
                    :key="cat.id" 
                    @click="toggleCategory(cat.id)"
                    class="relative p-5 rounded-3xl border border-transparent transition-all flex flex-col items-center justify-center space-y-3 group active:scale-95"
                    :class="selectedIds.includes(cat.id) ? 'bg-accent/15 shadow-[0_0_0_3px_hsl(var(--accent))] shadow-accent/20' : 'bg-muted/50 hover:bg-muted'"
                >
                    <div v-if="selectedIds.includes(cat.id)" class="absolute top-3 right-3 bg-accent rounded-full p-1 animate-in zoom-in">
                        <Check class="w-3.5 h-3.5 text-white stroke-[3]" />
                    </div>
                    <span class="text-4xl mb-1">{{ cat.icon || '🛍️' }}</span>
                    <span class="font-bold text-sm tracking-tight" :class="selectedIds.includes(cat.id) ? 'text-foreground font-black' : 'text-muted-foreground group-hover:text-foreground'">{{ cat.name_en }}</span>
                </button>
            </div>
            
            <div class="w-full mt-auto pt-4 relative bg-background">
                <div class="absolute -top-12 left-0 w-full h-12 bg-gradient-to-t from-background to-transparent pointer-events-none"></div>
                <button 
                    @click="saveInterests"
                    class="w-full bg-foreground text-background font-black text-xl py-4.5 rounded-full flex items-center justify-center disabled:opacity-50 disabled:bg-muted disabled:text-muted-foreground transition-all active:scale-95 shadow-xl"
                    :disabled="selectedIds.length < 1 || isSaving"
                >
                    <span v-if="isSaving">Saving...</span>
                    <span v-else-if="selectedIds.length < 3">Select {{ 3 - selectedIds.length }} more</span>
                    <span v-else>Continue to Feed</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar { display: none; }
</style>
