<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label for="email" class="block text-sm font-medium text-gray-300">{{ $t('auth.email') }}</label>
      <input 
        id="email" 
        v-model="email" 
        type="email" 
        required 
        class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-green-500 focus:ring-green-500"
      />
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-300">{{ $t('auth.password') }}</label>
      <input 
        id="password" 
        v-model="password" 
        type="password" 
        required 
        class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-green-500 focus:ring-green-500"
      />
    </div>

    <div v-if="authStore.error" class="text-red-500 text-sm">
      {{ authStore.error }}
    </div>

    <button 
      type="submit" 
      :disabled="authStore.loading"
      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-black bg-green-400 hover:bg-green-500 focus:outline-none disabled:opacity-50"
    >
      <span v-if="authStore.loading">Loading...</span>
      <span v-else>{{ $t('auth.login') }}</span>
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/useAuth'

const email = ref('')
const password = ref('')

const authStore = useAuthStore()
const router = useRouter()

const handleSubmit = async () => {
  const success = await authStore.login({
    email: email.value,
    password: password.value
  })
  
  if (success) {
    router.push({ name: 'feed' })
  }
}
</script>
