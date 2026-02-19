<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto p-4 flex items-center justify-between">
      <h1 class="text-2xl font-black text-blue-600 tracking-tight">Toys Academy</h1>

      <nav class="space-x-4 text-sm font-medium text-slate-600">
        <RouterLink to="/home" class="hover:text-blue-600">Accueil</RouterLink>
        <RouterLink to="/catalogue" class="hover:text-blue-600">Catalogue</RouterLink>
        <RouterLink to="/login" class="hover:text-blue-600">Connexion</RouterLink>
      </nav>
    </div>
  </header>
</template>

