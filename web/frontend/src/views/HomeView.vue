<script setup>
import logoImg from '../img/toysacademy.png'
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <section class="flex flex-col items-center justify-center mb-12 pt-6">
      <img
        :src="logoImg"
        alt="Toys Academy"
        class="h-40 w-auto object-contain logo-flying mb-6"
      />
      <h2 class="text-2xl font-semibold text-slate-800 tracking-tight mb-1">
        Bienvenue
      </h2>
      <p class="text-slate-600 text-center max-w-xl mx-auto mb-8">
        Collecte, tri, nettoyage et revalorisation de dons de jeux, jouets et livres pour enfants. Revente à prix solidaires en boutique/e-shop. Ateliers parentalité et de sensibilisation à l'environnement avec de l'upcycling de jouets pour s'approprier l'anti-gaspi de façon ludique.
      </p>
    </section>

    <!-- Carte & Adresse -->
    <section class="mx-4 sm:mx-auto sm:max-w-4xl">
      <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200/80 text-slate-800">
            <span class="material-symbols-outlined text-[24px]">location_on</span>
          </span>
          <div>
            <h2 class="text-lg font-semibold text-slate-800">Où nous trouver ?</h2>
            <p class="text-sm text-slate-600">
              74 Grande Rue · 54700 Jezainville
            </p>
          </div>
        </div>
        <div class="w-full aspect-video min-h-[240px] bg-slate-100">
          <iframe
            src="https://www.openstreetmap.org/export/embed.html?bbox=6.038%2C48.874%2C6.048%2C48.881&layer=mapnik&marker=48.877152%2C6.042376"
            title="Carte : 74 Grande Rue, 54700 Jezainville"
            class="w-full h-full border-0"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          />
        </div>
        <div class="px-6 py-3 border-t border-slate-100">
          <a
            href="https://www.openstreetmap.org/?mlat=48.877152&amp;mlon=6.042376#map=17/48.877152/6.042376"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 text-sm text-amber-700 hover:text-amber-800 font-medium"
          >
            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
            Ouvrir la carte en grand
          </a>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
@keyframes flying {
  0%, 100% {
    transform: translateY(0) translateX(0) rotate(-1deg);
  }
  25% {
    transform: translateY(-6px) translateX(3px) rotate(1deg);
  }
  50% {
    transform: translateY(-3px) translateX(-2px) rotate(0deg);
  }
  75% {
    transform: translateY(-8px) translateX(2px) rotate(0.5deg);
  }
}
.logo-flying {
  animation: flying 4s ease-in-out infinite;
}
</style>
