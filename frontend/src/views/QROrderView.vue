<template>
  <div class="min-h-screen bg-gray-50 pb-safe">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg sticky top-0 z-10">
      <div class="max-w-2xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
        <div class="text-center text-white">
          <h1 class="text-xl sm:text-2xl font-bold">{{ outlet?.name }}</h1>
          <p class="text-xs sm:text-sm text-blue-100 mt-1">📍 Meja {{ tableNumber }}</p>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600 text-sm">Memuat menu...</p>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="max-w-2xl mx-auto px-3 sm:px-4 py-6 sm:py-8">
      <div class="bg-red-50 border border-red-200 rounded-xl p-4 sm:p-6 text-center">
        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-base sm:text-lg font-semibold text-red-900 mb-2">Terjadi Kesalahan</h3>
        <p class="text-sm text-red-700">{{ error }}</p>
      </div>
    </div>

    <!-- Content -->
    <div v-else class="max-w-2xl mx-auto px-3 sm:px-4 py-4 sm:py-6 pb-28 sm:pb-32">
      <!-- Categories -->
      <div class="mb-4 sm:mb-6 -mx-3 sm:mx-0 px-3 sm:px-0">
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
          <button
            v-for="category in categories"
            :key="category.id"
            @click="selectedCategory = category.id"
            :class="[
              'px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex-shrink-0',
              selectedCategory === category.id
                ? 'bg-blue-600 text-white shadow-md'
                : 'bg-white text-gray-700 hover:bg-gray-100 shadow-sm'
            ]"
          >
            {{ category.name }}
          </button>
          <button
            @click="selectedCategory = null"
            :class="[
              'px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex-shrink-0',
              selectedCategory === null
                ? 'bg-blue-600 text-white shadow-md'
                : 'bg-white text-gray-700 hover:bg-gray-100 shadow-sm'
            ]"
          >
            Semua
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 sm:w-20 sm:h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <p class="text-gray-500 text-sm">Tidak ada menu tersedia</p>
      </div>

      <!-- Products -->
      <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
        >
          <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
            <img 
              v-if="product.image" 
              :src="`http://localhost:8000/storage/${product.image}`" 
              :alt="product.name"
              class="w-full h-full object-cover"
            >
            <svg v-else class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="p-2.5 sm:p-3">
            <h3 class="font-medium text-gray-900 text-xs sm:text-sm mb-1 line-clamp-2 min-h-[2.5rem] sm:min-h-[2.25rem]">
              {{ product.name }}
            </h3>
            <p class="text-blue-600 font-bold text-sm sm:text-base mb-2">
              Rp {{ formatNumber(product.selling_price) }}
            </p>
            <button
              @click="addToCart(product)"
              :disabled="product.stock <= 0 && product.track_stock"
              :class="[
                'w-full py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all active:scale-95',
                (product.stock > 0 || !product.track_stock)
                  ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'
                  : 'bg-gray-200 text-gray-400 cursor-not-allowed'
              ]"
            >
              {{ (product.stock > 0 || !product.track_stock) ? '+ Tambah' : 'Habis' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart Button -->
    <div
      v-if="cart.length > 0"
      class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-2xl z-40 safe-bottom"
    >
      <div class="max-w-2xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
        <button
          @click="showCart = true"
          class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 sm:py-3.5 rounded-xl font-bold flex items-center justify-between shadow-lg active:scale-95 transition-all"
        >
          <div class="flex items-center gap-2">
            <span class="bg-white text-blue-600 px-2.5 py-0.5 rounded-full text-xs font-bold">
              {{ cart.length }}
            </span>
            <span class="text-sm sm:text-base">Lihat Pesanan</span>
          </div>
          <span class="text-sm sm:text-base">Rp {{ formatNumber(totalPrice) }}</span>
        </button>
      </div>
    </div>

    <!-- Cart Modal -->
    <div
      v-if="showCart"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center sm:justify-center"
      @click.self="showCart = false"
    >
      <div class="bg-white w-full sm:max-w-lg sm:mx-4 rounded-t-2xl sm:rounded-2xl max-h-[85vh] sm:max-h-[90vh] overflow-hidden flex flex-col animate-slide-up">
        <!-- Header -->
        <div class="flex-shrink-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 sm:px-6 py-4">
          <div class="flex justify-between items-center">
            <div>
              <h2 class="text-lg sm:text-xl font-bold">🛒 Pesanan Anda</h2>
              <p class="text-xs sm:text-sm text-blue-100 mt-0.5">{{ cart.length }} item</p>
            </div>
            <button 
              @click="showCart = false" 
              class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-full transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-2 sm:space-y-3">
          <div
            v-for="item in cart"
            :key="item.product_id"
            class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl"
          >
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                {{ item.product_name }}
              </h3>
              <p class="text-xs sm:text-sm text-blue-600 font-medium">
                Rp {{ formatNumber(item.price) }}
              </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <button
                @click="decreaseQuantity(item)"
                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-colors active:scale-90"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4" />
                </svg>
              </button>
              <span class="w-8 sm:w-10 text-center font-bold text-sm sm:text-base">
                {{ item.quantity }}
              </span>
              <button
                @click="increaseQuantity(item)"
                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-colors active:scale-90"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex-shrink-0 bg-white border-t p-3 sm:p-4 space-y-3">
          <div class="bg-blue-50 p-3 rounded-xl">
            <div class="flex justify-between items-center">
              <span class="text-gray-700 font-medium text-sm sm:text-base">Total Pembayaran</span>
              <span class="text-blue-600 font-bold text-lg sm:text-xl">
                Rp {{ formatNumber(totalPrice) }}
              </span>
            </div>
          </div>
          
          <input
            v-model="customerName"
            type="text"
            placeholder="Nama Anda (opsional)"
            class="w-full px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl text-sm sm:text-base focus:border-blue-600 focus:outline-none transition-colors"
          />
          
          <textarea
            v-model="customerNote"
            placeholder="Catatan pesanan (opsional)"
            rows="2"
            class="w-full px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl text-sm sm:text-base focus:border-blue-600 focus:outline-none transition-colors resize-none"
          ></textarea>
          
          <button
            @click="submitOrder"
            :disabled="submitting"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 sm:py-3.5 rounded-xl font-bold text-sm sm:text-base disabled:from-gray-400 disabled:to-gray-400 disabled:cursor-not-allowed shadow-lg active:scale-95 transition-all"
          >
            {{ submitting ? 'Mengirim...' : 'Kirim Pesanan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div
      v-if="showSuccess"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-sm w-full text-center animate-scale-up">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
          <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">🎉 Pesanan Terkirim!</h3>
        <p class="text-sm sm:text-base text-gray-600 mb-6">
          Pesanan Anda sedang diproses.<br class="hidden sm:block">Mohon tunggu sebentar.
        </p>
        <button
          @click="resetOrder"
          class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 sm:py-3.5 rounded-xl font-bold text-sm sm:text-base shadow-lg active:scale-95 transition-all"
        >
          Pesan Lagi
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const outletId = route.params.outletId
const tableId = route.params.tableId
const tableNumber = ref(tableId)

const outlet = ref(null)
const categories = ref([])
const products = ref([])
const selectedCategory = ref(null)
const cart = ref([])
const customerName = ref('')
const customerNote = ref('')

const loading = ref(true)
const error = ref(null)
const showCart = ref(false)
const showSuccess = ref(false)
const submitting = ref(false)

const filteredProducts = computed(() => {
  if (!selectedCategory.value) return products.value
  return products.value.filter(p => p.category_id === selectedCategory.value)
})

const totalPrice = computed(() => {
  return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num)
}

const loadData = async () => {
  try {
    loading.value = true
    error.value = null

    // Load categories and products from public endpoints
    const [categoriesRes, productsRes] = await Promise.all([
      api.get('/public/categories'),
      api.get('/public/products', { params: { outlet_id: outletId, per_page: 100 } })
    ])

    // Filter categories: only Makanan FNB, Minuman FNB, Snack FNB
    const allowedCategories = ['makanan-fnb', 'minuman-fnb', 'snack-fnb']
    categories.value = categoriesRes.data.filter(cat => 
      allowedCategories.includes(cat.slug.toLowerCase())
    )
    
    // Get allowed category IDs
    const allowedCategoryIds = categories.value.map(c => c.id)
    
    // Handle paginated response and filter by allowed categories
    const productData = productsRes.data.data || productsRes.data
    products.value = productData.filter(p => {
      // Only show active products from allowed categories
      if (!p.is_active || !allowedCategoryIds.includes(p.category_id)) {
        return false
      }
      
      // If product tracks stock, check stock > 0
      // If product doesn't track stock (F&B), always show
      return !p.track_stock || p.stock > 0
    })

    // Set outlet name from first product or default
    if (products.value.length > 0) {
      outlet.value = { name: 'Warung Makan Sedap' }
    }
  } catch (err) {
    error.value = 'Gagal memuat menu. Silakan refresh halaman.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const addToCart = (product) => {
  const existingItem = cart.value.find(item => item.product_id === product.id)
  
  if (existingItem) {
    existingItem.quantity++
  } else {
    cart.value.push({
      product_id: product.id,
      product_name: product.name,
      price: product.selling_price,
      quantity: 1,
      discount: 0,
      notes: customerName.value
    })
  }
}

const increaseQuantity = (item) => {
  item.quantity++
}

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity--
  } else {
    cart.value = cart.value.filter(i => i.product_id !== item.product_id)
    if (cart.value.length === 0) {
      showCart.value = false
    }
  }
}

const submitOrder = async () => {
  try {
    submitting.value = true
    
    // Build notes from customer name and note
    let notes = 'QR Order'
    if (customerName.value) {
      notes += ` - ${customerName.value}`
    }
    if (customerNote.value) {
      notes += ` | Note: ${customerNote.value}`
    }
    
    console.log('Sending order:', {
      outlet_id: parseInt(outletId),
      table_id: parseInt(tableId),
      items: cart.value,
      discount: 0,
      tax: 0,
      payment_method: 'cash',
      paid_amount: 0,
      notes: notes
    })
    
    await api.post('/public/orders', {
      outlet_id: parseInt(outletId),
      table_id: parseInt(tableId),
      items: cart.value,
      discount: 0,
      tax: 0,
      payment_method: 'cash',
      paid_amount: 0, // Will be paid at cashier
      notes: notes
    })

    showCart.value = false
    showSuccess.value = true
  } catch (err) {
    console.error('Order error:', err)
    console.error('Error response:', err.response?.data)
    alert(`Gagal mengirim pesanan: ${err.response?.data?.message || 'Silakan coba lagi.'}`)
  } finally {
    submitting.value = false
  }
}

const resetOrder = () => {
  cart.value = []
  customerName.value = ''
  customerNote.value = ''
  showSuccess.value = false
}

onMounted(() => {
  loadData()
})
</script>
<style scoped>
/* Hide scrollbar but keep functionality */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Line clamp */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Safe area for mobile devices */
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}

.safe-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}

/* Animations */
@keyframes slide-up {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes scale-up {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slide-up 0.3s ease-out;
}

.animate-scale-up {
  animation: scale-up 0.3s ease-out;
}

/* Smooth transitions */
* {
  -webkit-tap-highlight-color: transparent;
}

button:active {
  transform: scale(0.98);
}

/* Better touch targets for mobile */
@media (max-width: 640px) {
  button {
    min-height: 44px;
  }
}
</style>