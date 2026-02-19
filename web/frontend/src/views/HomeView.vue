<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <section class="mb-12">
      <h2 class="text-2xl font-semibold text-slate-800 tracking-tight mb-1">
        Bienvenue
      </h2>
      <p class="text-slate-600">
        Toys Academy — catalogue de jouets reconditionnés et box personnalisées.
      </p>
    </section>

    <nav class="grid grid-cols-1 sm:grid-cols-2 gap-4" aria-label="Navigation principale">
      <RouterLink
        to="/catalogue"
        class="flex items-start gap-4 p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50/50 transition-colors text-left"
      >
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-600 material-symbols-outlined">
          inventory_2
        </span>
        <div>
          <span class="font-medium text-slate-800 block">Catalogue</span>
          <span class="text-sm text-slate-500">Parcourir les articles disponibles</span>
        </div>
      </RouterLink>

      <RouterLink
        to="/ma-box"
        class="flex items-start gap-4 p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50/50 transition-colors text-left"
      >
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-600 material-symbols-outlined">
          card_giftcard
        </span>
        <div>
          <span class="font-medium text-slate-800 block">Ma box</span>
          <span class="text-sm text-slate-500">Consulter ma box validée</span>
        </div>
      </RouterLink>

      <RouterLink
        to="/connexion"
        class="flex items-start gap-4 p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50/50 transition-colors text-left"
      >
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-600 material-symbols-outlined">
          login
        </span>
        <div>
          <span class="font-medium text-slate-800 block">Connexion / Inscription</span>
          <span class="text-sm text-slate-500">Se connecter ou s'inscrire et gérer mes préférences</span>
        </div>
      </RouterLink>

      <RouterLink
        to="/back-office"
        class="flex items-start gap-4 p-5 bg-white rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50/50 transition-colors text-left sm:col-span-2"
      >
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-600 material-symbols-outlined">
          settings
        </span>
        <div>
          <span class="font-medium text-slate-800 block">Back-office</span>
          <span class="text-sm text-slate-500">Gestion des campagnes, box, abonnés et articles</span>
        </div>
      </RouterLink>
    </nav>
  </div>
</template>
