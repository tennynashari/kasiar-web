<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Laporan Penjualan</h2>

    <!-- Filter -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="label">Tanggal Dari</label>
          <input v-model="dateFrom" type="date" class="input">
        </div>
        <div>
          <label class="label">Tanggal Sampai</label>
          <input v-model="dateTo" type="date" class="input">
        </div>
        <div>
          <label class="label">Group By</label>
          <select v-model="groupBy" class="input">
            <option value="day">Harian</option>
            <option value="week">Mingguan</option>
            <option value="month">Bulanan</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="loadReport" class="btn btn-primary w-full">
            Generate Laporan
          </button>
        </div>
      </div>
    </div>

    <!-- Report Table -->
    <div class="card">
      <h3 class="text-lg font-semibold mb-4">Ringkasan Penjualan</h3>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold">Periode</th>
              <th class="px-4 py-3 text-right text-sm font-semibold">Transaksi</th>
              <th class="px-4 py-3 text-right text-sm font-semibold">Total Penjualan</th>
              <th class="px-4 py-3 text-right text-sm font-semibold">Total Diskon</th>
              <th class="px-4 py-3 text-right text-sm font-semibold">Rata-rata</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="row in reportData" :key="row.period" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm">{{ row.period }}</td>
              <td class="px-4 py-3 text-sm text-right">{{ row.transaction_count }}</td>
              <td class="px-4 py-3 text-sm text-right font-semibold">
                {{ formatCurrency(row.total_revenue) }}
              </td>
              <td class="px-4 py-3 text-sm text-right text-red-600">
                {{ formatCurrency(row.total_discount) }}
              </td>
              <td class="px-4 py-3 text-sm text-right">
                {{ formatCurrency(row.average_transaction) }}
              </td>
            </tr>
          </tbody>
          <tfoot class="bg-gray-50 font-semibold">
            <tr>
              <td class="px-4 py-3 text-sm">Total</td>
              <td class="px-4 py-3 text-sm text-right">{{ totalTransactions }}</td>
              <td class="px-4 py-3 text-sm text-right">{{ formatCurrency(totalRevenue) }}</td>
              <td class="px-4 py-3 text-sm text-right text-red-600">{{ formatCurrency(totalDiscount) }}</td>
              <td class="px-4 py-3 text-sm text-right">{{ formatCurrency(avgTransaction) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div v-if="reportData.length === 0" class="text-center py-8 text-gray-500">
        Tidak ada data laporan
      </div>

      <div v-if="reportData.length > 0" class="mt-6">
        <button @click="exportExcel" class="btn btn-success">
          📥 Export Excel
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'

const dashboardStore = useDashboardStore()

const dateFrom = ref('')
const dateTo = ref('')
const groupBy = ref('day')
const reportData = ref([])

const totalTransactions = computed(() => {
  return reportData.value.reduce((sum, row) => sum + parseInt(row.transaction_count), 0)
})

const totalRevenue = computed(() => {
  return reportData.value.reduce((sum, row) => sum + parseFloat(row.total_revenue), 0)
})

const totalDiscount = computed(() => {
  return reportData.value.reduce((sum, row) => sum + parseFloat(row.total_discount), 0)
})

const avgTransaction = computed(() => {
  return totalTransactions.value > 0 ? totalRevenue.value / totalTransactions.value : 0
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

const loadReport = async () => {
  try {
    const data = await dashboardStore.fetchSalesReport({
      date_from: dateFrom.value,
      date_to: dateTo.value,
      group_by: groupBy.value
    })
    reportData.value = data
  } catch (error) {
    console.error('Failed to load report:', error)
  }
}

const exportExcel = () => {
  alert('Export Excel functionality will be implemented')
}

onMounted(() => {
  // Set default date range (last 7 days)
  const today = new Date()
  const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)
  
  dateFrom.value = lastWeek.toISOString().split('T')[0]
  dateTo.value = today.toISOString().split('T')[0]
  
  loadReport()
})
</script>
