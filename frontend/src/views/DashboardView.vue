<template>
  <div>
    <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Dashboard</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4 sm:mb-6">
      <div class="card p-3 sm:p-6">
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Omzet Hari Ini</div>
        <div class="text-base sm:text-2xl font-bold text-primary-600">
          {{ formatCurrency(stats?.today?.total_revenue || 0) }}
        </div>
      </div>

      <div class="card p-3 sm:p-6">
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Jumlah Transaksi</div>
        <div class="text-base sm:text-2xl font-bold text-green-600">
          {{ stats?.today?.total_transactions || 0 }}
        </div>
      </div>

      <div class="card p-3 sm:p-6">
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Rata-rata Transaksi</div>
        <div class="text-base sm:text-2xl font-bold text-blue-600">
          {{ formatCurrency(stats?.today?.average_transaction || 0) }}
        </div>
      </div>

      <div class="card p-3 sm:p-6">
        <div class="text-xs sm:text-sm text-gray-600 mb-1">Kas Hari Ini</div>
        <div class="text-base sm:text-2xl font-bold text-purple-600">
          {{ formatCurrency(stats?.today?.cash_in_hand || 0) }}
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
      <!-- Top Products -->
      <div class="card">
        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Produk Terlaris</h3>
        <div class="space-y-2 sm:space-y-3">
          <div 
            v-for="product in stats?.top_products?.slice(0, 5)" 
            :key="product.id"
            class="flex justify-between items-center p-2 sm:p-3 bg-gray-50 rounded"
          >
            <div class="flex-1 min-w-0">
              <div class="font-medium text-sm sm:text-base truncate">{{ product.name }}</div>
              <div class="text-xs sm:text-sm text-gray-600">{{ product.total_quantity }} terjual</div>
            </div>
            <div class="text-right flex-shrink-0 ml-2">
              <div class="font-semibold text-primary-600 text-sm sm:text-base">
                {{ formatCurrency(product.total_revenue) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Low Stock -->
      <div class="card">
        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Stok Menipis</h3>
        <div class="space-y-2 sm:space-y-3">
          <div 
            v-for="product in stats?.low_stock_products" 
            :key="product.id"
            class="flex justify-between items-center p-2 sm:p-3 bg-red-50 rounded"
          >
            <div class="flex-1 min-w-0">
              <div class="font-medium text-sm sm:text-base truncate">{{ product.name }}</div>
              <div class="text-xs sm:text-sm text-gray-600">{{ product.category?.name }}</div>
            </div>
            <div class="text-right flex-shrink-0 ml-2">
              <div class="font-semibold text-red-600 text-sm sm:text-base">
                Stok: {{ product.stock }}
              </div>
              <div class="text-xs text-gray-600">Min: {{ product.min_stock }}</div>
            </div>
          </div>
          <div v-if="!stats?.low_stock_products?.length" class="text-center text-gray-500 py-4 text-sm">
            Semua produk stok aman
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'

const dashboardStore = useDashboardStore()
const stats = ref(null)

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

onMounted(async () => {
  try {
    stats.value = await dashboardStore.fetchDashboard()
  } catch (error) {
    console.error('Failed to load dashboard:', error)
  }
})
</script>
