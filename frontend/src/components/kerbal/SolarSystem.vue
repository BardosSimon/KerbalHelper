<script setup>
import { computed } from 'vue'

const props = defineProps({
  center: { type: Object, default: null },
  orbiters: { type: Array, default: () => [] },
  selectedId: { type: Number, default: null },
  completionCount: { type: Function, required: true },
  achievementsForBody: { type: Function, required: true }
})

const emit = defineEmits(['select'])

const BODY_STYLES = {
  Kerbol: { color: '#ffcc4d', glow: '#ffae34', size: 96, isStar: true },
  Moho:   { color: '#a85b3a', glow: '#7a3a22', size: 18 },
  Eve:    { color: '#8a4ad0', glow: '#5a279c', size: 30 },
  Gilly:  { color: '#a08070', glow: '#5a4030', size: 10 },
  Kerbin: { color: '#3a8ee0', glow: '#1f5e9b', size: 28 },
  Mun:    { color: '#c8c8c8', glow: '#6a6a6a', size: 14 },
  Minmus: { color: '#a8d8c8', glow: '#4a8070', size: 12 },
  Duna:   { color: '#c45b3c', glow: '#7a2c1a', size: 24 },
  Ike:    { color: '#9a8a80', glow: '#5a4a40', size: 12 },
  Dres:   { color: '#8a8580', glow: '#4a4540', size: 16 },
  Jool:   { color: '#67b06b', glow: '#2f6d34', size: 60 },
  Laythe: { color: '#3a6fd0', glow: '#1f3f80', size: 18 },
  Vall:   { color: '#9ac8e0', glow: '#4a7090', size: 16 },
  Tylo:   { color: '#d8c8a8', glow: '#80704a', size: 22 },
  Bop:    { color: '#7a6a5a', glow: '#3a2a1a', size: 11 },
  Pol:    { color: '#d8c47a', glow: '#80703a', size: 11 },
  Eeloo:  { color: '#d8e3e8', glow: '#7a8c93', size: 18 }
}

const fallbackStyle = { color: '#9aa0a6', glow: '#444', size: 18 }

const styleFor = (name) => BODY_STYLES[name] ?? fallbackStyle

const isStar = computed(() => props.center?.body_type === 'star')

const minOrbit = 130
const maxOrbit = 460

