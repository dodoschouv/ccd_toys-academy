<script setup>
import { RouterView } from 'vue-router'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <Header />

    <main class="pt-[3.5rem] pb-6 flex-1">
      <router-view />
    </main>

    <Footer />
  </div>
</template>ss

