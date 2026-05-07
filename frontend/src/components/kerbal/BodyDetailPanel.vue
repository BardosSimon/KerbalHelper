<script setup>
import { computed, reactive } from 'vue'
import { X, Rocket, Orbit, Satellite, MapPin, Undo2, Loader2 } from 'lucide-vue-next'
import { useKerbalStore } from '@stores/KerbalStore.mjs'

const props = defineProps({
  body: { type: Object, default: null }
})

const emit = defineEmits(['close', 'select'])

const store = useKerbalStore()

const moons = computed(() => (props.body ? store.moonsOf(props.body.id) : []))
const achievements = computed(() => (props.body ? store.achievementsForBody(props.body.id) : []))

const TYPE_META = {
  flyby:   { label: 'Flyby',   icon: Rocket },
  orbit:   { label: 'Orbit',   icon: Orbit },
  probe:   { label: 'Probe',   icon: Satellite },
  landing: { label: 'Landing', icon: MapPin },
  return:  { label: 'Return',  icon: Undo2 },
  science: { label: 'Science', icon: Satellite }
}

const fmtNumber = (n) => (n == null ? '—' : Number(n).toLocaleString())
const fmtKm = (n) => (n == null ? '—' : `${fmtNumber(n)} km`)
const fmtM = (n) => (n == null ? '—' : `${fmtNumber(n)} m`)
const fmtMps = (n) => (n == null ? '—' : `${fmtNumber(n)} m/s`)

const toggling = reactive({})

async function toggle(achievement) {
  if (toggling[achievement.id]) return
  toggling[achievement.id] = true
  try {
    await store.toggleAchievement(achievement)
  } finally {
    toggling[achievement.id] = false
  }
}
</script>