const orbitData = computed(() => {
  if (!props.orbiters.length) return []
  const distances = props.orbiters.map((p) => Math.log10((p.semi_major_axis_km ?? 1) + 1))
  const min = Math.min(...distances)
  const max = Math.max(...distances)
  const span = Math.max(max - min, 0.0001)
  return props.orbiters.map((p, i) => {
    const t = props.orbiters.length === 1 ? 0.5 : (distances[i] - min) / span
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
    <div class="nebula nebula-a" />
    <div class="nebula nebula-b" />
    <div class="nebula nebula-c" />

    <div class="starfield drift-slow" />
    <div class="starfield drift-mid twinkle" />
    <div class="starfield drift-fast bright" />

    <div class="meteor meteor-1" />
    <div class="meteor meteor-2" />
    <div class="meteor meteor-3" />
    <div class="meteor meteor-4" />

    <div class="system">
      <template v-if="center">
        <div
          v-if="isStar"
          class="corona"
          :style="{
            width: styleFor(center.name).size * 1.9 + 'px',
            height: styleFor(center.name).size * 1.9 + 'px'
          }"
        />
        <div
          v-if="isStar"
          class="corona corona-2"
          :style="{
            width: styleFor(center.name).size * 2.6 + 'px',
            height: styleFor(center.name).size * 2.6 + 'px'
          }"
        />
        <div
          class="halo"
          :style="{
            width: styleFor(center.name).size * 1.6 + 'px',
            height: styleFor(center.name).size * 1.6 + 'px',
            background: `radial-gradient(circle, ${styleFor(center.name).glow}55 0%, transparent 70%)`
          }"
        />
      </template>

      <button
        v-if="center"
        class="center-body"
        :style="{
          width: styleFor(center.name).size + 'px',
          height: styleFor(center.name).size + 'px',
          background: isStar
            ? `radial-gradient(circle at 35% 35%, #fff7c4 0%, ${styleFor(center.name).color} 45%, ${styleFor(center.name).glow} 100%)`
            : `radial-gradient(circle at 30% 30%, #ffffffaa 0%, ${styleFor(center.name).color} 40%, ${styleFor(center.name).glow} 100%)`,
          boxShadow: isStar
            ? `0 0 60px 10px ${styleFor(center.name).glow}, 0 0 120px 30px ${styleFor(center.name).glow}55`
            : `0 0 30px 6px ${styleFor(center.name).glow}aa`
        }"
        :class="{ selected: selectedId === center.id, star: isStar }"
        @click="emit('select', center)"
        :title="center.name"
      >
        <span class="center-shine" />
        <span class="sr-only">{{ center.name }}</span>
      </button>

      <span v-if="center && !isStar" class="center-label">{{ center.name }}</span>

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
        <div
          class="planet-anchor"
          :style="{
            animationDuration: o.period + 's',
            animationDelay: o.delay + 's'
          }"
        >
          <button
            class="planet"
            :class="{ selected: selectedId === o.planet.id }"
            :style="{
              width: Math.max(56, styleFor(o.planet.name).size + 16) + 'px',
              height: Math.max(56, styleFor(o.planet.name).size + 16) + 'px'
            }"
            @click.stop="emit('select', o.planet)"
            :title="o.planet.name"
            :aria-label="o.planet.name"
          >
            <span
              class="planet-halo"
              :style="{
                width: styleFor(o.planet.name).size * 2.2 + 'px',
                height: styleFor(o.planet.name).size * 2.2 + 'px',
                background: `radial-gradient(circle, ${styleFor(o.planet.name).glow}55 0%, transparent 70%)`
              }"
            />
            <span
              v-if="selectedId === o.planet.id"
              class="planet-select-ring"
              :style="{
                width: styleFor(o.planet.name).size + 16 + 'px',
                height: styleFor(o.planet.name).size + 16 + 'px',
                borderColor: styleFor(o.planet.name).color
              }"
            />
            <span
              class="planet-visual"
              :style="{
                width: styleFor(o.planet.name).size + 'px',
                height: styleFor(o.planet.name).size + 'px',
                background: `radial-gradient(circle at 30% 30%, #ffffffaa 0%, ${styleFor(o.planet.name).color} 40%, ${styleFor(o.planet.name).glow} 100%)`,
                boxShadow: `0 0 18px 2px ${styleFor(o.planet.name).glow}aa`
              }"
            >
              <span class="planet-shine" />
            </span>
          </button>
          <span
            class="planet-label"
            :style="{ '--label-offset': (styleFor(o.planet.name).size / 2 + 12) + 'px' }"
          >{{ o.planet.name }}</span>
          <span
            class="planet-progress"
            :style="{ '--progress-offset': (styleFor(o.planet.name).size / 2 + 12) + 'px' }"
          >
            {{ completionCount(o.planet.id) }}/{{ achievementsForBody(o.planet.id).length }}
          </span>
        </div>
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
  inset: -10%;
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
  opacity: 0.6;
  pointer-events: none;
  animation-name: drift;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
}
.starfield.drift-slow { animation-duration: 240s; background-size: 800px 800px; opacity: 0.35; }
.starfield.drift-mid  { animation-duration: 160s; background-size: 500px 500px; opacity: 0.45; }
.starfield.drift-fast { animation-duration: 90s;  background-size: 320px 320px; opacity: 0.7;  }

