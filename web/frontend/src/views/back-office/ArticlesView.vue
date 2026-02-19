<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../api/index.js'

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const articlesPath = () => `${base}/articles`
const adminArticlesPath = () => `${base}/admin/articles`

const categoriesMap = {
  SOC: 'Jeux de société',
  FIG: 'Figurines / Poupées',
  CON: 'Construction',
  EXT: 'Extérieur',
  EVL: 'Éveil / Éducatif',
  LIV: 'Livres'
}
const statesMap = { N: 'Neuf', TB: 'Très bon état', B: 'Bon état' }

const articles = ref([])
const total = ref(0)
const loading = ref(false)
const error = ref(null)
const currentPage = ref(1)
const perPage = 20
const showCreate = ref(false)
const editingId = ref(null)
const form = ref({
  id: '',
  designation: '',
  category: 'SOC',
  age_range: '',
  state: 'B',
  price: '',
  weight: '',
  barcode: ''
})

const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage)))

async function fetchArticles() {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(articlesPath(), {
      params: { page: currentPage.value, per_page: perPage }
    })
    articles.value = res.data?.data ?? []
    total.value = res.data?.total ?? 0
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur chargement'
    articles.value = []
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editingId.value = null
  form.value = {
    id: '',
    designation: '',
    category: 'SOC',
    age_range: '',
    state: 'B',
    price: '',
    weight: '',
    barcode: ''
  }
  showCreate.value = true
}

function openEdit(a) {
  showCreate.value = false
  editingId.value = a.id
  form.value = {
    id: a.id,
    designation: a.designation,
    category: a.category,
    age_range: a.age_range,
    state: a.state,
    price: String(a.price),
    weight: String(a.weight),
    barcode: a.barcode ?? ''
  }
}

function cancelForm() {
  showCreate.value = false
  editingId.value = null
}

async function submitCreate() {
  try {
    await api.post(adminArticlesPath(), {
      id: form.value.id.trim(),
      designation: form.value.designation.trim(),
      category: form.value.category,
      age_range: form.value.age_range.trim(),
      state: form.value.state,
      price: parseInt(form.value.price, 10) || 0,
      weight: parseInt(form.value.weight, 10) || 0,
      barcode: form.value.barcode.trim() || null
    })
    cancelForm()
    await fetchArticles()
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur création'
  }
}

async function submitEdit() {
  if (!editingId.value) return
  try {
    await api.put(`${adminArticlesPath()}/${editingId.value}`, {
      designation: form.value.designation.trim(),
      category: form.value.category,
      age_range: form.value.age_range.trim(),
      state: form.value.state,
      price: parseInt(form.value.price, 10) || 0,
      weight: parseInt(form.value.weight, 10) || 0,
      barcode: form.value.barcode.trim() || null
    })
    cancelForm()
    await fetchArticles()
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur modification'
  }
}

async function remove(id) {
  if (!confirm('Supprimer cet article ?')) return
  try {
    await api.delete(`${adminArticlesPath()}/${id}`)
    await fetchArticles()
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur suppression'
  }
}

function goPage(p) {
  if (p < 1 || p > totalPages.value) return
  currentPage.value = p
  fetchArticles()
}

