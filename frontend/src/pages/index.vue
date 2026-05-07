<script setup>
import { onMounted, ref, computed, watch, nextTick } from 'vue'
import { useKerbalStore } from '@stores/KerbalStore.mjs'
import SolarSystem from '@components/kerbal/SolarSystem.vue'
import BodyDetailPanel from '@components/kerbal/BodyDetailPanel.vue'
import { RefreshCw, ArrowLeft } from 'lucide-vue-next'

const store = useKerbalStore()
const selected = ref(null)
const focusedId = ref(null)

onMounted(() => store.loadAll())

const totalAchievements = computed(() => store.achievements.length)
const completedAchievements = computed(
  () => store.progress.filter((p) => p.is_completed && p.user_id === store.userId).length
)

const center = computed(() => {
  if (focusedId.value != null) {
    return store.bodies.find((b) => b.id === focusedId.value) ?? store.star
  }
  return store.star
})

const orbiters = computed(() => {
  if (focusedId.value != null) {
    return store.moonsOf(focusedId.value)
  }
  return store.planets
})

function selectBody(body) {
  selected.value = body
  if (!body) return
  if (body.body_type === 'star') {
    focusedId.value = null
    return
  }
  const hasMoons = store.moonsOf(body.id).length > 0
  const isPlanetOfStar = store.star && body.parent_id === store.star.id
  if (isPlanetOfStar && hasMoons) {
    focusedId.value = body.id
  }
}

function backToSystem() {
  focusedId.value = null
  selected.value = null
}

const counterPulse = ref(false)
watch(completedAchievements, async () => {
  counterPulse.value = false
  await nextTick()
  counterPulse.value = true
  setTimeout(() => (counterPulse.value = false), 700)
})
</script>

<template>
  <div class="relative w-screen h-screen overflow-hidden text-white font-sans">
    <SolarSystem
      :center="center"
      :orbiters="orbiters"
      :selected-id="selected?.id ?? null"
      :completion-count="store.completedCountForBody"
      :achievements-for-body="store.achievementsForBody"
      @select="selectBody"
    />

    <header class="absolute top-0 left-0 right-0 px-6 py-4 flex items-center justify-between pointer-events-none z-40">
      <div class="flex items-center gap-3 pointer-events-auto">
        <button
          v-if="focusedId != null"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 hover:bg-white/20 text-xs transition backdrop-blur"
          @click="backToSystem"
        >
          <ArrowLeft class="w-3.5 h-3.5" />
          Back to system
        </button>
        <div>
          <h1 class="title-shimmer text-xl sm:text-2xl font-bold tracking-wide">
            Kerbal <span class="text-indigo-300">Helper</span>
          </h1>
          <p class="text-xs text-white/60 mt-0.5">
            Click a body to inspect Δv, atmosphere and tick off your flybys, orbits, probes &amp; landings.
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2 pointer-events-auto">
        <div
          class="px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs transition"
          :class="{ 'counter-pulse': counterPulse }"
        >
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

    <Transition name="loader">
      <div
        v-if="store.loading && !store.bodies.length"
        class="absolute inset-0 flex items-center justify-center z-30 bg-black/70 backdrop-blur-sm"
      >
        <div class="flex flex-col items-center gap-4 text-white/80">
          <div class="loader-orbit">
            <span class="loader-core" />
            <span class="loader-ring">
              <span class="loader-dot" />
            </span>
            <span class="loader-ring loader-ring-2">
              <span class="loader-dot" />
            </span>
          </div>
          <span class="text-sm tracking-widest uppercase">Loading the Kerbol system…</span>
        </div>
      </div>
    </Transition>

    <Transition name="error">
      <div
        v-if="store.error"
        class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 px-4 py-2 rounded-lg bg-rose-600/90 text-white text-sm shadow-lg"
      >
        {{ store.error }}
      </div>
    </Transition>

    <Transition name="panel">
      <BodyDetailPanel
        v-if="selected"
        :body="selected"
        @close="selected = null"
        @select="selectBody"
      />
    </Transition>
  </div>
</template>

<style scoped>
.title-shimmer {
  background: linear-gradient(90deg, #fff 0%, #c7d2ff 30%, #fff 60%, #c7d2ff 100%);
  background-size: 200% 100%;
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  animation: titleShimmer 6s linear infinite;
}
@keyframes titleShimmer {
  from { background-position: 0% 0%; }
  to   { background-position: 200% 0%; }
}

.counter-pulse {
  animation: counterPop 0.7s ease;
  background: rgba(110, 231, 183, 0.15) !important;
  border-color: rgba(110, 231, 183, 0.5) !important;
}
@keyframes counterPop {
  0%   { transform: scale(1); }
  30%  { transform: scale(1.18); }
  100% { transform: scale(1); }
}

/* Panel slide-in */
.panel-enter-active,
.panel-leave-active { transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.35s ease; }
.panel-enter-from,
.panel-leave-to    { transform: translateX(100%); opacity: 0; }

/* Loader fade */
.loader-enter-active,
.loader-leave-active { transition: opacity 0.4s ease; }
.loader-enter-from,
.loader-leave-to    { opacity: 0; }

/* Error toast */
.error-enter-active,
.error-leave-active { transition: transform 0.3s ease, opacity 0.3s ease; }
.error-enter-from,
.error-leave-to    { transform: translate(-50%, 20px); opacity: 0; }

/* Custom orbit loader */
.loader-orbit {
  position: relative;
  width: 80px;
  height: 80px;
}
.loader-core {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%, #fff, #ffcc4d 60%, #ff8c00);
  box-shadow: 0 0 18px 4px #ffae34aa;
  transform: translate(-50%, -50%);
  animation: corePulse 1.6s ease-in-out infinite alternate;
}
@keyframes corePulse {
  from { box-shadow: 0 0 12px 2px #ffae3488; }
  to   { box-shadow: 0 0 22px 6px #ffae34cc; }
}
.loader-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 1px dashed rgba(255, 255, 255, 0.2);
  animation: spin 2.4s linear infinite;
}
.loader-ring-2 {
  inset: 14px;
  animation: spin 1.6s linear infinite reverse;
  border-color: rgba(180, 200, 255, 0.25);
}
.loader-dot {
  position: absolute;
  top: -4px;
  left: 50%;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #6ee7ff;
  box-shadow: 0 0 8px 2px #6ee7ffaa;
  transform: translateX(-50%);
}
.loader-ring-2 .loader-dot {
  background: #c084fc;
  box-shadow: 0 0 8px 2px #c084fcaa;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}
</style>

<route lang="yaml">
name: index
meta:
  title: Kerbal Helper
</route>
