<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore.js'

const router = useRouter()
const authStore = useAuthStore()

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.replace('/connexion')
    return
  }
})
</script>

<template>
  <div class="max-w-2xl mx-auto px-4 py-8">
    <h2 class="text-xl font-semibold text-slate-800 mb-6">Paramètres</h2>

    <div class="rounded-lg border border-slate-200 bg-white p-6 space-y-6">
      <section>
        <h3 class="text-lg font-medium text-slate-800 mb-2">Compte</h3>
        <p class="text-sm text-slate-600 mb-4">
          Gérez les informations de votre compte et vos préférences.
        </p>
        <router-link
          to="/profil"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-slate-800 text-white text-sm font-medium hover:bg-slate-700"
        >
          <span class="material-symbols-outlined text-[20px]">person</span>
          Modifier mon profil
        </router-link>
      </section>

      <section class="pt-4 border-t border-slate-200">
        <h3 class="text-lg font-medium text-slate-800 mb-2">Sécurité</h3>
        <p class="text-sm text-slate-600">
          Nous n'implémenterons pas cette fonctionalité.
        </p>
      </section>
    </div>
  </div>
</template>
