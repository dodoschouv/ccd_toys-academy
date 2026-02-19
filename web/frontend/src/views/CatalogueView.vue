<script setup>
import { ref, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useArticleStore } from '../stores/articleStore'

const articleStore = useArticleStore()
const { articles, loading } = storeToRefs(articleStore)

const currentPage = ref(1)
const itemsPerPage = 10

const categoriesMap = {
  'SOC': 'Jeux de société',
  'FIG': 'Figurines / Poupées',
  'CON': 'Construction',
  'EXT': 'Extérieur',
  'EVL': 'Éveil / Éducatif',
  'LIV': 'Livres'
}

const formatCategory = (code) => categoriesMap[code] || code

const totalPages = computed(() => Math.ceil(articles.value.length / itemsPerPage))

const paginatedArticles = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return articles.value.slice(start, end)
})

onMounted(() => {
  articleStore.fetchArticles()
})
</script>

<template>
  <div class="max-w-7xl mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Catalogue des articles</h2>
      <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
        {{ articles.length }} articles disponibles
      </span>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
    </div>

    <div v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div
            v-for="article in paginatedArticles"
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
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-4 py-2 rounded-lg font-medium transition-colors"
            :class="currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50'"
        >
          Précédent
        </button>

        <span class="text-sm font-medium text-slate-600">
          Page {{ currentPage }} sur {{ totalPages }}
        </span>

        <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-4 py-2 rounded-lg font-medium transition-colors"
            :class="currentPage === totalPages ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50'"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>