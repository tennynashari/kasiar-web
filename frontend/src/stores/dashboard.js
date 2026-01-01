import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useDashboardStore = defineStore('dashboard', () => {
  const stats = ref(null)
  const loading = ref(false)

  const fetchDashboard = async (params = {}) => {
    loading.value = true
    try {
      const response = await api.get('/dashboard', { params })
      stats.value = response.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchSalesReport = async (params) => {
    try {
      const response = await api.get('/reports/sales', { params })
      return response.data
    } catch (error) {
      throw error
    }
  }

  return {
    stats,
    loading,
    fetchDashboard,
    fetchSalesReport
  }
})