onMounted(() => fetchArticles())
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-6">
    <h3 class="text-lg font-medium text-slate-800 mb-2">Articles</h3>
    <p class="text-sm text-slate-500 mb-6">Gestion du catalogue : ajout et modification des articles.</p>

    <div v-if="error" class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
      {{ error }}
    </div>

    <div class="mb-4">
      <button
        type="button"
        @click="openCreate"
        class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-700"
      >
        Nouvel article
      </button>
    </div>

    <!-- Formulaire création -->
    <div v-if="showCreate" class="mb-6 p-4 rounded-lg border border-slate-200 bg-slate-50/50">
      <h4 class="font-medium text-slate-800 mb-3">Nouvel article</h4>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div>
          <label class="block text-slate-600 mb-1">ID</label>
          <input v-model="form.id" class="w-full rounded border border-slate-300 px-2 py-1.5" placeholder="ex: ART001" />
        </div>
        <div>
          <label class="block text-slate-600 mb-1">Désignation</label>
          <input v-model="form.designation" class="w-full rounded border border-slate-300 px-2 py-1.5" />
        </div>
        <div>
          <label class="block text-slate-600 mb-1">Catégorie</label>
          <select v-model="form.category" class="w-full rounded border border-slate-300 px-2 py-1.5">
            <option v-for="(label, code) in categoriesMap" :key="code" :value="code">{{ label }}</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-600 mb-1">Tranche d'âge</label>
          <input v-model="form.age_range" class="w-full rounded border border-slate-300 px-2 py-1.5" placeholder="ex: 3-5" />
        </div>
        <div>
          <label class="block text-slate-600 mb-1">État</label>
          <select v-model="form.state" class="w-full rounded border border-slate-300 px-2 py-1.5">
            <option v-for="(label, code) in statesMap" :key="code" :value="code">{{ label }}</option>
          </select>
        </div>
        <div>
          <label class="block text-slate-600 mb-1">Prix (€)</label>
          <input v-model="form.price" type="number" min="0" class="w-full rounded border border-slate-300 px-2 py-1.5" />
        </div>
        <div>
          <label class="block text-slate-600 mb-1">Poids (g)</label>
          <input v-model="form.weight" type="number" min="0" class="w-full rounded border border-slate-300 px-2 py-1.5" />
        </div>
        <div>
          <label class="block text-slate-600 mb-1">Code-barres</label>
          <input v-model="form.barcode" class="w-full rounded border border-slate-300 px-2 py-1.5" />
        </div>
      </div>
      <div class="mt-3 flex gap-2">
        <button type="button" @click="submitCreate" class="px-3 py-1.5 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Créer</button>
        <button type="button" @click="cancelForm" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 text-sm hover:bg-slate-50">Annuler</button>
      </div>
    </div>

    <!-- Formulaire édition (inline pour la ligne en cours) -->
    <template v-if="editingId">
      <div class="mb-6 p-4 rounded-lg border border-blue-200 bg-blue-50/50">
        <h4 class="font-medium text-slate-800 mb-3">Modifier l'article</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
          <div>
            <label class="block text-slate-600 mb-1">Désignation</label>
            <input v-model="form.designation" class="w-full rounded border border-slate-300 px-2 py-1.5" />
          </div>
          <div>
            <label class="block text-slate-600 mb-1">Catégorie</label>
            <select v-model="form.category" class="w-full rounded border border-slate-300 px-2 py-1.5">
              <option v-for="(label, code) in categoriesMap" :key="code" :value="code">{{ label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-slate-600 mb-1">Tranche d'âge</label>
            <input v-model="form.age_range" class="w-full rounded border border-slate-300 px-2 py-1.5" />
          </div>
          <div>
            <label class="block text-slate-600 mb-1">État</label>
            <select v-model="form.state" class="w-full rounded border border-slate-300 px-2 py-1.5">
              <option v-for="(label, code) in statesMap" :key="code" :value="code">{{ label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-slate-600 mb-1">Prix (€)</label>
            <input v-model="form.price" type="number" min="0" class="w-full rounded border border-slate-300 px-2 py-1.5" />
          </div>
          <div>
            <label class="block text-slate-600 mb-1">Poids (g)</label>
            <input v-model="form.weight" type="number" min="0" class="w-full rounded border border-slate-300 px-2 py-1.5" />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-slate-600 mb-1">Code-barres</label>
            <input v-model="form.barcode" class="w-full rounded border border-slate-300 px-2 py-1.5" />
          </div>
        </div>
        <div class="mt-3 flex gap-2">
          <button type="button" @click="submitEdit" class="px-3 py-1.5 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Enregistrer</button>
          <button type="button" @click="cancelForm" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 text-sm hover:bg-slate-50">Annuler</button>
        </div>
      </div>
    </template>

    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-slate-600"></div>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-slate-600 border-b border-slate-200">
          <tr>
            <th class="py-2 pr-2 font-medium">ID</th>
            <th class="py-2 pr-2 font-medium">Désignation</th>
            <th class="py-2 pr-2 font-medium">Catégorie</th>
            <th class="py-2 pr-2 font-medium">Âge</th>
            <th class="py-2 pr-2 font-medium">État</th>
            <th class="py-2 pr-2 font-medium">Prix</th>
            <th class="py-2 pr-2 font-medium">Poids</th>
            <th class="py-2 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody class="text-slate-700">
          <tr v-for="a in articles" :key="a.id" class="border-b border-slate-100 hover:bg-slate-50/50">
            <td class="py-2 pr-2">{{ a.id }}</td>
            <td class="py-2 pr-2 max-w-[180px] truncate" :title="a.designation">{{ a.designation }}</td>
            <td class="py-2 pr-2">{{ categoriesMap[a.category] || a.category }}</td>
            <td class="py-2 pr-2">{{ a.age_range }}</td>
            <td class="py-2 pr-2">{{ statesMap[a.state] || a.state }}</td>
            <td class="py-2 pr-2">{{ a.price }} €</td>
            <td class="py-2 pr-2">{{ a.weight }} g</td>
            <td class="py-2 flex gap-1">
              <button type="button" @click="openEdit(a)" class="text-blue-600 hover:underline">Modifier</button>
              <button type="button" @click="remove(a.id)" class="text-red-600 hover:underline">Supprimer</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="totalPages > 1" class="mt-4 flex items-center justify-center gap-2 text-sm">
        <button
          type="button"
          :disabled="currentPage <= 1"
          @click="goPage(currentPage - 1)"
          class="px-3 py-1 rounded border border-slate-300 disabled:opacity-50"
        >
          Précédent
        </button>
        <span class="text-slate-600">Page {{ currentPage }} / {{ totalPages }}</span>
        <button
          type="button"
          :disabled="currentPage >= totalPages"
          @click="goPage(currentPage + 1)"
          class="px-3 py-1 rounded border border-slate-300 disabled:opacity-50"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>
