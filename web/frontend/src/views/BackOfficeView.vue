<script setup>
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()

const links = [
  { to: '/back-office/dashboard', label: 'Dashboard', icon: 'dashboard' },
  { to: '/back-office/campaigns', label: 'Campagnes', icon: 'campaign' },
  { to: '/back-office/subscribers', label: 'Abonnés', icon: 'group' },
  { to: '/back-office/articles', label: 'Articles', icon: 'inventory_2' },
]

function isActive(path) {
  return route.path === path || route.path.startsWith(path + '/')
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <h2 class="text-xl font-semibold text-slate-800 mb-6">Back-office</h2>

    <div class="flex flex-col sm:flex-row gap-6">
      <aside class="w-full sm:w-48 shrink-0">
        <nav class="rounded-lg border border-slate-200 bg-white p-2">
          <RouterLink
            v-for="item in links"
            :key="item.to"
            :to="item.to"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors',
              isActive(item.to) ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">{{ item.icon }}</span>
            {{ item.label }}
          </RouterLink>
        </nav>
      </aside>
      <main class="min-w-0 flex-1">
        <router-view />
      </main>
    </div>
  </div>
</template>
