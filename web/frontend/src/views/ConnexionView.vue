<script setup>
import { ref } from 'vue'
import api from '../api/index.js'

const activeSection = ref('connexion')

const email = ref('')
const password = ref('')

const lastName = ref('')
const firstName = ref('')
const subscriberEmail = ref('')
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

const connexionError = ref('')
const inscriptionMessage = ref('')
const inscriptionError = ref('')
const inscriptionLoading = ref(false)

async function submitConnexion() {
  connexionError.value = ''
  if (!email.value.trim()) {
    connexionError.value = 'Saisissez votre email.'
    return
  }
  console.log('Connexion avec', email.value, password.value)
}

async function submitInscription() {
  inscriptionError.value = ''
  inscriptionMessage.value = ''
  if (!lastName.value.trim() || !firstName.value.trim() || !subscriberEmail.value.trim()) {
    inscriptionError.value = 'Nom, prénom et email sont requis.'
    return
  }
  if (!childAgeRange.value) {
    inscriptionError.value = 'Choisissez la tranche d\'âge de l\'enfant.'
    return
  }
  inscriptionLoading.value = true
  try {
    await api.post('/api/subscribers', {
      last_name: lastName.value.trim(),
      first_name: firstName.value.trim(),
      email: subscriberEmail.value.trim(),
      child_age_range: childAgeRange.value,
      preferences: preferences.value,
    })
    inscriptionMessage.value = 'Inscription enregistrée.'
  } catch (e) {
    inscriptionError.value = e.response?.data?.error || 'Erreur lors de l\'inscription.'
  } finally {
    inscriptionLoading.value = false
  }
}

function movePreference(index, direction) {
  const newPrefs = [...preferences.value]
  const target = index + direction
  if (target < 0 || target >= newPrefs.length) return
  ;[newPrefs[index], newPrefs[target]] = [newPrefs[target], newPrefs[index]]
  preferences.value = newPrefs
}
</script>

<template>
  <div class="max-w-2xl mx-auto px-4 py-8">
    <h2 class="text-xl font-semibold text-slate-800 mb-6">Connexion / Inscription</h2>

    <div class="flex gap-2 mb-8 border-b border-slate-200">
      <button
        type="button"
        :class="[
          'px-4 py-2 text-sm font-medium rounded-t-md transition-colors',
          activeSection === 'connexion' ? 'bg-white border border-slate-200 border-b-0 -mb-px text-slate-900' : 'text-slate-600 hover:text-slate-900'
        ]"
        @click="activeSection = 'connexion'"
      >
        Connexion
      </button>
      <button
        type="button"
        :class="[
          'px-4 py-2 text-sm font-medium rounded-t-md transition-colors',
          activeSection === 'inscription' ? 'bg-white border border-slate-200 border-b-0 -mb-px text-slate-900' : 'text-slate-600 hover:text-slate-900'
        ]"
        @click="activeSection = 'inscription'"
      >
        Inscription
      </button>
    </div>

    <section v-show="activeSection === 'connexion'" class="rounded-lg border border-slate-200 bg-white p-6">
      <h3 class="text-lg font-medium text-slate-800 mb-4">Se connecter</h3>
      <form @submit.prevent="submitConnexion" class="space-y-4">
        <div>
          <label for="email-login" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input
            id="email-login"
            v-model="email"
            type="email"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            placeholder="votre@email.fr"
          />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
          <input
            id="password"
            v-model="password"
            type="password"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            placeholder="••••••••"
          />
        </div>
        <p v-if="connexionError" class="text-sm text-red-600">{{ connexionError }}</p>
        <button
          type="submit"
          class="w-full rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
        >
          Se connecter
        </button>
      </form>
    </section>

    <section v-show="activeSection === 'inscription'" class="rounded-lg border border-slate-200 bg-white p-6">
      <h3 class="text-lg font-medium text-slate-800 mb-4">S'inscrire ou modifier mes préférences</h3>
      <form @submit.prevent="submitInscription" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="last-name" class="block text-sm font-medium text-slate-700 mb-1">Nom</label>
            <input
              id="last-name"
              v-model="lastName"
              type="text"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            />
          </div>
          <div>
            <label for="first-name" class="block text-sm font-medium text-slate-700 mb-1">Prénom</label>
            <input
              id="first-name"
              v-model="firstName"
              type="text"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            />
          </div>
        </div>
        <div>
          <label for="subscriber-email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
          <input
            id="subscriber-email"
            v-model="subscriberEmail"
            type="email"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            placeholder="votre@email.fr"
          />
        </div>
        <div>
          <label for="age-range" class="block text-sm font-medium text-slate-700 mb-1">Tranche d'âge de l'enfant</label>
          <select
            id="age-range"
            v-model="childAgeRange"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
          >
            <option value="">Choisir</option>
            <option v-for="a in ageRanges" :key="a.code" :value="a.code">{{ a.label }}</option>
          </select>
        </div>
        <div>
          <span class="block text-sm font-medium text-slate-700 mb-2">Préférences de catégories (ordre du plus au moins souhaité)</span>
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
        <p v-if="inscriptionError" class="text-sm text-red-600">{{ inscriptionError }}</p>
        <p v-if="inscriptionMessage" class="text-sm text-green-600">{{ inscriptionMessage }}</p>
        <button
          type="submit"
          :disabled="inscriptionLoading"
          class="w-full rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
        >
          {{ inscriptionLoading ? 'Enregistrement…' : 'Enregistrer' }}
        </button>
      </form>
    </section>
  </div>
</template>
