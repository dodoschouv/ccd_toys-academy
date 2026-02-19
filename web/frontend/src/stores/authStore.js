import { defineStore } from 'pinia'
import api from '../api/index.js'

const base = import.meta.env.VITE_API_URL ? '' : '/api'
const loginPath = () => `${base}/auth/login`
const registerPath = () => `${base}/auth/register`
const mePath = () => `${base}/auth/me`

const TOKEN_KEY = 'toys_academy_token'
const USER_KEY = 'toys_academy_user'

function getStoredToken() {
  try {
    return localStorage.getItem(TOKEN_KEY)
  } catch {
    return null
  }
}

function setStoredToken(token) {
  try {
    if (token) localStorage.setItem(TOKEN_KEY, token)
    else localStorage.removeItem(TOKEN_KEY)
  } catch {}
}

function getStoredUser() {
  try {
    const raw = localStorage.getItem(USER_KEY)
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
}

function setStoredUser(user) {
  try {
    if (user) localStorage.setItem(USER_KEY, JSON.stringify(user))
    else localStorage.removeItem(USER_KEY)
  } catch {}
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: getStoredToken(),
    user: getStoredUser(),
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user,
    subscriberId: (state) => state.user?.subscriber_id ?? null,
  },

  actions: {
    setAuth(token, user) {
      this.token = token
      this.user = user
      setStoredToken(token)
      setStoredUser(user)
    },

    clearAuth() {
      this.token = null
      this.user = null
      setStoredToken(null)
      setStoredUser(null)
    },

    async login(email, password) {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.post(loginPath(), { email: email.trim(), password })
        this.setAuth(data.token, data.user)
        return { success: true }
      } catch (e) {
        const msg = e.response?.data?.error || e.message || 'Erreur de connexion'
        this.error = msg
        return { success: false, error: msg }
      } finally {
        this.loading = false
      }
    },

    async register(payload) {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.post(registerPath(), payload)
        this.setAuth(data.token, data.user)
        return { success: true }
      } catch (e) {
        const msg = e.response?.data?.error || e.message || 'Erreur d\'inscription'
        this.error = msg
        return { success: false, error: msg }
      } finally {
        this.loading = false
      }
    },

    async fetchMe() {
      if (!this.token) return null
      try {
        const { data } = await api.get(mePath())
        this.user = data
        setStoredUser(data)
        return data
      } catch {
        this.clearAuth()
        return null
      }
    },

    logout() {
      this.clearAuth()
    },
  },
})
