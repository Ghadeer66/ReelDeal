<template>
  <div class="min-h-screen bg-[#111111] py-12 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-3xl mx-auto">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-white">Pick your interests</h2>
        <p class="mt-2 text-gray-400">We'll use this to personalize your feed.</p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
        <button 
          v-for="category in categories" 
          :key="category.id"
          @click="toggleInterest(category.id)"
          :class="[
            'py-4 px-6 border-2 rounded-xl transition-all duration-200 text-center font-medium',
            selectedInterests.includes(category.id) 
              ? 'border-green-400 bg-green-400/10 text-green-400' 
              : 'border-gray-700 bg-[#1a1a18] text-gray-300 hover:border-gray-500'
          ]"
        >
          {{ category.name }}
        </button>
      </div>

      <div class="flex justify-between items-center mt-10 border-t border-gray-800 pt-6">
        <button 
          @click="router.push({name: 'feed'})"
          class="text-gray-400 hover:text-white"
        >
          Skip for now
        </button>
        <button 
          @click="saveInterests"
          :disabled="selectedInterests.length === 0"
          class="bg-green-400 text-black px-8 py-3 rounded-full font-bold hover:bg-green-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Continue
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
// Note: Category API logic needs to be implemented in a service later
// Stubs for display:
const categories = ref([
  { id: '1', name: 'Electronics' },
  { id: '2', name: 'Fashion' },
  { id: '3', name: 'Home & Garden' },
  { id: '4', name: 'Sports' },
  { id: '5', name: 'Gaming' }
])
const selectedInterests = ref<string[]>([])
const router = useRouter()

const toggleInterest = (id: string) => {
  const index = selectedInterests.value.indexOf(id)
  if (index === -1) {
    selectedInterests.value.push(id)
  } else {
    selectedInterests.value.splice(index, 1)
  }
}

const saveInterests = async () => {
  // TODO: Call API to save interests
  router.push({ name: 'feed' })
}
</script>
