<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import logoImg from '../img/toysacademy.png'

const shareOpen = ref(false)
const shareMenuRef = ref(null)
const pageUrl = ref('')
const copyDone = ref(false)

onMounted(() => {
  pageUrl.value = typeof window !== 'undefined' ? window.location.href : ''
  document.addEventListener('click', onDocumentClick)
})
onUnmounted(() => {
  document.removeEventListener('click', onDocumentClick)
})

function onDocumentClick(e) {
  if (shareMenuRef.value && !shareMenuRef.value.contains(e.target)) {
    shareOpen.value = false
  }
}

function shareFacebook() {
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(pageUrl.value)}`, '_blank', 'width=600,height=400')
  shareOpen.value = false
}
function shareTwitter() {
  window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(pageUrl.value)}&text=Toys%20Academy%20-%20jouets%20reconditionnés%20et%20box%20personnalisées`, '_blank', 'width=600,height=400')
  shareOpen.value = false
}
function shareLinkedIn() {
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(pageUrl.value)}`, '_blank', 'width=600,height=400')
  shareOpen.value = false
}
async function copyUrl() {
  try {
    await navigator.clipboard.writeText(pageUrl.value)
    copyDone.value = true
    setTimeout(() => { copyDone.value = false }, 2000)
  } catch (_) {}
  shareOpen.value = false
}
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 py-10">
    <section class="flex flex-col sm:flex-row sm:items-center gap-10 sm:gap-16 mb-12 pt-6 min-h-[85vh]">
      <div class="flex justify-center sm:justify-start shrink-0 hero-pop" style="animation-delay: 0s">
        <img
          :src="logoImg"
          alt="Toys Academy"
          class="h-52 sm:h-72 w-auto object-contain logo-flying"
        />
      </div>
      <div class="flex flex-col items-center sm:items-start text-center sm:text-left flex-1 min-w-0">
        <h2 class="text-3xl sm:text-5xl font-bold text-slate-800 tracking-tight mb-6 hero-pop" style="animation-delay: 0.1s">
          Bienvenue
        </h2>
        <p class="text-slate-600 w-full max-w-none mb-10 text-base sm:text-lg leading-loose sm:leading-[1.8] hero-pop" style="animation-delay: 0.2s">
          Collecte, tri, nettoyage et revalorisation de dons de jeux, jouets et livres pour enfants. Revente à prix solidaires en boutique/e-shop. Ateliers parentalité et de sensibilisation à l'environnement avec de l'upcycling de jouets pour s'approprier l'anti-gaspi de façon ludique.
        </p>
        <a
          href="#ou-nous-trouver"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-blue-900 text-white text-base font-medium hover:bg-blue-800 transition-colors shrink-0 hero-pop"
          style="animation-delay: 0.35s"
        >
          <span class="material-symbols-outlined text-[20px]">location_on</span>
          Où nous trouver
        </a>
      </div>
    </section>

    <!-- Carte & Adresse -->
    <section id="ou-nous-trouver" class="mx-4 sm:mx-auto sm:max-w-4xl scroll-mt-6 hero-pop" style="animation-delay: 0.5s">
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

    <!-- Partager la page -->
    <section class="mt-10 mx-4 sm:mx-0">
      <div class="rounded-2xl bg-blue-900 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <p class="text-white font-semibold text-lg">Vous aimez Toys Academy ?</p>
          <p class="text-blue-100 text-sm mt-0.5">Aidez-nous à le faire connaître !</p>
        </div>
        <div class="relative shrink-0" ref="shareMenuRef">
          <button
            type="button"
            @click="shareOpen = !shareOpen"
            :class="[
              'inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm font-medium hover:bg-slate-50 transition-colors',
              shareOpen && 'ring-2 ring-blue-300'
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">share</span>
            Partager la page
          </button>
          <div
            v-show="shareOpen"
            class="absolute right-0 top-full mt-1 py-1 w-56 rounded-lg border border-slate-200 bg-white shadow-lg z-30"
          >
            <button type="button" @click="shareFacebook" class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50">
              <span class="text-[#1877F2] font-bold text-lg">f</span>
              Facebook
            </button>
            <button type="button" @click="shareTwitter" class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50">
              <span class="text-slate-800 font-bold text-sm">𝕏</span>
              Twitter
            </button>
            <button type="button" @click="shareLinkedIn" class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50">
              <span class="text-[#0A66C2] font-bold text-sm">in</span>
              LinkedIn
            </button>
            <button type="button" @click="copyUrl" class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50">
              <span class="material-symbols-outlined text-[20px] text-slate-500">link</span>
              {{ copyDone ? 'URL copiée !' : 'Copier l\'URL' }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Information -->
    <section class="mt-8 mx-4 sm:mx-0 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-4">
      <p class="text-slate-600 text-sm sm:text-base">
        Si vous voulez plus d'information sur le site et pourquoi ce projet a été réalisé
      </p>
      <RouterLink
        to="/information"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 hover:border-slate-400 transition-colors shrink-0"
      >
        <span class="material-symbols-outlined text-[20px]">info</span>
        Information
      </RouterLink>
    </section>
  </div>
</template>

<style scoped>
@keyframes heroPop {
  from {
    opacity: 0;
    transform: translateY(16px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
.hero-pop {
  animation: heroPop 0.5s ease-out forwards;
  opacity: 0;
}

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