<template>
  <aside
    v-if="body"
    class="fixed top-0 right-0 h-full w-full sm:w-[420px] bg-[#0c0c1f]/95 backdrop-blur-md border-l border-white/10 text-white z-30 overflow-y-auto shadow-2xl"
  >
    <header class="flex items-center justify-between px-5 py-4 border-b border-white/10 sticky top-0 bg-[#0c0c1f]/95 backdrop-blur-md z-10">
      <div>
        <p class="text-xs uppercase tracking-widest text-white/50">{{ body.body_type }}</p>
        <h2 class="text-2xl font-bold">{{ body.name }}</h2>
      </div>
      <button
        class="p-2 rounded hover:bg-white/10 transition"
        @click="emit('close')"
        aria-label="Close"
      >
        <X class="w-5 h-5" />
      </button>
    </header>

    <section class="px-5 py-4 grid grid-cols-2 gap-3 text-sm">
      <div class="stat">
        <div class="stat-label">Radius</div>
        <div class="stat-value">{{ fmtKm(body.radius_km) }}</div>
      </div>
      <div class="stat">
        <div class="stat-label">Surface gravity</div>
        <div class="stat-value">{{ body.surface_gravity_g != null ? body.surface_gravity_g + ' g' : '—' }}</div>
      </div>
      <div class="stat">
        <div class="stat-label">Semi-major axis</div>
        <div class="stat-value">{{ fmtKm(body.semi_major_axis_km) }}</div>
      </div>
      <div class="stat">
        <div class="stat-label">Sphere of influence</div>
        <div class="stat-value">{{ fmtKm(body.sphere_of_influence_km) }}</div>
      </div>
      <div class="stat">
        <div class="stat-label">Atmosphere</div>
        <div class="stat-value">
          {{ body.has_atmosphere ? `Yes (${fmtM(body.atmosphere_height_m)})` : 'None' }}
        </div>
      </div>
      <div class="stat">
        <div class="stat-label">Low orbit altitude</div>
        <div class="stat-value">{{ fmtM(body.low_orbit_altitude_m) }}</div>
      </div>
      <div class="stat col-span-2 bg-indigo-500/10 border-indigo-500/30">
        <div class="stat-label text-indigo-200">Δv from Kerbin LKO</div>
        <div class="stat-value text-indigo-100">{{ fmtMps(body.delta_v_from_kerbin_low_orbit_mps) }}</div>
      </div>
      <div class="stat">
        <div class="stat-label">Δv to land</div>
        <div class="stat-value">{{ fmtMps(body.delta_v_landing_mps) }}</div>
      </div>
      <div v-if="body.name !== 'Kerbin'" class="stat">
        <div class="stat-label">Δv return to Kerbin</div>
        <div class="stat-value">{{ fmtMps(body.delta_v_return_mps) }}</div>
      </div>
    </section>

    <section v-if="moons.length" class="px-5 py-3 border-t border-white/10">
      <h3 class="text-xs uppercase tracking-widest text-white/50 mb-2">Moons</h3>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="m in moons"
          :key="m.id"
          class="px-3 py-1 rounded-full bg-white/5 hover:bg-white/15 border border-white/10 text-xs transition"
          @click="emit('select', m)"
        >
          {{ m.name }}
        </button>
      </div>
    </section>

    <section class="px-5 py-4 border-t border-white/10">
      <h3 class="text-xs uppercase tracking-widest text-white/50 mb-3">Achievements</h3>
      <ul v-if="achievements.length" class="space-y-2">
        <li
          v-for="a in achievements"
          :key="a.id"
          class="flex items-start gap-3 p-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition"
        >
          <button
            class="mt-0.5 w-5 h-5 shrink-0 rounded border border-white/30 flex items-center justify-center transition"
            :class="store.progressFor(a.id)?.is_completed
              ? 'bg-emerald-500 border-emerald-400'
              : 'bg-transparent hover:bg-white/10'"
            @click="toggle(a)"
            :disabled="toggling[a.id]"
            :aria-label="`Toggle ${a.name}`"
          >
            <svg v-if="store.progressFor(a.id)?.is_completed" class="w-3 h-3 text-white" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="3">
              <path d="M3 8.5l3.5 3.5L13 5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <component :is="TYPE_META[a.achievement_type]?.icon ?? Rocket" class="w-4 h-4 text-white/60" />
              <span class="font-medium">{{ TYPE_META[a.achievement_type]?.label ?? a.achievement_type }}</span>
              <span
                class="ml-auto text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full border"
                :class="{
                  'bg-emerald-500/15 border-emerald-400/40 text-emerald-200': a.difficulty === 'easy',
                  'bg-sky-500/15 border-sky-400/40 text-sky-200': a.difficulty === 'medium',
                  'bg-amber-500/15 border-amber-400/40 text-amber-200': a.difficulty === 'hard',
                  'bg-rose-500/15 border-rose-400/40 text-rose-200': a.difficulty === 'extreme'
                }"
              >
                {{ a.difficulty }}
              </span>
            </div>
            <p v-if="a.description" class="text-xs text-white/60 mt-1">{{ a.description }}</p>
            <div class="mt-1 flex flex-wrap gap-3 text-[11px] text-white/50">
              <span v-if="a.recommended_delta_v_mps != null">Δv ~ {{ fmtMps(a.recommended_delta_v_mps) }}</span>
              <span v-if="a.science_reward">+{{ a.science_reward }} science</span>
              <span v-if="a.funds_reward">+{{ fmtNumber(a.funds_reward) }} funds</span>
            </div>
          </div>
          <Loader2 v-if="toggling[a.id]" class="w-4 h-4 animate-spin text-white/50" />
        </li>
      </ul>
      <p v-else class="text-sm text-white/50">No achievements defined for {{ body.name }}.</p>
    </section>
  </aside>
</template>

<style scoped>
.stat {
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.03);
  border-radius: 0.5rem;
  padding: 0.6rem 0.75rem;
}
.stat-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(255, 255, 255, 0.5);
}
.stat-value {
  font-weight: 600;
  margin-top: 2px;
}
</style>
