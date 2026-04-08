<template>
  <div class="min-h-screen bg-[#111111] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-green-400">ReelDeal</h2>
      <p class="mt-2 text-center text-sm text-gray-400">Sign in to discover products</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-[#1a1a18] py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <LoginForm />
        <div class="mt-6 text-center text-sm">
          <router-link to="/register" class="font-medium text-green-400 hover:text-green-500">
            Don't have an account? Sign up
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import LoginForm from '@/components/auth/LoginForm.vue'
</script>
