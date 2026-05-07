import { api } from '@utils/http.mjs'

const unwrap = (res) => res.data?.data ?? res.data

export const kerbalApi = {
  async fetchBodies() {
    return unwrap(await api.get('/api/celestial-bodies'))
  },
  async fetchAchievements() {
    return unwrap(await api.get('/api/achievements'))
  },
  async fetchUserProgress(userId) {
    return unwrap(await api.get(`/api/users/${userId}/progress`))
  },
  async upsertProgress(payload) {
    return unwrap(await api.post('/api/progress', payload))
  },
  async deleteProgress(progressId) {
    await api.delete(`/api/progress/${progressId}`)
  }
}