.starfield.twinkle { animation: drift 160s linear infinite, twinkle 6s ease-in-out infinite alternate; }
.starfield.bright {
  filter: drop-shadow(0 0 2px #fff8);
}

@keyframes drift {
  from { background-position: 0 0; }
  to   { background-position: 1000px 600px; }
}
@keyframes twinkle {
  from { opacity: 0.2; }
  to   { opacity: 0.7; }
}

.nebula {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  opacity: 0.55;
  mix-blend-mode: screen;
  animation: nebulaDrift 60s ease-in-out infinite alternate;
}
.nebula-a {
  width: 60vw; height: 60vw;
  top: -10vw; left: -15vw;
  background: radial-gradient(circle, #4a2cb8 0%, transparent 60%);
}
.nebula-b {
  width: 55vw; height: 55vw;
  bottom: -10vw; right: -10vw;
  background: radial-gradient(circle, #1f6dc4 0%, transparent 60%);
  animation-duration: 80s;
  animation-delay: -20s;
}
.nebula-c {
  width: 40vw; height: 40vw;
  top: 30vh; right: 20vw;
  background: radial-gradient(circle, #c43c8a 0%, transparent 60%);
  opacity: 0.35;
  animation-duration: 100s;
  animation-delay: -40s;
}
@keyframes nebulaDrift {
  0%   { transform: translate(0, 0) scale(1); }
  50%  { transform: translate(40px, -30px) scale(1.1); }
  100% { transform: translate(-30px, 50px) scale(0.95); }
}

.meteor {
  position: absolute;
  width: 2px;
  height: 2px;
  background: #fff;
  border-radius: 50%;
  box-shadow: 0 0 8px 2px #fff;
  pointer-events: none;
  opacity: 0;
  animation: meteor 9s linear infinite;
}
.meteor::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  width: 120px;
  height: 1px;
  background: linear-gradient(90deg, #fff 0%, transparent 100%);
  transform: translateY(-50%);
}
.meteor-1 { top: 10%; left: -10%; animation-delay: 0s; }
.meteor-2 { top: 35%; left: -10%; animation-delay: 3s; animation-duration: 11s; }
.meteor-3 { top: 60%; left: -10%; animation-delay: 6s; animation-duration: 8s; }
.meteor-4 { top: 80%; left: -10%; animation-delay: 1.5s; animation-duration: 12s; }
@keyframes meteor {
  0%   { transform: translate(0, 0) rotate(15deg); opacity: 0; }
  5%   { opacity: 1; }
  60%  { opacity: 1; }
  100% { transform: translate(120vw, 30vh) rotate(15deg); opacity: 0; }
}

.system {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
}

.center-body {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 9999px;
  border: none;
  padding: 0;
  cursor: pointer;
  transform: translate(-50%, -50%);
  transition: transform 0.3s ease, width 0.3s ease, height 0.3s ease;
}
.center-body:hover { transform: translate(-50%, -50%) scale(1.06); }
.center-body.selected { outline: 2px solid #fff8; outline-offset: 6px; }
.center-body.star { animation: starPulse 4s ease-in-out infinite alternate; overflow: hidden; }

.center-shine {
  position: absolute;
  inset: -20%;
  border-radius: 50%;
  background: conic-gradient(from 0deg, transparent 0%, rgba(255,255,255,0.18) 25%, transparent 50%, rgba(255,255,255,0.12) 75%, transparent 100%);
  pointer-events: none;
  animation: spinPlain 30s linear infinite;
}
.center-body.star .center-shine {
  background: conic-gradient(from 0deg, transparent 0%, rgba(255,240,180,0.4) 20%, transparent 40%, rgba(255,200,90,0.35) 60%, transparent 80%, rgba(255,240,180,0.3) 100%);
  animation-duration: 18s;
}

.corona {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 50%;
  pointer-events: none;
  transform: translate(-50%, -50%);
  background: conic-gradient(from 0deg,
    rgba(255, 200, 90, 0.0) 0%,
    rgba(255, 220, 140, 0.35) 12%,
    rgba(255, 200, 90, 0.0) 24%,
    rgba(255, 220, 140, 0.3) 38%,
    rgba(255, 200, 90, 0.0) 50%,
    rgba(255, 220, 140, 0.35) 62%,
    rgba(255, 200, 90, 0.0) 74%,
    rgba(255, 220, 140, 0.3) 88%,
    rgba(255, 200, 90, 0.0) 100%);
  filter: blur(8px);
  mix-blend-mode: screen;
  animation: spin 35s linear infinite;
}
.corona-2 {
  filter: blur(18px);
  opacity: 0.6;
  animation: spin 60s linear infinite reverse;
}

.halo {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 50%;
  pointer-events: none;
  transform: translate(-50%, -50%);
  animation: haloBreath 5s ease-in-out infinite alternate;
}
@keyframes haloBreath {
  from { transform: translate(-50%, -50%) scale(0.95); opacity: 0.7; }
  to   { transform: translate(-50%, -50%) scale(1.15); opacity: 1; }
}

@keyframes spin {
  from { transform: translate(-50%, -50%) rotate(0deg); }
  to   { transform: translate(-50%, -50%) rotate(360deg); }
}
@keyframes spinPlain {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.center-label {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, 56px);
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #fff;
  text-shadow: 0 1px 4px #000a;
  pointer-events: none;
}

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
  animation: orbitGlow 6s ease-in-out infinite alternate;
}
@keyframes orbitGlow {
  from { border-color: rgba(255,255,255,0.05); box-shadow: 0 0 0 0 transparent; }
  to   { border-color: rgba(180,200,255,0.18); box-shadow: 0 0 12px rgba(180,200,255,0.05) inset; }
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

.planet-anchor {
  position: absolute;
  top: 50%;
  left: 100%;
  width: 0;
  height: 0;
  pointer-events: auto;
  animation-name: counterRotate;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
}
@keyframes counterRotate {
  from { transform: translate(-50%, -50%) rotate(0deg); }
  to   { transform: translate(-50%, -50%) rotate(-360deg); }
}

.planet {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 56px;
  height: 56px;
  transform: translate(-50%, -50%);
  border-radius: 9999px;
  border: none;
  padding: 0;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: filter 0.2s ease, transform 0.2s ease;
}
.planet-visual {
  position: relative;
  display: block;
  border-radius: 9999px;
  pointer-events: none;
  overflow: hidden;
  transition: transform 0.3s ease, filter 0.2s ease;
}
.planet:hover .planet-visual { filter: brightness(1.3); transform: scale(1.18); }

.planet-shine {
  position: absolute;
  inset: -30%;
  border-radius: 50%;
  background: conic-gradient(from 0deg, transparent 0%, rgba(255,255,255,0.18) 25%, transparent 50%, rgba(0,0,0,0.18) 75%, transparent 100%);
  animation: spinPlain 12s linear infinite;
  pointer-events: none;
  mix-blend-mode: overlay;
}

.planet-halo {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 50%;
  pointer-events: none;
  transform: translate(-50%, -50%);
  opacity: 0;
  transition: opacity 0.3s ease;
  animation: haloBreath 4s ease-in-out infinite alternate;
}
.planet:hover .planet-halo { opacity: 0.8; }
.planet.selected .planet-halo { opacity: 1; }

.planet-select-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 50%;
  border: 2px solid #fff;
  transform: translate(-50%, -50%);
  pointer-events: none;
  animation: selectPulse 1.6s ease-in-out infinite;
}
@keyframes selectPulse {
  0%   { transform: translate(-50%, -50%) scale(1); opacity: 1; }
  100% { transform: translate(-50%, -50%) scale(1.6); opacity: 0; }
}

.planet-label {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, var(--label-offset, 22px));
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
  white-space: nowrap;
  color: #fff;
  text-shadow: 0 1px 2px #000a;
  pointer-events: none;
}
.planet-progress {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, calc(-1 * var(--progress-offset, 32px) - 10px));
  font-size: 10px;
  background: rgba(0, 0, 0, 0.55);
  padding: 1px 6px;
  border-radius: 999px;
  white-space: nowrap;
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #fff;
  pointer-events: none;
}

.sr-only {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px; overflow: hidden;
  clip: rect(0,0,0,0); border: 0;
}
</style>
