<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/index.js'
import { useAuthStore } from '../stores/authStore.js'

const router = useRouter()
const authStore = useAuthStore()

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const mePath = () => `${base}/auth/me`
const subscribersPath = () => `${base}/subscribers`

const loading = ref(true)
const saving = ref(false)
const error = ref('')
const message = ref('')

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const childAgeRange = ref('')
const preferences = ref(['SOC', 'FIG', 'CON', 'EXT', 'EVL', 'LIV'])

const categories = [
  { code: 'SOC', label: 'Jeux de société' },
  { code: 'FIG', label: 'Figurines et poupées' },
  { code: 'CON', label: 'Construction' },
  { code: 'EXT', label: 'Extérieur' },
  { code: 'EVL', label: 'Éveil et éducatif' },
  { code: 'LIV', label: 'Livres' },
]
const ageRanges = [
  { code: 'BB', label: '0-3 ans' },
  { code: 'PE', label: '3-6 ans' },
  { code: 'EN', label: '6-10 ans' },
  { code: 'AD', label: '10+ ans' },
]

function movePreference(index, direction) {
  const newPrefs = [...preferences.value]
  const target = index + direction
  if (target < 0 || target >= newPrefs.length) return
  ;[newPrefs[index], newPrefs[target]] = [newPrefs[target], newPrefs[index]]
  preferences.value = newPrefs
}

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.replace('/connexion')
    return
  }
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get(mePath())
    if (data.subscriber) {
      const s = data.subscriber
      firstName.value = s.first_name ?? ''
      lastName.value = s.last_name ?? ''
      email.value = s.email ?? authStore.user?.email ?? ''
      childAgeRange.value = s.child_age_range ?? ''
      preferences.value = Array.isArray(s.preferences) && s.preferences.length
        ? s.preferences
        : ['SOC', 'FIG', 'CON', 'EXT', 'EVL', 'LIV']
    } else {
      firstName.value = authStore.user?.first_name ?? ''
      lastName.value = authStore.user?.last_name ?? ''
      email.value = authStore.user?.email ?? ''
    }
  } catch (e) {
    error.value = e.response?.data?.error || 'Impossible de charger le profil.'
  } finally {
    loading.value = false
  }
})

async function save() {
  if (!email.value.trim() || !lastName.value.trim() || !firstName.value.trim()) {
    error.value = 'Nom, prénom et email sont requis.'
    return
  }
  if (!childAgeRange.value || !['BB', 'PE', 'EN', 'AD'].includes(childAgeRange.value)) {
    error.value = 'Choisissez une tranche d\'âge valide.'
    return
  }
  saving.value = true
  error.value = ''
  message.value = ''
  try {
    await api.post(subscribersPath(), {
      email: email.value.trim(),
      first_name: firstName.value.trim(),
      last_name: lastName.value.trim(),
      child_age_range: childAgeRange.value,
      preferences: preferences.value,
    })
    message.value = 'Profil enregistré.'
  } catch (e) {
    error.value = e.response?.data?.error || 'Erreur lors de l\'enregistrement.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto px-4 py-8">
    <h2 class="text-xl font-semibold text-slate-800 mb-6">Mon profil</h2>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-2 border-slate-300 border-t-slate-600"></div>
    </div>

    <div v-else class="rounded-lg border border-slate-200 bg-white p-6">
      <form @submit.prevent="save" class="space-y-4">
        <div v-if="error" class="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ error }}</div>
        <div v-if="message" class="p-3 rounded-lg bg-green-50 text-green-700 text-sm">{{ message }}</div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nom</label>
            <input
              v-model="lastName"
              type="text"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Prénom</label>
            <input
              v-model="firstName"
              type="text"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            readonly
            class="w-full rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-slate-600"
          />
          <p class="text-xs text-slate-500 mt-1">L'email ne peut pas être modifié ici.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Tranche d'âge de l'enfant</label>
          <select
            v-model="childAgeRange"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900"
          >
            <option value="">Choisir</option>
            <option v-for="a in ageRanges" :key="a.code" :value="a.code">{{ a.label }}</option>
          </select>
        </div>
        <div>
          <span class="block text-sm font-medium text-slate-700 mb-2">Préférences (ordre du plus au moins souhaité)</span>
          <ul class="space-y-2">
            <li
              v-for="(code, index) in preferences"
              :key="code"
              class="flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm"
            >
              <span class="text-slate-500 w-6">{{ index + 1 }}</span>
              <span class="flex-1 font-medium text-slate-800">{{ categories.find(c => c.code === code)?.label || code }}</span>
              <button
                type="button"
                class="p-1 rounded text-slate-500 hover:bg-slate-200 disabled:opacity-40"
                :disabled="index === 0"
                @click="movePreference(index, -1)"
              >
                <span class="material-symbols-outlined text-lg">arrow_upward</span>
              </button>
              <button
                type="button"
                class="p-1 rounded text-slate-500 hover:bg-slate-200 disabled:opacity-40"
                :disabled="index === preferences.length - 1"
                @click="movePreference(index, 1)"
              >
                <span class="material-symbols-outlined text-lg">arrow_downward</span>
              </button>
            </li>
          </ul>
        </div>
        <button
          type="submit"
          :disabled="saving"
          class="w-full rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
        >
          {{ saving ? 'Enregistrement…' : 'Enregistrer les modifications' }}
        </button>
      </form>
    </div>
  </div>
</template>
