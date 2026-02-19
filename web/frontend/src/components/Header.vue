<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore.js'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const menuOpen = ref(false)
const userMenuOpen = ref(false)
const userMenuRef = ref(null)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isAdmin = computed(() => authStore.user?.role === 'admin')
const userLabel = computed(() => {
  const u = authStore.user
  if (!u) return ''
  const first = (u.first_name || '').trim()
  const last = (u.last_name || '').trim()
  if (first && last) return `${first} ${last}`
  return first || last || u.email || 'Compte'
})

function isActive(path) {
  return route.path === path || (path !== '/home' && route.path.startsWith(path))
}

function closeMenu() {
  menuOpen.value = false
  userMenuOpen.value = false
}

function logout() {
  authStore.logout()
  userMenuOpen.value = false
  closeMenu()
  router.push('/home')
}

function onDocumentClick(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', onDocumentClick)
})
onUnmounted(() => {
  document.removeEventListener('click', onDocumentClick)
})
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
            v-if="isAdmin"
            to="/back-office"
            :class="[
              'flex items-center gap-1.5 px-3 py-2 rounded-md font-medium transition-colors',
              isActive('/back-office') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
            Back-office
          </RouterLink>
          <!-- Connecté : menu utilisateur -->
          <div v-if="isAuthenticated" ref="userMenuRef" class="relative">
            <button
              type="button"
              @click="userMenuOpen = !userMenuOpen"
              class="flex items-center gap-1.5 px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-100 transition-colors"
            >
              <span class="material-symbols-outlined text-[20px]">person</span>
              <span class="max-w-[260px] truncate" :title="userLabel">{{ userLabel }}</span>
              <span class="material-symbols-outlined text-[18px]">expand_more</span>
            </button>
            <div
              v-show="userMenuOpen"
              class="absolute right-0 top-full mt-1 py-1 w-48 rounded-lg border border-slate-200 bg-white shadow-lg z-30"
            >
              <RouterLink
                to="/profil"
                @click="closeMenu"
                class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
              >
                <span class="material-symbols-outlined text-[18px]">person</span>
                Mon profil
              </RouterLink>
              <RouterLink
                to="/settings"
                @click="closeMenu"
                class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50"
              >
                <span class="material-symbols-outlined text-[18px]">settings</span>
                Paramètres
              </RouterLink>
              <button
                type="button"
                @click="logout"
                class="flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded-b-lg"
              >
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Se déconnecter
              </button>
            </div>
          </div>
          <RouterLink
            v-else
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
            v-if="isAdmin"
            to="/back-office"
            @click="closeMenu"
            :class="[
              'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-colors',
              isActive('/back-office') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined text-[22px]">admin_panel_settings</span>
            Back-office
          </RouterLink>
          <template v-if="isAuthenticated">
            <div class="px-4 py-2 text-xs font-medium text-slate-500 border-t border-slate-100 mt-2 pt-3">
              {{ userLabel }}
            </div>
            <RouterLink
              to="/profil"
              @click="closeMenu"
              class="flex items-center gap-2 px-4 py-3 rounded-md font-medium text-slate-600 hover:bg-slate-50"
            >
              <span class="material-symbols-outlined text-[22px]">person</span>
              Mon profil
            </RouterLink>
            <RouterLink
              to="/settings"
              @click="closeMenu"
              class="flex items-center gap-2 px-4 py-3 rounded-md font-medium text-slate-600 hover:bg-slate-50"
            >
              <span class="material-symbols-outlined text-[22px]">settings</span>
              Paramètres
            </RouterLink>
            <button
              type="button"
              @click="logout"
              class="flex items-center gap-2 px-4 py-3 rounded-md font-medium text-slate-600 hover:bg-slate-50 w-full text-left"
            >
              <span class="material-symbols-outlined text-[22px]">logout</span>
              Se déconnecter
            </button>
          </template>
          <RouterLink
            v-else
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

