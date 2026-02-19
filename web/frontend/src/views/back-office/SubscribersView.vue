<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/index.js'

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const subscribersPath = () => `${base}/subscribers`

const categoriesMap = {
  SOC: 'Jeux de société',
  FIG: 'Figurines / Poupées',
  CON: 'Construction',
  EXT: 'Extérieur',
  EVL: 'Éveil / Éducatif',
  LIV: 'Livres'
}

const subscribers = ref([])
const loading = ref(false)
const error = ref(null)

function formatPrefs(prefs) {
  if (!Array.isArray(prefs)) return '—'
  return prefs.map(p => categoriesMap[p] || p).join(', ') || '—'
}

async function fetchSubscribers() {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(subscribersPath())
    subscribers.value = Array.isArray(res.data) ? res.data : []
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur chargement abonnés'
    subscribers.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchSubscribers())
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-6">
    <h3 class="text-lg font-medium text-slate-800 mb-2">Abonnés</h3>
    <p class="text-sm text-slate-500 mb-6">Liste des abonnés avec tranche d'âge et préférences.</p>

    <div v-if="error" class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
      {{ error }}
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-slate-600"></div>
    </div>

    <div v-else-if="subscribers.length === 0" class="text-slate-500 text-sm py-4">
      Aucun abonné.
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-slate-600 border-b border-slate-200">
          <tr>
            <th class="py-2 pr-4 font-medium">Nom</th>
            <th class="py-2 pr-4 font-medium">Prénom</th>
            <th class="py-2 pr-4 font-medium">Email</th>
            <th class="py-2 pr-4 font-medium">Tranche d'âge</th>
            <th class="py-2 font-medium">Préférences</th>
          </tr>
        </thead>
        <tbody class="text-slate-700">
          <tr
            v-for="s in subscribers"
            :key="s.id"
            class="border-b border-slate-100 hover:bg-slate-50/50"
          >
            <td class="py-3 pr-4">{{ s.last_name }}</td>
            <td class="py-3 pr-4">{{ s.first_name }}</td>
            <td class="py-3 pr-4">{{ s.email }}</td>
            <td class="py-3 pr-4">{{ s.child_age_range || '—' }}</td>
            <td class="py-3">{{ formatPrefs(s.preferences) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
