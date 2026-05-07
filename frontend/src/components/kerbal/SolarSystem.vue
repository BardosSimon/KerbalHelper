<script setup>
import { computed } from 'vue'

const props = defineProps({
  star: { type: Object, default: null },
  planets: { type: Array, default: () => [] },
  selectedId: { type: Number, default: null },
  completionCount: { type: Function, required: true },
  achievementsForBody: { type: Function, required: true }
})

const emit = defineEmits(['select'])

const BODY_STYLES = {
  Kerbol: { color: '#ffcc4d', glow: '#ffae34', size: 96 },
  Moho:   { color: '#a85b3a', glow: '#7a3a22', size: 18 },
  Eve:    { color: '#8a4ad0', glow: '#5a279c', size: 30 },
  Kerbin: { color: '#3a8ee0', glow: '#1f5e9b', size: 28, accent: '#4caf50' },
  Duna:   { color: '#c45b3c', glow: '#7a2c1a', size: 24 },
  Dres:   { color: '#8a8580', glow: '#4a4540', size: 16 },
  Jool:   { color: '#67b06b', glow: '#2f6d34', size: 60 },
  Eeloo:  { color: '#d8e3e8', glow: '#7a8c93', size: 18 }
}

const fallbackStyle = { color: '#9aa0a6', glow: '#444', size: 20 }

const styleFor = (name) => BODY_STYLES[name] ?? fallbackStyle

const minOrbit = 130
const maxOrbit = 460

const orbitData = computed(() => {
  if (!props.planets.length) return []
  const distances = props.planets.map((p) => Math.log10((p.semi_major_axis_km ?? 1) + 1))
  const min = Math.min(...distances)
  const max = Math.max(...distances)
  const span = Math.max(max - min, 0.0001)
  return props.planets.map((p, i) => {
    const t = (distances[i] - min) / span
    const radius = minOrbit + t * (maxOrbit - minOrbit)
    const period = 18 + t * 180
    const offsetDeg = (i * 47) % 360
    const delay = -((offsetDeg / 360) * period)
    return { planet: p, radius, period, delay }
  })
})
</script>

<template>
  <div class="solar-stage">
    <div class="starfield" />
    <div class="starfield twinkle" />

    <div class="system">
      <button
        v-if="star"
        class="star"
        :style="{
          width: styleFor(star.name).size + 'px',
          height: styleFor(star.name).size + 'px',
          background: `radial-gradient(circle at 35% 35%, #fff7c4 0%, ${styleFor(star.name).color} 45%, ${styleFor(star.name).glow} 100%)`,
          boxShadow: `0 0 60px 10px ${styleFor(star.name).glow}, 0 0 120px 30px ${styleFor(star.name).glow}55`
        }"
        :class="{ selected: selectedId === star.id }"
        @click="emit('select', star)"
        :title="star.name"
      >
        <span class="sr-only">{{ star.name }}</span>
      </button>

      <div
        v-for="o in orbitData"
        :key="o.planet.id"
        class="orbit-ring"
        :style="{ width: o.radius * 2 + 'px', height: o.radius * 2 + 'px' }"
      />

      <div
        v-for="o in orbitData"
        :key="'spin-' + o.planet.id"
        class="orbit-spin"
        :style="{
          width: o.radius * 2 + 'px',
          height: o.radius * 2 + 'px',
          animationDuration: o.period + 's',
          animationDelay: o.delay + 's'
        }"
      >
        <button
          class="planet"
          :class="{ selected: selectedId === o.planet.id }"
          :style="{
            width: styleFor(o.planet.name).size + 'px',
            height: styleFor(o.planet.name).size + 'px',
            background: `radial-gradient(circle at 30% 30%, #ffffffaa 0%, ${styleFor(o.planet.name).color} 40%, ${styleFor(o.planet.name).glow} 100%)`,
            boxShadow: `0 0 18px 2px ${styleFor(o.planet.name).glow}aa`,
            animationDuration: o.period + 's',
            animationDelay: o.delay + 's'
          }"
          @click.stop="emit('select', o.planet)"
          :title="o.planet.name"
        >
          <span class="planet-label">{{ o.planet.name }}</span>
          <span class="planet-progress">
            {{ completionCount(o.planet.id) }}/{{ achievementsForBody(o.planet.id).length }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.solar-stage {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  background:
    radial-gradient(ellipse at center, #1a1a3e 0%, #0a0a1f 55%, #03030a 100%);
}

.starfield {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(2px 2px at 20% 30%, #fff 0%, transparent 100%),
    radial-gradient(1px 1px at 75% 60%, #fff 0%, transparent 100%),
    radial-gradient(1.5px 1.5px at 50% 80%, #fff 0%, transparent 100%),
    radial-gradient(1px 1px at 10% 70%, #fff 0%, transparent 100%),
    radial-gradient(1px 1px at 90% 20%, #fff 0%, transparent 100%),
    radial-gradient(1.5px 1.5px at 35% 50%, #fff 0%, transparent 100%),
    radial-gradient(1px 1px at 60% 15%, #fff 0%, transparent 100%),
    radial-gradient(1px 1px at 80% 85%, #fff 0%, transparent 100%);
  background-size: 600px 600px;
  opacity: 0.65;
  pointer-events: none;
}
.starfield.twinkle {
  background-size: 400px 400px;
  opacity: 0.3;
  animation: twinkle 6s ease-in-out infinite alternate;
}
@keyframes twinkle {
  from { opacity: 0.15; }
  to   { opacity: 0.6; }
}

.system {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
}

.star {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 9999px;
  border: none;
  padding: 0;
  cursor: pointer;
  transform: translate(-50%, -50%);
  transition: transform 0.2s ease;
  animation: starPulse 4s ease-in-out infinite alternate;
}
.star:hover { transform: translate(-50%, -50%) scale(1.06); }
.star.selected { outline: 2px solid #fff8; outline-offset: 6px; }
@keyframes starPulse {
  from { filter: brightness(0.95); }
  to   { filter: brightness(1.15); }
}

.orbit-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  border: 1px dashed rgba(255, 255, 255, 0.08);
  border-radius: 9999px;
  transform: translate(-50%, -50%);
  pointer-events: none;
}

.orbit-spin {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 9999px;
  pointer-events: none;
  animation-name: orbit;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
}
@keyframes orbit {
  from { transform: translate(-50%, -50%) rotate(0deg); }
  to   { transform: translate(-50%, -50%) rotate(360deg); }
}

.planet {
  position: absolute;
  top: 50%;
  left: 100%;
  transform: translate(-50%, -50%);
  border-radius: 9999px;
  border: none;
  padding: 0;
  cursor: pointer;
  pointer-events: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #fff;
  text-shadow: 0 1px 2px #0009;
  animation-name: counterRotate;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
  animation-direction: reverse;
  transition: filter 0.2s ease;
}
.planet:hover { filter: brightness(1.25); }
.planet.selected { outline: 2px solid #fff; outline-offset: 4px; }
@keyframes counterRotate {
  from { transform: translate(-50%, -50%) rotate(0deg); }
  to   { transform: translate(-50%, -50%) rotate(-360deg); }
}

.planet-label {
  position: absolute;
  top: 100%;
  margin-top: 6px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
  white-space: nowrap;
}
.planet-progress {
  position: absolute;
  top: -22px;
  font-size: 10px;
  background: rgba(0, 0, 0, 0.55);
  padding: 1px 6px;
  border-radius: 999px;
  white-space: nowrap;
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.sr-only {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px; overflow: hidden;
  clip: rect(0,0,0,0); border: 0;
}
</style>
