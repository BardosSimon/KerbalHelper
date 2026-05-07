<script setup>
import { onMounted, ref, computed } from 'vue'
import { useKerbalStore } from '@stores/KerbalStore.mjs'
import SolarSystem from '@components/kerbal/SolarSystem.vue'
import BodyDetailPanel from '@components/kerbal/BodyDetailPanel.vue'
import { Loader2, RefreshCw } from 'lucide-vue-next'

const store = useKerbalStore()
const selected = ref(null)

onMounted(() => store.loadAll())

const totalAchievements = computed(() => store.achievements.length)
const completedAchievements = computed(
  () => store.progress.filter((p) => p.is_completed && p.user_id === store.userId).length
)

function selectBody(body) {
  selected.value = body
}
</script>

<template>
  <div class="relative w-screen h-screen overflow-hidden text-white font-sans">
    <SolarSystem
      :star="store.star"
      :planets="store.planets"
      :selected-id="selected?.id ?? null"
      :completion-count="store.completedCountForBody"
      :achievements-for-body="store.achievementsForBody"
      @select="selectBody"
    />

    <header class="absolute top-0 left-0 right-0 px-6 py-4 flex items-center justify-between pointer-events-none z-20">
      <div class="pointer-events-auto">
        <h1 class="text-xl sm:text-2xl font-bold tracking-wide">
          Kerbal <span class="text-indigo-300">Helper</span>
        </h1>
        <p class="text-xs text-white/60 mt-0.5">
          Click a body to inspect Δv, atmosphere and tick off your flybys, orbits, probes &amp; landings.
        </p>
      </div>
      <div class="flex items-center gap-2 pointer-events-auto">
        <div class="px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs">
          <span class="text-white/60">Achievements:</span>
          <span class="ml-1 font-semibold">{{ completedAchievements }} / {{ totalAchievements }}</span>
        </div>
        <button
          class="p-2 rounded-full bg-white/5 border border-white/10 hover:bg-white/15 transition"
          @click="store.loadAll()"
          aria-label="Refresh"
          :disabled="store.loading"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': store.loading }" />
        </button>
      </div>
    </header>

    <div
      v-if="store.loading && !store.bodies.length"
      class="absolute inset-0 flex items-center justify-center z-30 bg-black/60"
    >
      <div class="flex items-center gap-3 text-white/80">
        <Loader2 class="w-5 h-5 animate-spin" />
        <span>Loading the Kerbol system…</span>
      </div>
    </div>

    <div
      v-if="store.error"
      class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 px-4 py-2 rounded-lg bg-rose-600/90 text-white text-sm shadow-lg"
    >
      {{ store.error }}
    </div>

    <BodyDetailPanel
      :body="selected"
      @close="selected = null"
      @select="selectBody"
    />
  </div>
</template>

<route lang="yaml">
name: index
meta:
  title: Kerbal Helper
</route>
