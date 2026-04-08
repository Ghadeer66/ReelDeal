<template>
  <div class="flex flex-col min-h-screen">
    <div class="flex-1">
      <router-view></router-view>
    </div>
    <BottomTabBar v-if="showBottomNav" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import BottomTabBar from '@/components/BottomTabBar.vue';

const route = useRoute();
// Only show bottom nav on main app routes, not on auth or onboarding
const showBottomNav = computed(() => {
    const authRoutes = ['/login', '/register', '/onboarding'];
    return !authRoutes.includes(route.path);
});
</script>
