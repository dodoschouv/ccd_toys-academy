<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/index.js'

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const campaignsPath = () => `${base}/admin/campaigns`
const boxesPath = (id) => `${base}/admin/campaigns/${id}/boxes`
const composePath = (id) => `${base}/admin/campaigns/${id}/compose`
const validatePath = (id) => `${base}/admin/boxes/${id}/validate`

const campaigns = ref([])
const loading = ref(false)
const error = ref(null)
const expandedId = ref(null)
const boxesByCampaign = ref({})
const loadingBoxes = ref({})
const composingId = ref(null)
const composeResultByCampaign = ref({})
const validatingId = ref(null)
const newCampaignWeight = ref('')

async function fetchCampaigns() {
  loading.value = true
  error.value = null
  try {
    const res = await api.get(campaignsPath())
    campaigns.value = Array.isArray(res.data) ? res.data : []
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur chargement campagnes'
    campaigns.value = []
  } finally {
    loading.value = false
  }
}

async function fetchBoxes(campaignId) {
  loadingBoxes.value[campaignId] = true
  try {
    const res = await api.get(boxesPath(campaignId))
    boxesByCampaign.value[campaignId] = Array.isArray(res.data) ? res.data : []
  } catch (e) {
    boxesByCampaign.value[campaignId] = []
  } finally {
    loadingBoxes.value[campaignId] = false
  }
}

function toggleBoxes(campaignId) {
  if (expandedId.value === campaignId) {
    expandedId.value = null
    return
  }
  expandedId.value = campaignId
  if (!boxesByCampaign.value[campaignId]) fetchBoxes(campaignId)
}

async function compose(campaignId) {
  composingId.value = campaignId
  composeResultByCampaign.value[campaignId] = null
  try {
    const res = await api.post(composePath(campaignId))
    composeResultByCampaign.value[campaignId] = res.data
    await fetchBoxes(campaignId)
  } catch (e) {
    composeResultByCampaign.value[campaignId] = { error: e.response?.data?.error || e.message || 'Erreur composition' }
  } finally {
    composingId.value = null
  }
}

async function validateBox(boxId, campaignId) {
  validatingId.value = boxId
  try {
    await api.post(validatePath(boxId))
    if (campaignId) await fetchBoxes(campaignId)
  } catch (e) {
    console.error(e)
  } finally {
    validatingId.value = null
  }
}

async function createCampaign() {
  const maxWeight = parseInt(newCampaignWeight.value, 10)
  if (!Number.isInteger(maxWeight) || maxWeight <= 0) return
  try {
    await api.post(campaignsPath(), { max_weight_per_box: maxWeight })
    newCampaignWeight.value = ''
    await fetchCampaigns()
  } catch (e) {
    error.value = e.response?.data?.error || e.message || 'Erreur création'
  }
}

function formatDate(val) {
  if (!val) return '—'
  const d = new Date(val)
  return Number.isNaN(d.getTime()) ? val : d.toLocaleDateString('fr-FR')
}

onMounted(() => fetchCampaigns())
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-6">
    <h3 class="text-lg font-medium text-slate-800 mb-2">Campagnes</h3>
    <p class="text-sm text-slate-500 mb-6">Liste des campagnes, création et composition des box.</p>

    <div v-if="error" class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
      {{ error }}
    </div>

    <div class="mb-6 flex flex-wrap items-end gap-3">
      <label class="flex flex-col gap-1">
        <span class="text-xs font-medium text-slate-500">Poids max par box (g)</span>
        <input
          v-model.number="newCampaignWeight"
          type="number"
          min="1"
          placeholder="ex: 2000"
          class="rounded-lg border border-slate-300 px-3 py-2 w-36 text-sm"
        />
      </label>
      <button
        type="button"
        @click="createCampaign"
        :disabled="!newCampaignWeight || newCampaignWeight < 1"
        class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Nouvelle campagne
      </button>
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-slate-600"></div>
    </div>

    <div v-else-if="campaigns.length === 0" class="text-slate-500 text-sm py-4">
      Aucune campagne.
    </div>

    <ul v-else class="space-y-4">
      <li
        v-for="c in campaigns"
        :key="c.id"
        class="border border-slate-200 rounded-lg overflow-hidden"
      >
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50/50">
          <div class="flex flex-wrap items-center gap-4">
            <span class="font-medium text-slate-800">Campagne #{{ c.id }}</span>
            <span class="text-sm text-slate-600">Poids max : {{ c.max_weight_per_box }} g</span>
            <span class="text-sm text-slate-500">{{ formatDate(c.created_at) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="compose(c.id)"
              :disabled="composingId === c.id"
              class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
            >
              {{ composingId === c.id ? 'Composition…' : 'Composer' }}
            </button>
            <button
              type="button"
              @click="toggleBoxes(c.id)"
              class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50"
            >
              {{ expandedId === c.id ? 'Masquer les box' : 'Voir les box' }}
            </button>
          </div>
        </div>

        <div v-if="expandedId === c.id" class="p-4 border-t border-slate-200">
          <div v-if="loadingBoxes[c.id]" class="flex justify-center py-4">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-slate-300 border-t-slate-600"></div>
          </div>
          <div v-else-if="composeResultByCampaign[c.id] && composingId !== c.id" class="mb-3 text-sm">
            <span v-if="composeResultByCampaign[c.id].error" class="text-red-600">{{ composeResultByCampaign[c.id].error }}</span>
            <span v-else class="text-slate-600">
              Dernière composition : score {{ composeResultByCampaign[c.id].score }}, {{ composeResultByCampaign[c.id].boxes_count }} box(es).
            </span>
          </div>
          <div v-else-if="!(boxesByCampaign[c.id]?.length)" class="text-slate-500 text-sm py-2">
            Aucune box. Cliquez sur « Composer » pour lancer la composition.
          </div>
          <ul v-else class="space-y-3">
            <li
              v-for="box in boxesByCampaign[c.id]"
              :key="box.id"
              class="rounded-lg border border-slate-200 p-3 bg-white text-sm"
            >
              <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="font-medium text-slate-800">
                  {{ box.subscriber?.first_name }} {{ box.subscriber?.last_name }}
                  <span class="text-slate-500 font-normal">({{ box.subscriber?.email }})</span>
                </span>
                <span
                  :class="[
                    'px-2 py-0.5 rounded text-xs font-medium',
                    box.status === 'validated' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                  ]"
                >
                  {{ box.status === 'validated' ? 'Validée' : 'Brouillon' }}
                </span>
              </div>
              <div class="flex flex-wrap gap-3 text-slate-600 mb-2">
                <span>Score : {{ box.score }}</span>
                <span>Poids : {{ box.total_weight }} g</span>
                <span>Prix : {{ box.total_price }} €</span>
              </div>
              <div v-if="box.articles?.length" class="mb-2">
                <span class="text-slate-500">Articles :</span>
                <ul class="mt-1 list-disc list-inside text-slate-600">
                  <li v-for="a in box.articles" :key="a.id">{{ a.designation }} ({{ a.price }} €)</li>
                </ul>
              </div>
              <button
                v-if="box.status !== 'validated'"
                type="button"
                @click="validateBox(box.id, c.id)"
                :disabled="validatingId === box.id"
                class="mt-2 px-2 py-1 rounded bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700 disabled:opacity-50"
              >
                {{ validatingId === box.id ? '…' : 'Valider la box' }}
              </button>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</template>
