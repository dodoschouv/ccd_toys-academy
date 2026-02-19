<script setup>
import { ref } from 'vue'

const isLogin = ref(true)

const email = ref('')
const password = ref('')
const firstName = ref('')
const lastName = ref('')

const submitForm = () => {
  if (isLogin.value) {
    console.log('Connexion avec', email.value, password.value)
  } else {
    console.log('Inscription avec', firstName.value, lastName.value, email.value, password.value)
  }
}

const toggleForm = () => {
  isLogin.value = !isLogin.value
}
</script>

<template>
  <div class="max-w-md mx-auto mt-16 p-6 bg-white rounded-xl shadow-md border border-slate-200">
    <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">
      {{ isLogin ? 'Connexion' : 'Inscription' }}
    </h2>

    <form @submit.prevent="submitForm" class="space-y-4">
      <div v-if="!isLogin" class="grid grid-cols-2 gap-4">
        <input
          v-model="firstName"
          type="text"
          placeholder="Prénom"
          class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <input
          v-model="lastName"
          type="text"
          placeholder="Nom"
          class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <input
        v-model="email"
        type="email"
        placeholder="Email"
        class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        required
      />
      <input
        v-model="password"
        type="password"
        placeholder="Mot de passe"
        class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        required
      />

      <button
        type="submit"
        class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition-colors"
      >
        {{ isLogin ? 'Se connecter' : "S'inscrire" }}
      </button>
    </form>

    <div class="text-center mt-4 text-sm text-slate-600">
      <span>
        {{ isLogin ? "Pas de compte ?" : "Déjà inscrit ?" }}
      </span>
      <button
        @click="toggleForm"
        class="text-blue-600 font-medium ml-1 hover:underline"
      >
        {{ isLogin ? "Inscription" : "Connexion" }}
      </button>
    </div>
  </div>
</template>
