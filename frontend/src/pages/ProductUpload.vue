<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    categories: any[];
}>();

const form = useForm({
    title: '',
    price: '',
    currency: 'SAR',
    category_id: '',
    condition: 'new',
    description: '',
    media: [] as File[]
});

const mediaPreviewUrls = ref<string[]>([]);
const mediaInput = ref<HTMLInputElement | null>(null);

const handleMediaUpload = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (!files) return;
    
    for (let i = 0; i < files.length; i++) {
        if (form.media.length >= 5) break;
        form.media.push(files[i]);
        mediaPreviewUrls.value.push(URL.createObjectURL(files[i]));
    }
};

const removeMedia = (index: number) => {
    form.media.splice(index, 1);
    URL.revokeObjectURL(mediaPreviewUrls.value[index]);
    mediaPreviewUrls.value.splice(index, 1);
};

const triggerFileInput = () => {
    mediaInput.value?.click();
};

const submit = () => {
    form.post('/products', {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};
</script>

<template>
  <Head title="Upload Product" />
  <div class="min-h-screen bg-background pb-32">
    <header class="h-14 flex items-center px-4 border-b border-border sticky top-0 bg-background/90 backdrop-blur-md z-40">
        <h1 class="font-bold text-xl">Sell an Item</h1>
    </header>

    <form @submit.prevent="submit" class="p-4 space-y-6 max-w-lg mx-auto">
        <!-- Media Upload Box -->
        <div>
            <label class="block text-sm font-bold text-muted-foreground uppercase tracking-wide mb-2">Product Media (Up to 5)</label>
            <div class="grid grid-cols-3 gap-2">
                <div 
                    v-for="(url, index) in mediaPreviewUrls" 
                    :key="index"
                    class="relative aspect-[3/4] rounded-2xl bg-muted overflow-hidden border border-border group"
                >
                    <img v-if="form.media[index].type.startsWith('image/')" :src="url" class="w-full h-full object-cover" />
                    <video v-else :src="url" class="w-full h-full object-cover" muted></video>
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <button @click.prevent="removeMedia(index)" class="absolute top-2 right-2 bg-black/60 backdrop-blur-md rounded-full p-1.5 shadow-sm hover:scale-110 transition-transform">
                        <X class="w-4 h-4 text-white" />
                    </button>
                    <div v-if="index === 0" class="absolute bottom-2 left-2 bg-accent text-accent-foreground text-[10px] font-black uppercase px-2 py-0.5 rounded shadow-sm">
                        Cover
                    </div>
                </div>

                <button 
                    v-if="form.media.length < 5"
                    @click.prevent="triggerFileInput"
                    class="aspect-[3/4] flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-muted-foreground/30 bg-muted/20 hover:bg-muted/50 text-muted-foreground transition-colors active:scale-95"
                >
                    <Upload class="w-8 h-8 mb-2 opacity-80" />
                    <span class="text-xs font-bold uppercase tracking-wider">Upload</span>
                </button>
            </div>
            <input type="file" ref="mediaInput" @change="handleMediaUpload" multiple accept="video/*, image/*" class="hidden" />
            <p v-if="form.errors.media" class="text-destructive text-sm mt-1 font-medium">{{ form.errors.media }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Title</label>
            <input v-model="form.title" type="text" placeholder="What are you selling?" class="w-full bg-muted/50 border border-border rounded-xl px-4 py-3.5 text-lg font-bold focus:ring-2 focus:ring-accent focus:bg-background outline-none transition-all placeholder:font-normal" required />
            <p v-if="form.errors.title" class="text-destructive text-sm mt-1 font-medium">{{ form.errors.title }}</p>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Price</label>
                <input v-model="form.price" type="number" step="0.01" placeholder="0.00" class="w-full bg-muted/50 border border-border rounded-xl px-4 py-3.5 text-xl font-black focus:ring-2 focus:ring-accent focus:bg-background outline-none transition-all placeholder:font-normal" required />
                <p v-if="form.errors.price" class="text-destructive text-sm mt-1 font-medium">{{ form.errors.price }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Currency</label>
                <div class="relative">
                    <select v-model="form.currency" class="w-full bg-muted/50 border border-border rounded-xl px-4 py-3.5 text-lg font-bold focus:ring-2 focus:ring-accent focus:bg-background outline-none appearance-none transition-all">
                        <option value="SAR">SAR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Category</label>
                <select v-model="form.category_id" class="w-full bg-muted/50 border border-border rounded-xl px-4 py-3.5 font-bold focus:ring-2 focus:ring-accent focus:bg-background outline-none appearance-none transition-all" required>
                    <option value="" disabled>Select...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name_en }}</option>
                </select>
                <p v-if="form.errors.category_id" class="text-destructive text-sm mt-1 font-medium">{{ form.errors.category_id }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Condition</label>
                <select v-model="form.condition" class="w-full bg-muted/50 border border-border rounded-xl px-4 py-3.5 font-bold focus:ring-2 focus:ring-accent focus:bg-background outline-none appearance-none transition-all">
                    <option value="new">New</option>
                    <option value="used">Used</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Description</label>
            <textarea v-model="form.description" rows="4" placeholder="Describe your item, brand, flaws, size, etc." class="w-full bg-muted/50 border border-border rounded-xl px-4 py-4 font-medium focus:ring-2 focus:ring-accent focus:bg-background outline-none resize-none transition-all"></textarea>
            <p v-if="form.errors.description" class="text-destructive text-sm mt-1 font-medium">{{ form.errors.description }}</p>
        </div>

        <div class="pt-6 pb-8">
            <button type="submit" :disabled="form.processing" class="w-full bg-accent text-accent-foreground font-black tracking-wide py-4.5 rounded-full shadow-xl shadow-accent/20 flex justify-center items-center gap-2 text-lg active:scale-95 transition-transform disabled:opacity-50 border border-accent">
                <span>{{ form.processing ? 'Uploading...' : 'Publish Item' }}</span>
            </button>
        </div>
    </form>
  </div>
</template>
