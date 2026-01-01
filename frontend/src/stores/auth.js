import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const loading = ref(false)

  const initAuth = () => {
    const savedToken = localStorage.getItem('token')
    const savedUser = localStorage.getItem('user')
    
    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
    }
  }

  const login = async (email, password) => {
    loading.value = true
    try {
      const response = await api.post('/login', { email, password })
      
      token.value = response.data.token
      user.value = response.data.user
      
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      
      return true
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  const isOwner = () => user.value?.role === 'owner'
  const isSupervisor = () => user.value?.role === 'supervisor'
  const isKasir = () => user.value?.role === 'kasir'
  const isKitchen = () => user.value?.role === 'kitchen'

  return {
    user,
    token,
    loading,
    initAuth,
    login,
    logout,
    isOwner,
    isSupervisor,
    isKasir,
    isKitchen
  }
})
