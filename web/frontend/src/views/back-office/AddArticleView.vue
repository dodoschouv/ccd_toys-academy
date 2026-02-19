<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useArticleStore } from '../../stores/articleStore.js'

const router = useRouter()
const articleStore = useArticleStore()
const { references } = storeToRefs(articleStore)

const formData = ref({
  designation: '',
  category: 'SOC',
  age_range: 'PE',
  state: 'N',
  price: 0,
  weight: 0,
  barcode: ''
})

const errorMessage = ref('')
const isSubmitting = ref(false)

onMounted(() => {
  articleStore.fetchReferences()
})

const submitForm = async () => {
  errorMessage.value = ''
  isSubmitting.value = true

  try {
    await articleStore.addArticle(formData.value)
    router.push('/catalogue')
  } catch (error) {
    errorMessage.value = error.response?.data?.error || 'Une erreur est survenue lors de l\'ajout.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Ajouter un article (Don)</h2>
        <button @click="router.push('/catalogue')" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
          ← Retour au catalogue
        </button>
      </div>

      <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="submitForm" class="space-y-5">

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">Désignation *</label>
          <input v-model="formData.designation" type="text" required
                 class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                 placeholder="Ex: Monopoly Junior">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie *</label>
            <select v-model="formData.category" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
              <option v-for="(label, code) in references.categories" :key="code" :value="code">
                {{ label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Tranche d'âge *</label>
            <select v-model="formData.age_range" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
              <option v-for="(label, code) in references.age_ranges" :key="code" :value="code">
                {{ label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">État *</label>
            <select v-model="formData.state" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
              <option v-for="(label, code) in references.states" :key="code" :value="code">
                {{ label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Code barre (Optionnel)</label>
            <input v-model="formData.barcode" type="text"
                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                   placeholder="Scan ou saisie">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Poids (grammes) *</label>
            <input v-model.number="formData.weight" type="number" min="0" required
                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Prix solidaire (€) *</label>
            <input v-model.number="formData.price" type="number" min="0" required
                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>

        <div class="pt-4 border-t border-slate-100 mt-6">
          <button type="submit" :disabled="isSubmitting"
                  class="w-full md:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold rounded-lg transition-colors flex justify-center items-center">
            <span v-if="isSubmitting" class="animate-spin h-5 w-5 mr-3 border-2 border-white border-t-transparent rounded-full"></span>
            {{ isSubmitting ? 'Enregistrement...' : 'Ajouter l\'article' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>