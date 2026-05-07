import { defineStore } from 'pinia'
import { kerbalApi } from '@utils/kerbalApi.mjs'

export const useKerbalStore = defineStore('kerbal', {
  state: () => ({
    userId: 1,
    bodies: [],
    achievements: [],
    progress: [],
    loading: false,
    error: null
  }),
  persist: {
    pick: ['userId']
  },
  getters: {
    star: (state) => state.bodies.find((b) => b.body_type === 'star') ?? null,
    planets: (state) => {
      const star = state.bodies.find((b) => b.body_type === 'star')
      if (!star) return []
      return state.bodies
        .filter((b) => b.parent_id === star.id)
        .slice()
        .sort((a, b) => (a.semi_major_axis_km ?? 0) - (b.semi_major_axis_km ?? 0))
    },
    moonsOf: (state) => (parentId) =>
      state.bodies
        .filter((b) => b.parent_id === parentId)
        .slice()
        .sort((a, b) => (a.semi_major_axis_km ?? 0) - (b.semi_major_axis_km ?? 0)),
    achievementsForBody: (state) => (bodyId) =>
      state.achievements
        .filter((a) => a.celestial_body_id === bodyId)
        .slice()
        .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0)),
    progressFor: (state) => (achievementId) =>
      state.progress.find(
        (p) => p.program_achievement_id === achievementId && p.user_id === state.userId
      ) ?? null,
    completedCountForBody() {
      return (bodyId) => {
        const list = this.achievementsForBody(bodyId)
        return list.filter((a) => this.progressFor(a.id)?.is_completed).length
      }
    }
  },
  actions: {
    async loadAll() {
      this.loading = true
      this.error = null
      try {
        const [bodies, achievements, progress] = await Promise.all([
          kerbalApi.fetchBodies(),
          kerbalApi.fetchAchievements(),
          kerbalApi.fetchUserProgress(this.userId)
        ])
        this.bodies = bodies ?? []
        this.achievements = achievements ?? []
        this.progress = progress ?? []
      } catch (e) {
        this.error = e?.message ?? 'Failed to load Kerbal data'
      } finally {
        this.loading = false
      }
    },
    async toggleAchievement(achievement) {
      const existing = this.progressFor(achievement.id)
      const nextCompleted = !existing?.is_completed
      const payload = {
        user_id: this.userId,
        program_achievement_id: achievement.id,
        is_completed: nextCompleted,
        completed_at: nextCompleted ? new Date().toISOString() : null
      }
      const saved = await kerbalApi.upsertProgress(payload)
      const idx = this.progress.findIndex(
        (p) => p.user_id === saved.user_id && p.program_achievement_id === saved.program_achievement_id
      )
      if (idx >= 0) {
        this.progress.splice(idx, 1, saved)
      } else {
        this.progress.push(saved)
      }
    }
  }
})
