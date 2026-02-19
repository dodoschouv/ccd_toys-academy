<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/index.js'

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const dashboardPath = () => `${base}/admin/dashboard`
const subscribersPath = () => `${base}/subscribers`

const categoriesMap = {
  SOC: 'Jeux de société',
  FIG: 'Figurines / Poupées',
  CON: 'Construction',
  EXT: 'Extérieur',
  EVL: 'Éveil / Éducatif',
  LIV: 'Livres'
}

const stats = ref({ stock: 0, subscribers_count: 0, average_score: 0 })
const subscribers = ref([])
const loadingStats = ref(true)
const loadingSubscribers = ref(true)
const errorStats = ref(null)
const errorSubscribers = ref(null)

function formatPrefs(prefs) {
  if (!Array.isArray(prefs)) return '—'
  return prefs.map(p => categoriesMap[p] || p).join(', ') || '—'
}

async function fetchStats() {
  loadingStats.value = true
  errorStats.value = null
  try {
    const res = await api.get(dashboardPath())
    stats.value = res.data || { stock: 0, subscribers_count: 0, average_score: 0 }
  } catch (e) {
    errorStats.value = e.response?.data?.error || e.message || 'Erreur chargement des statistiques'
  } finally {
    loadingStats.value = false
  }
}

async function fetchSubscribers() {
  loadingSubscribers.value = true
  errorSubscribers.value = null
  try {
    const res = await api.get(subscribersPath())
    subscribers.value = Array.isArray(res.data) ? res.data : []
  } catch (e) {
    errorSubscribers.value = e.response?.data?.error || e.message || 'Erreur chargement des abonnés'
    subscribers.value = []
  } finally {
    loadingSubscribers.value = false
  }
}

onMounted(() => {
  fetchStats()
  fetchSubscribers()
})
</script>

<template>
  <div class="space-y-8">
    <div>
      <h3 class="text-lg font-semibold text-slate-800 mb-1">Tableau de bord</h3>
      <p class="text-sm text-slate-500">Vue d’ensemble du stock, des abonnés et des scores.</p>
    </div>

    <!-- Stats -->
    <div v-if="errorStats" class="p-4 rounded-lg bg-red-50 text-red-700 text-sm">
      {{ errorStats }}
    </div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div
        v-if="loadingStats"
        class="rounded-xl border border-slate-200 bg-white p-6 flex items-center justify-center min-h-[120px]"
      >
        <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-slate-600"></div>
      </div>
      <template v-else>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
              <span class="material-symbols-outlined text-[28px]">inventory_2</span>
            </span>
            <div>
              <p class="text-sm font-medium text-slate-500">Stock</p>
              <p class="text-2xl font-semibold text-slate-800">{{ stats.stock }}</p>
              <p class="text-xs text-slate-400">articles</p>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
              <span class="material-symbols-outlined text-[28px]">group</span>
            </span>
            <div>
              <p class="text-sm font-medium text-slate-500">Abonnés actifs</p>
              <p class="text-2xl font-semibold text-slate-800">{{ stats.subscribers_count }}</p>
              <p class="text-xs text-slate-400">inscrits</p>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
              <span class="material-symbols-outlined text-[28px]">star</span>
            </span>
            <div>
              <p class="text-sm font-medium text-slate-500">Score moyen</p>
              <p class="text-2xl font-semibold text-slate-800">{{ stats.average_score }}</p>
              <p class="text-xs text-slate-400">sur les box</p>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Liste des abonnés -->
    <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200">
        <h4 class="text-base font-semibold text-slate-800">Liste des abonnés</h4>
        <p class="text-sm text-slate-500 mt-0.5">Tous les abonnés avec tranche d’âge et préférences.</p>
      </div>

      <div v-if="errorSubscribers" class="p-4 text-red-600 text-sm">
        {{ errorSubscribers }}
      </div>
      <div v-else-if="loadingSubscribers" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-slate-600"></div>
      </div>
      <div v-else-if="subscribers.length === 0" class="px-6 py-8 text-slate-500 text-sm text-center">
        Aucun abonné.
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-6 py-3 font-semibold">Nom</th>
              <th class="px-6 py-3 font-semibold">Prénom</th>
              <th class="px-6 py-3 font-semibold">Email</th>
              <th class="px-6 py-3 font-semibold">Tranche d’âge</th>
              <th class="px-6 py-3 font-semibold">Préférences</th>
            </tr>
          </thead>
          <tbody class="text-slate-700 divide-y divide-slate-100">
            <tr
              v-for="s in subscribers"
              :key="s.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <td class="px-6 py-3 font-medium">{{ s.last_name }}</td>
              <td class="px-6 py-3">{{ s.first_name }}</td>
              <td class="px-6 py-3">{{ s.email }}</td>
              <td class="px-6 py-3">{{ s.child_age_range || '—' }}</td>
              <td class="px-6 py-3 text-slate-600">{{ formatPrefs(s.preferences) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
