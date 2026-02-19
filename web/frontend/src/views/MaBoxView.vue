<script setup>
import { ref } from 'vue'
import api from '../api/index.js'

const email = ref('')
const loading = ref(false)
const error = ref('')
const boxes = ref([])

async function fetchBox() {
  const value = email.value.trim()
  if (!value) {
    error.value = 'Saisissez votre adresse email.'
    return
  }
  error.value = ''
  loading.value = true
  boxes.value = []
  try {
    const { data } = await api.get('/api/subscribers/box', { params: { email: value } })
    boxes.value = Array.isArray(data) ? data : []
  } catch (e) {
    if (e.response?.status === 404) {
      error.value = 'Aucun abonné avec cet email.'
    } else {
      error.value = e.response?.data?.error || 'Erreur lors de la récupération.'
    }
    boxes.value = []
  } finally {
    loading.value = false
  }
}

const categoriesMap = {
  SOC: 'Jeux de société',
  FIG: 'Figurines et poupées',
  CON: 'Construction',
  EXT: 'Extérieur',
  EVL: 'Éveil et éducatif',
  LIV: 'Livres'
}
const ageMap = { BB: '0-3 ans', PE: '3-6 ans', EN: '6-10 ans', AD: '10+ ans' }
const stateMap = { N: 'Neuf', TB: 'Très bon état', B: 'Bon état' }
</script>

<template>
  <div class="max-w-3xl mx-auto px-4 py-8">
    <h2 class="text-xl font-semibold text-slate-800 mb-6">Ma box</h2>

    <div class="mb-8">
      <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Adresse email</label>
      <div class="flex gap-2">
        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="votre@email.fr"
          class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          @keydown.enter="fetchBox"
        />
        <button
          type="button"
          :disabled="loading"
          class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
          @click="fetchBox"
        >
          Voir ma box
        </button>
      </div>
      <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <span class="text-sm text-slate-500">Chargement…</span>
    </div>

    <template v-else-if="boxes.length > 0">
      <p class="text-sm text-slate-600 mb-4">{{ boxes.length }} box validée(s) trouvée(s).</p>
      <div class="space-y-6">
        <section
          v-for="box in boxes"
          :key="box.id"
          class="rounded-lg border border-slate-200 bg-white p-5"
        >
          <div class="flex flex-wrap gap-4 text-sm text-slate-600 border-b border-slate-100 pb-4 mb-4">
            <span>Score : <strong class="text-slate-800">{{ box.score }}</strong></span>
            <span>Poids : <strong class="text-slate-800">{{ box.total_weight }} g</strong></span>
            <span>Prix : <strong class="text-slate-800">{{ box.total_price }} €</strong></span>
          </div>
          <h3 class="text-sm font-medium text-slate-700 mb-3">Articles</h3>
          <ul class="space-y-2">
            <li
              v-for="art in box.articles"
              :key="art.id"
              class="flex justify-between items-start gap-4 py-2 border-b border-slate-50 last:border-0 text-sm"
            >
              <span class="font-medium text-slate-800">{{ art.designation }}</span>
              <span class="text-slate-500 shrink-0">{{ art.price }} € · {{ art.weight }} g · {{ categoriesMap[art.category] || art.category }} · {{ stateMap[art.state] || art.state }}</span>
            </li>
          </ul>
        </section>
      </div>
    </template>

    <p v-else-if="!loading && email && boxes.length === 0 && !error" class="text-sm text-slate-500">
      Aucune box validée pour cet email.
    </p>
  </div>
</template>
