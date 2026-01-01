import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useProductStore = defineStore('product', () => {
  const products = ref([])
  const categories = ref([])
  const loading = ref(false)

  const fetchProducts = async (params = {}) => {
    loading.value = true
    try {
      const response = await api.get('/products', { params })
      products.value = response.data.data
      return response.data
    } catch (error) {
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      const response = await api.get('/categories')
      categories.value = response.data
      return response.data
    } catch (error) {
      throw error
    }
  }

  const findByBarcode = async (barcode) => {
    try {
      const response = await api.post('/products/find-barcode', { barcode })
      return response.data
    } catch (error) {
      throw error
    }
  }

  const createProduct = async (data) => {
    try {
      const config = data instanceof FormData ? {
        headers: { 'Content-Type': 'multipart/form-data' }
      } : {}
      
      const response = await api.post('/products', data, config)
      products.value.unshift(response.data)
      return response.data
    } catch (error) {
      throw error
    }
  }

  const updateProduct = async (id, data) => {
    try {
      const config = data instanceof FormData ? {
        headers: { 'Content-Type': 'multipart/form-data' }
      } : {}
      
      const response = await api.post(`/products/${id}`, data, config)
      const index = products.value.findIndex(p => p.id === id)
      if (index !== -1) {
        products.value[index] = response.data
      }
      return response.data
    } catch (error) {
      throw error
    }
  }

  const deleteProduct = async (id) => {
    try {
      await api.delete(`/products/${id}`)
      products.value = products.value.filter(p => p.id !== id)
    } catch (error) {
      throw error
    }
  }

  const addCategory = async (data) => {
    try {
      const response = await api.post('/categories', data)
      categories.value.push(response.data)
      return response.data
    } catch (error) {
      throw error
    }
  }

  const updateCategory = async (id, data) => {
    try {
      const response = await api.put(`/categories/${id}`, data)
      const index = categories.value.findIndex(c => c.id === id)
      if (index !== -1) {
        categories.value[index] = response.data
      }
      return response.data
    } catch (error) {
      throw error
    }
  }

  const deleteCategory = async (id) => {
    try {
      await api.delete(`/categories/${id}`)
      categories.value = categories.value.filter(c => c.id !== id)
    } catch (error) {
      throw error
    }
  }

  return {
    products,
    categories,
    loading,
    fetchProducts,
    fetchCategories,
    findByBarcode,
    createProduct,
    updateProduct,
    deleteProduct,
    addCategory,
    updateCategory,
    deleteCategory
  }
})
