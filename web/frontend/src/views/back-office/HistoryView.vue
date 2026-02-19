<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/index.js'

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const historyPath = () => `${base}/admin/history`

const items = ref([])
const loading = ref(true)
const error = ref(null)

function formatDate(val) {
  if (!val) return '—'
  const d = new Date(val)
  return Number.isNaN(d.getTime()) ? val : d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

async function fetchHistory() {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(historyPath())
    items.value = Array.isArray(res.data) ? res.data : []
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur chargement de l\'historique'
    items.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchHistory())
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
      <h3 class="text-lg font-semibold text-slate-800">Historique global</h3>
      <p class="text-sm text-slate-500 mt-0.5">Vue d’ensemble de toutes les campagnes avec nombre de box validées, articles distribués et score moyen.</p>
    </div>

    <div v-if="error" class="p-4 text-red-600 text-sm">
      {{ error }}
    </div>
    <div v-else-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-slate-600"></div>
    </div>
    <div v-else-if="items.length === 0" class="px-6 py-8 text-slate-500 text-sm text-center">
      Aucune campagne.
    </div>
    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-6 py-3 font-semibold">Campagne</th>
            <th class="px-6 py-3 font-semibold">Date</th>
            <th class="px-6 py-3 font-semibold">Poids max / box</th>
            <th class="px-6 py-3 font-semibold">Box validées</th>
            <th class="px-6 py-3 font-semibold">Articles distribués</th>
            <th class="px-6 py-3 font-semibold">Score moyen</th>
          </tr>
        </thead>
        <tbody class="text-slate-700 divide-y divide-slate-100">
          <tr
            v-for="row in items"
            :key="row.id"
            class="hover:bg-slate-50/80 transition-colors"
          >
            <td class="px-6 py-3 font-medium">#{{ row.id }}</td>
            <td class="px-6 py-3">{{ formatDate(row.created_at) }}</td>
            <td class="px-6 py-3">{{ row.max_weight_per_box }} g</td>
            <td class="px-6 py-3">{{ row.boxes_count }}</td>
            <td class="px-6 py-3">{{ row.articles_count }}</td>
            <td class="px-6 py-3">{{ row.average_score }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
