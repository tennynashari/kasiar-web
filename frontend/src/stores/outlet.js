import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useOutletStore = defineStore('outlet', () => {
  const outlets = ref([])
  const currentOutlet = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchOutlets = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/outlets')
      outlets.value = response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch outlets'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchOutlet = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(`/outlets/${id}`)
      currentOutlet.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch outlet'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createOutlet = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/outlets', data)
      outlets.value.unshift(response.data)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create outlet'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateOutlet = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.put(`/outlets/${id}`, data)
      const index = outlets.value.findIndex(o => o.id === id)
      if (index !== -1) {
        outlets.value[index] = response.data
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update outlet'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteOutlet = async (id) => {
    loading.value = true
    error.value = null
    try {
      await api.delete(`/outlets/${id}`)
      outlets.value = outlets.value.filter(o => o.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete outlet'
      throw err
    } finally {
      loading.value = false
    }
  }

  const generateQrCodes = async (outletId, tableCount) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post(`/outlets/${outletId}/generate-qr`, {
        table_count: tableCount
      })
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to generate QR codes'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    outlets,
    currentOutlet,
    loading,
    error,
    fetchOutlets,
    fetchOutlet,
    createOutlet,
    updateOutlet,
    deleteOutlet,
    generateQrCodes
  }
})
