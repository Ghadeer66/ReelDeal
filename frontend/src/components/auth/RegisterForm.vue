<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
      <input 
        id="name" 
        v-model="name" 
        type="text" 
        required 
        class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-green-500 focus:ring-green-500"
      />
    </div>

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
        minlength="8"
        class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-green-500 focus:ring-green-500"
      />
    </div>

    <div>
      <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
      <input 
        id="password_confirmation" 
        v-model="password_confirmation" 
        type="password" 
        required 
        minlength="8"
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
      <span v-else>{{ $t('auth.register') }}</span>
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/useAuth'

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')

const authStore = useAuthStore()
const router = useRouter()

const handleSubmit = async () => {
  if (password.value !== password_confirmation.value) {
    authStore.error = "Passwords do not match"
    return
  }

  const success = await authStore.register({
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: password_confirmation.value,
    user_type: 'customer'
  })
  
  if (success) {
    router.push({ name: 'interests' })
  }
}
</script>
