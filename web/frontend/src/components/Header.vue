<script setup>
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()

function isActive(path) {
  return route.path === path || (path !== '/home' && route.path.startsWith(path))
}
</script>

<template>
  <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex h-14 items-center justify-between">
        <RouterLink to="/home" class="text-lg font-semibold text-slate-900 tracking-tight">
          Toys Academy
        </RouterLink>

        <nav class="flex items-center gap-1 text-sm">
          <RouterLink
            to="/home"
            :class="[
              'flex items-center gap-1.5 px-3 py-2 rounded-md font-medium transition-colors',
              isActive('/home') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">home</span>
            Accueil
          </RouterLink>
          <RouterLink
            to="/catalogue"
            :class="[
              'flex items-center gap-1.5 px-3 py-2 rounded-md font-medium transition-colors',
              isActive('/catalogue') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
            Catalogue
          </RouterLink>
          <RouterLink
            to="/ma-box"
            :class="[
              'flex items-center gap-1.5 px-3 py-2 rounded-md font-medium transition-colors',
              isActive('/ma-box') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">card_giftcard</span>
            Ma box
          </RouterLink>
          <RouterLink
            to="/back-office"
            :class="[
              'flex items-center gap-1.5 px-3 py-2 rounded-md font-medium transition-colors',
              isActive('/back-office') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">settings</span>
            Back-office
          </RouterLink>
          <RouterLink
            to="/connexion"
            :class="[
              'flex items-center gap-1.5 px-3 py-2 rounded-md font-medium transition-colors',
              isActive('/connexion') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">login</span>
            Connexion
          </RouterLink>
        </nav>
      </div>
    </div>
  </header>
</template>

