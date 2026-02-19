<script setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useArticleStore } from '../stores/articleStore'

const articleStore = useArticleStore()
const { articles, total, loading, currentPage, itemsPerPage, filters } = storeToRefs(articleStore)

const categoriesMap = {
  SOC: 'Jeux de société',
  FIG: 'Figurines / Poupées',
  CON: 'Construction',
  EXT: 'Extérieur',
  EVL: 'Éveil / Éducatif',
  LIV: 'Livres'
}
const statesMap = { N: 'Neuf', TB: 'Très bon état', B: 'Bon état' }

const formatCategory = (code) => categoriesMap[code] || code

const totalPages = computed(() => {
  if (itemsPerPage.value === 0) return 1
  return Math.ceil(total.value / itemsPerPage.value)
})

const nextPage = () => {
  if (currentPage.value < totalPages.value) articleStore.changePage(currentPage.value + 1)
}
const prevPage = () => {
  if (currentPage.value > 1) articleStore.changePage(currentPage.value - 1)
}

const perPageOptions = [10, 20, 30]
const localCategory = ref(null)
const localState = ref(null)
const localAgeRange = ref('')

const applyFilters = () => {
  articleStore.setFilters({
    category: localCategory.value || null,
    state: localState.value || null,
    age_range: localAgeRange.value.trim() || null
  })
}
const resetFilters = () => {
  localCategory.value = null
  localState.value = null
  localAgeRange.value = ''
  articleStore.resetFilters()
}
const hasActiveFilters = computed(() =>
  !!localCategory.value || !!localState.value || !!localAgeRange.value.trim()
)

onMounted(() => {
  articleStore.fetchArticles(1, 10)
})
</script>

<template>
  <div class="max-w-7xl mx-auto p-4">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Catalogue des articles</h2>
      <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
        {{ total }} articles au total
      </span>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-wrap items-end gap-3">
      <div class="flex flex-wrap gap-3 items-end">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Catégorie</label>
          <select
            v-model="localCategory"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm min-w-[180px] bg-white"
          >
            <option :value="null">Toutes</option>
            <option v-for="(label, code) in categoriesMap" :key="code" :value="code">{{ label }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">État</label>
          <select
            v-model="localState"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm min-w-[160px] bg-white"
          >
            <option :value="null">Tous</option>
            <option v-for="(label, code) in statesMap" :key="code" :value="code">{{ label }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Tranche d'âge</label>
          <input
            v-model="localAgeRange"
            type="text"
            placeholder="ex: 3-5"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-28 bg-white"
          />
        </div>
        <button
          type="button"
          @click="applyFilters"
          class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 transition-colors"
        >
          Appliquer
        </button>
        <button
          v-if="hasActiveFilters"
          type="button"
          @click="resetFilters"
          class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors"
        >
          Réinitialiser
        </button>
      </div>
      <div class="ml-auto flex items-center gap-2">
        <label class="text-sm text-slate-600">Par page</label>
        <select
          :value="itemsPerPage"
          @change="articleStore.setItemsPerPage(Number($event.target.value))"
          class="rounded-lg border border-slate-300 px-2 py-1.5 text-sm bg-white"
        >
          <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
    </div>

    <div v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div
            v-for="article in articles"
            :key="article.id"
            class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col hover:shadow-md transition-shadow"
        >
          <h3 class="font-bold text-lg text-slate-800 mb-3 truncate" :title="article.designation">
            {{ article.designation }}
          </h3>

          <div class="flex flex-wrap gap-2 mb-4">
            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-bold rounded">
              {{ formatCategory(article.category) }}
            </span>
            <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded">
              Âge: {{ article.age_range }}
            </span>
            <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded">
              État: {{ article.state }}
            </span>
          </div>

          <div class="mt-auto pt-4 border-t border-slate-100 flex justify-between items-center">
            <span class="text-sm text-slate-500 font-medium">{{ article.weight }} g</span>
            <span class="text-xl font-black text-slate-800">{{ article.price }} €</span>
          </div>
        </div>
      </div>

      <div class="flex justify-center items-center space-x-4 mt-8" v-if="totalPages > 1">
        <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer"
            :class="currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50'"
        >
          Précédent
        </button>

        <span class="text-sm font-medium text-slate-600">
          Page {{ currentPage }} sur {{ totalPages }}
        </span>

        <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer"
            :class="currentPage === totalPages ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50'"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>