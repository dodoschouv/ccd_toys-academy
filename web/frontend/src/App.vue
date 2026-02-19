<script setup>
import { RouterView } from 'vue-router'
import Header from './components/Header.vue'
</script>

<template>
  <div class="min-h-screen bg-slate-50">
    <Header />

    <main class="pt-[3.5rem] pb-6">
      <router-view />
    </main>
  </div>
</template>

