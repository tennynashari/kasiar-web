<template>
  <div>
    <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Riwayat Transaksi</h2>

    <!-- Filter -->
    <div class="card mb-4 sm:mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
        <div>
          <label class="label text-xs sm:text-sm">Tanggal Dari</label>
          <input v-model="dateFrom" type="date" class="input text-sm">
        </div>
        <div>
          <label class="label text-xs sm:text-sm">Tanggal Sampai</label>
          <input v-model="dateTo" type="date" class="input text-sm">
        </div>
        <div>
          <label class="label text-xs sm:text-sm">Tipe Bisnis</label>
          <select v-model="businessType" class="input text-sm">
            <option value="">Semua</option>
            <option value="retail">Retail</option>
            <option value="minimarket">Minimarket</option>
            <option value="fnb">F&B</option>
          </select>
        </div>
        <div>
          <label class="label text-xs sm:text-sm">Metode Pembayaran</label>
          <select v-model="paymentMethod" class="input text-sm">
            <option value="">Semua</option>
            <option value="cash">Tunai</option>
            <option value="qris">QRIS</option>
            <option value="transfer">Transfer</option>
            <option value="ewallet">E-Wallet</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="loadTransactions" class="btn btn-primary w-full text-sm">
            Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold">No. Transaksi</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Tipe</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Kasir</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Pembayaran</th>
            <th class="px-4 py-3 text-right text-sm font-semibold">Total</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Status</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm font-mono">{{ transaction.transaction_no }}</td>
            <td class="px-4 py-3 text-sm">{{ formatDate(transaction.created_at) }}</td>
            <td class="px-4 py-3 text-sm">
              <span :class="[
                'px-2 py-1 rounded text-xs font-medium',
                transaction.business_type === 'retail' ? 'bg-blue-100 text-blue-700' :
                transaction.business_type === 'minimarket' ? 'bg-green-100 text-green-700' :
                'bg-orange-100 text-orange-700'
              ]">
                {{ getBusinessTypeLabel(transaction.business_type) }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm">{{ transaction.user?.name || 'Customer' }}</td>
            <td class="px-4 py-3 text-sm capitalize">{{ transaction.payment_method || '-' }}</td>
            <td class="px-4 py-3 text-sm text-right font-semibold">
              {{ formatCurrency(transaction.total) }}
            </td>
            <td class="px-4 py-3 text-sm text-center">
              <span
                class="px-2 py-1 rounded text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-700': transaction.status === 'completed',
                  'bg-red-100 text-red-700': transaction.status === 'void',
                  'bg-yellow-100 text-yellow-700': transaction.status === 'refund',
                  'bg-gray-100 text-gray-700': transaction.status === 'pending',
                  'bg-blue-100 text-blue-700': transaction.status === 'processed',
                  'bg-purple-100 text-purple-700': transaction.status === 'delivered'
                }"
              >
                {{ transaction.status }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm text-center">
              <div class="flex items-center justify-center gap-2">
                <button @click="viewDetail(transaction)" class="text-blue-600 hover:text-blue-700 font-medium">
                  Detail
                </button>
                <button 
                  v-if="transaction.business_type === 'fnb' && ['pending', 'processed', 'delivered'].includes(transaction.status)"
                  @click="showStatusModal(transaction)" 
                  class="text-green-600 hover:text-green-700 font-medium"
                >
                  Ubah Status
                </button>
                <button @click="deleteTransaction(transaction)" class="text-red-600 hover:text-red-700 font-medium">
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="transactions.length === 0" class="text-center py-8 text-gray-500 text-sm">
        Tidak ada transaksi
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
      <div v-for="transaction in transactions" :key="transaction.id" class="card p-3">
        <div class="flex justify-between items-start mb-2">
          <div>
            <div class="font-mono text-xs font-semibold text-gray-900">{{ transaction.transaction_no }}</div>
            <div class="text-xs text-gray-600">{{ formatDate(transaction.created_at) }}</div>
          </div>
          <span :class="[
            'px-2 py-0.5 rounded text-xs font-medium',
            transaction.business_type === 'retail' ? 'bg-blue-100 text-blue-700' :
            transaction.business_type === 'minimarket' ? 'bg-green-100 text-green-700' :
            'bg-orange-100 text-orange-700'
          ]">
            {{ getBusinessTypeLabel(transaction.business_type) }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
          <div>
            <span class="text-gray-600">Kasir:</span>
            <span class="font-medium ml-1">{{ transaction.user?.name || 'Customer' }}</span>
          </div>
          <div>
            <span class="text-gray-600">Pembayaran:</span>
            <span class="font-medium ml-1 capitalize">{{ transaction.payment_method || '-' }}</span>
          </div>
        </div>

        <!-- Notes Preview for Mobile -->
        <div v-if="transaction.notes" class="text-xs text-gray-600 mb-2 bg-blue-50 p-2 rounded">
          📝 {{ transaction.notes.length > 50 ? transaction.notes.substring(0, 50) + '...' : transaction.notes }}
        </div>

        <div class="flex justify-between items-center pt-3 border-t">
          <div>
            <div class="text-xs text-gray-600">Total</div>
            <div class="text-base font-bold text-primary-600">{{ formatCurrency(transaction.total) }}</div>
          </div>
          <div class="flex items-center gap-2">
            <span :class="[
              'px-2 py-1 rounded text-xs font-medium',
              transaction.status === 'completed' ? 'bg-green-100 text-green-700' :
              transaction.status === 'void' ? 'bg-red-100 text-red-700' :
              transaction.status === 'refund' ? 'bg-yellow-100 text-yellow-700' :
              transaction.status === 'processed' ? 'bg-blue-100 text-blue-700' :
              transaction.status === 'delivered' ? 'bg-purple-100 text-purple-700' :
              'bg-gray-100 text-gray-700'
            ]">
              {{ transaction.status }}
            </span>
            <button 
              v-if="transaction.business_type === 'fnb' && ['pending', 'processed', 'delivered'].includes(transaction.status)"
              @click="showStatusModal(transaction)" 
              class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-lg hover:bg-green-100"
            >
              Ubah
            </button>
            <button @click="viewDetail(transaction)" class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg hover:bg-blue-100">
              Detail
            </button>
            <button @click="deleteTransaction(transaction)" class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-lg hover:bg-red-100">
              Hapus
            </button>
          </div>
        </div>
      </div>

      <div v-if="transactions.length === 0" class="text-center py-8 text-gray-500 text-sm">
        Tidak ada transaksi
      </div>
    </div>

    <!-- Detail Modal -->
  <div v-if="showDetail && selectedTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="card max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h3 class="text-xl font-bold">Detail Transaksi</h3>
          <p class="text-sm text-gray-600">{{ selectedTransaction.transaction_no }}</p>
        </div>
        <button @click="showDetail = false" class="text-gray-500 hover:text-gray-700 text-2xl">
          ×
        </button>
      </div>

      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <div class="text-gray-600">Tanggal</div>
            <div class="font-medium">{{ formatDate(selectedTransaction.created_at) }}</div>
          </div>
          <div>
            <div class="text-gray-600">Kasir</div>
            <div class="font-medium">{{ selectedTransaction.user?.name || 'Customer' }}</div>
          </div>
          <div>
            <div class="text-gray-600">Metode Pembayaran</div>
            <div class="font-medium capitalize">{{ selectedTransaction.payment_method || '-' }}</div>
          </div>
          <div>
            <div class="text-gray-600">Status</div>
            <div class="font-medium capitalize">{{ selectedTransaction.status }}</div>
          </div>
        </div>

        <!-- Notes Section -->
        <div v-if="selectedTransaction.notes" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
          <div class="text-xs text-blue-600 font-semibold mb-1">Catatan:</div>
          <div class="text-sm text-gray-700">{{ selectedTransaction.notes }}</div>
        </div>

        <div class="border-t pt-4">
          <h4 class="font-semibold mb-3">Item Transaksi</h4>
          <div class="space-y-2">
            <div
              v-for="item in selectedTransaction.items"
              :key="item.id"
              class="flex justify-between text-sm"
            >
              <div>
                <div class="font-medium">{{ item.product_name }}</div>
                <div class="text-gray-600">
                  {{ formatCurrency(item.price) }} x {{ item.quantity }}
                </div>
              </div>
              <div class="text-right font-medium">
                {{ formatCurrency(item.subtotal) }}
              </div>
            </div>
          </div>
        </div>

        <div class="border-t pt-4 space-y-2">
          <div class="flex justify-between text-sm">
            <span>Subtotal</span>
            <span>{{ formatCurrency(selectedTransaction.subtotal) }}</span>
          </div>
          <div v-if="selectedTransaction.discount > 0" class="flex justify-between text-sm">
            <span>Diskon</span>
            <span class="text-red-600">-{{ formatCurrency(selectedTransaction.discount) }}</span>
          </div>
          <div v-if="selectedTransaction.tax > 0" class="flex justify-between text-sm">
            <span>Pajak</span>
            <span>{{ formatCurrency(selectedTransaction.tax) }}</span>
          </div>
          <div class="flex justify-between font-bold text-lg border-t pt-2">
            <span>Total</span>
            <span class="text-primary-600">{{ formatCurrency(selectedTransaction.total) }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span>Dibayar</span>
            <span>{{ formatCurrency(selectedTransaction.paid_amount) }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span>Kembalian</span>
            <span class="text-green-600">{{ formatCurrency(selectedTransaction.change_amount) }}</span>
          </div>
        </div>

        <div class="border-t pt-4">
          <button @click="printReceipt" class="btn btn-primary w-full">
            Print Ulang Struk
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Modal for F&B -->
  <div v-if="showStatusUpdate && statusTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="card max-w-md w-full">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h3 class="text-xl font-bold">Ubah Status Pesanan</h3>
          <p class="text-sm text-gray-600">{{ statusTransaction.transaction_no }}</p>
        </div>
        <button @click="showStatusUpdate = false" class="text-gray-500 hover:text-gray-700 text-2xl">
          ×
        </button>
      </div>

      <div class="space-y-4">
        <div>
          <label class="label">Status Saat Ini</label>
          <div class="font-medium text-lg capitalize">{{ statusTransaction.status }}</div>
        </div>

        <div>
          <label class="label">Ubah ke Status</label>
          <select v-model="newStatus" class="input">
            <option value="pending">Pending (Menunggu)</option>
            <option value="processed">Processed (Diproses)</option>
            <option value="delivered">Delivered (Diantar)</option>
            <option value="completed">Completed (Selesai)</option>
          </select>
        </div>

        <div class="flex gap-3">
          <button @click="showStatusUpdate = false" class="btn btn-secondary flex-1">
            Batal
          </button>
          <button @click="updateStatus" class="btn btn-primary flex-1">
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>
  </div>

  <!-- Receipt Print Component (hidden, only for printing) -->
  <ReceiptPrint v-if="printTransaction" :transaction="printTransaction" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import ReceiptPrint from '@/components/ReceiptPrint.vue'

const transactions = ref([])
const dateFrom = ref('')
const dateTo = ref('')
const businessType = ref('')
const paymentMethod = ref('')
const showDetail = ref(false)
const selectedTransaction = ref(null)
const showStatusUpdate = ref(false)
const statusTransaction = ref(null)
const newStatus = ref('')
const printTransaction = ref(null)

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getBusinessTypeLabel = (type) => {
  const labels = {
    retail: 'Retail',
    minimarket: 'Minimarket',
    fnb: 'F&B'
  }
  return labels[type] || type
}

const loadTransactions = async () => {
  try {
    const params = {}
    if (dateFrom.value) params.date_from = dateFrom.value
    if (dateTo.value) params.date_to = dateTo.value
    if (businessType.value) params.business_type = businessType.value
    if (paymentMethod.value) params.payment_method = paymentMethod.value

    const response = await api.get('/transactions', { params })
    transactions.value = response.data.data
  } catch (error) {
    console.error('Failed to load transactions:', error)
  }
}

const viewDetail = async (transaction) => {
  try {
    const response = await api.get(`/transactions/${transaction.id}`)
    selectedTransaction.value = response.data
    showDetail.value = true
  } catch (error) {
    console.error('Failed to load transaction detail:', error)
  }
}

const printReceipt = () => {
  printTransaction.value = selectedTransaction.value
  setTimeout(() => {
    window.print()
    printTransaction.value = null
  }, 100)
}

const showStatusModal = (transaction) => {
  statusTransaction.value = transaction
  newStatus.value = transaction.status
  showStatusUpdate.value = true
}

const updateStatus = async () => {
  try {
    await api.put(`/transactions/${statusTransaction.value.id}`, {
      status: newStatus.value
    })
    
    showStatusUpdate.value = false
    statusTransaction.value = null
    await loadTransactions()
    alert('Status berhasil diubah')
  } catch (error) {
    console.error('Failed to update status:', error)
    alert('Gagal mengubah status')
  }
}

const deleteTransaction = async (transaction) => {
  if (!confirm(`Hapus transaksi ${transaction.transaction_no}?`)) {
    return
  }

  try {
    await api.delete(`/transactions/${transaction.id}`)
    await loadTransactions()
    alert('Transaksi berhasil dihapus')
  } catch (error) {
    console.error('Failed to delete transaction:', error)
    alert('Gagal menghapus transaksi')
  }
}

onMounted(() => {
  // Set default date range (today)
  const today = new Date().toISOString().split('T')[0]
  dateFrom.value = today
  dateTo.value = today
  
  loadTransactions()
})
</script>
