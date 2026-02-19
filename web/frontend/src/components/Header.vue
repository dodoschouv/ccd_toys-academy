<script setup>
import { ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()
const menuOpen = ref(false)

function isActive(path) {
  return route.path === path || (path !== '/home' && route.path.startsWith(path))
}

function closeMenu() {
  menuOpen.value = false
}
</script>

<template>
  <header class="fixed top-0 left-0 right-0 z-20 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex h-14 items-center justify-start md:justify-center">
        <!-- Burger (mobile) -->
        <button
          type="button"
          @click="menuOpen = !menuOpen"
          class="md:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100"
          aria-label="Menu"
        >
          <span class="material-symbols-outlined text-[28px]">
            {{ menuOpen ? 'close' : 'menu' }}
          </span>
        </button>

        <!-- Nav (desktop) -->
        <nav class="hidden md:flex items-center gap-1 text-sm">
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

      <!-- Menu mobile déroulant -->
      <div
        v-show="menuOpen"
        class="md:hidden border-t border-slate-200 bg-white py-2"
      >
        <nav class="flex flex-col gap-0.5 text-sm">
          <RouterLink
            to="/home"
            @click="closeMenu"
            :class="[
              'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-colors',
              isActive('/home') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined text-[22px]">home</span>
            Accueil
          </RouterLink>
          <RouterLink
            to="/catalogue"
            @click="closeMenu"
            :class="[
              'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-colors',
              isActive('/catalogue') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined text-[22px]">inventory_2</span>
            Catalogue
          </RouterLink>
          <RouterLink
            to="/ma-box"
            @click="closeMenu"
            :class="[
              'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-colors',
              isActive('/ma-box') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined text-[22px]">card_giftcard</span>
            Ma box
          </RouterLink>
          <RouterLink
            to="/back-office"
            @click="closeMenu"
            :class="[
              'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-colors',
              isActive('/back-office') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined text-[22px]">settings</span>
            Back-office
          </RouterLink>
          <RouterLink
            to="/connexion"
            @click="closeMenu"
            :class="[
              'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-colors',
              isActive('/connexion') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined text-[22px]">login</span>
            Connexion
          </RouterLink>
        </nav>
      </div>
    </div>
  </header>
</template>

