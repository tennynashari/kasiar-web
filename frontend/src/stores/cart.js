import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useAuthStore } from './auth'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const discount = ref(0)
  const tax = ref(0)

  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => {
      return sum + (item.price * item.quantity - (item.discount || 0))
    }, 0)
  })

  const total = computed(() => {
    return subtotal.value - discount.value + tax.value
  })

  const itemCount = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const addItem = (product, quantity = 1) => {
    const existingItem = items.value.find(item => item.product_id === product.id)
    
    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      items.value.push({
        product_id: product.id,
        product_name: product.name,
        price: product.selling_price,
        quantity: quantity,
        discount: 0,
        notes: ''
      })
    }
  }

  const updateItem = (productId, updates) => {
    const item = items.value.find(item => item.product_id === productId)
    if (item) {
      Object.assign(item, updates)
    }
  }

  const removeItem = (productId) => {
    items.value = items.value.filter(item => item.product_id !== productId)
  }

  const setDiscount = (amount) => {
    discount.value = amount
  }

  const setTax = (amount) => {
    tax.value = amount
  }

  const clear = () => {
    items.value = []
    discount.value = 0
    tax.value = 0
  }

  const checkout = async (paymentData) => {
    const authStore = useAuthStore()
    
    // Get outlet_id from user, or use default outlet if Owner (outlet_id is null)
    const outletId = authStore.user.outlet_id || 1 // Default to first outlet
    
    try {
      const response = await api.post('/transactions', {
        outlet_id: outletId,
        items: items.value,
        discount: discount.value,
        tax: tax.value,
        ...paymentData
      })
      
      clear()
      return response.data
    } catch (error) {
      throw error
    }
  }

  return {
    items,
    discount,
    tax,
    subtotal,
    total,
    itemCount,
    addItem,
    updateItem,
    removeItem,
    setDiscount,
    setTax,
    clear,
    checkout
  }
})
