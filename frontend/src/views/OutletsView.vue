<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Outlet Management</h2>
      <button @click="openAddModal" class="btn-primary">
        + Tambah Outlet
      </button>
    </div>

    <!-- Outlets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="outlet in outlets" :key="outlet.id" class="card">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-lg font-bold">{{ outlet.name }}</h3>
            <span class="text-xs text-gray-500">{{ outlet.code }}</span>
          </div>
          <span :class="[
            'px-2 py-1 text-xs font-semibold rounded',
            outlet.business_type === 'retail' ? 'bg-blue-100 text-blue-800' :
            outlet.business_type === 'minimarket' ? 'bg-green-100 text-green-800' :
            'bg-orange-100 text-orange-800'
          ]">
            {{ getBusinessTypeLabel(outlet.business_type) }}
          </span>
        </div>

        <div class="space-y-2 text-sm text-gray-600 mb-4">
          <div class="flex items-start">
            <svg class="w-4 h-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ outlet.address }}</span>
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <span>{{ outlet.phone }}</span>
          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ outlet.users_count || 0 }} Users</span>
          </div>
        </div>

        <div v-if="outlet.enable_qr_order" class="mb-4 p-3 bg-green-50 rounded-lg">
          <div class="flex items-center text-sm text-green-800">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold">QR Order Enabled</span>
          </div>
          <button 
            @click="openQrModal(outlet)" 
            class="mt-2 text-sm text-green-700 hover:text-green-900 font-medium"
          >
            Generate QR Codes →
          </button>
        </div>

        <div class="flex gap-2">
          <button @click="editOutlet(outlet)" class="btn-secondary flex-1">
            Edit
          </button>
          <button @click="deleteOutletConfirm(outlet)" class="btn-danger flex-1">
            Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="card max-w-2xl w-full">
        <h3 class="text-xl font-bold mb-4">{{ editingOutlet ? 'Edit' : 'Tambah' }} Outlet</h3>
        
        <form @submit.prevent="saveOutlet" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="label">Nama Outlet *</label>
              <input v-model="form.name" type="text" class="input" required>
            </div>
            
            <div>
              <label class="label">Kode Outlet *</label>
              <input v-model="form.code" type="text" class="input" required placeholder="e.g. FNB001">
            </div>

            <div>
              <label class="label">Tipe Bisnis *</label>
              <select v-model="form.business_type" class="input" required>
                <option value="">Pilih Tipe</option>
                <option value="retail">Retail</option>
                <option value="minimarket">Minimarket</option>
                <option value="fnb">Food & Beverage</option>
              </select>
            </div>

            <div>
              <label class="label">Telepon *</label>
              <input v-model="form.phone" type="text" class="input" required>
            </div>
          </div>

          <div>
            <label class="label">Alamat *</label>
            <textarea v-model="form.address" class="input" rows="3" required></textarea>
          </div>

          <div>
            <label class="flex items-center">
              <input v-model="form.enable_qr_order" type="checkbox" class="mr-2">
              <span class="text-sm">Enable QR Order (untuk tipe F&B)</span>
            </label>
          </div>

          <div class="flex gap-2 justify-end">
            <button type="button" @click="closeModal" class="btn-secondary">
              Batal
            </button>
            <button type="submit" class="btn-primary">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- QR Code Modal -->
    <div v-if="showQrModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div class="card max-w-4xl w-full my-8">
        <h3 class="text-xl font-bold mb-4">Generate QR Codes - {{ selectedOutlet?.name }}</h3>
        
        <div v-if="!qrCodes" class="space-y-4">
          <div>
            <label class="label">Jumlah Meja</label>
            <input v-model.number="tableCount" type="number" class="input" min="1" max="100" placeholder="e.g. 10">
          </div>
          <div class="flex gap-2 justify-end">
            <button @click="closeQrModal" class="btn-secondary">Batal</button>
            <button @click="generateQr" class="btn-primary">Generate</button>
          </div>
        </div>

        <div v-else>
          <div class="mb-4 flex justify-end">
            <button @click="printQrCodes" class="btn-primary">
              🖨️ Print Semua
            </button>
          </div>
          
          <div id="qr-codes-container" class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto">
            <div v-for="qr in qrCodes" :key="qr.table_number" class="border rounded-lg p-4 text-center print-qr-item">
              <div class="font-bold text-lg mb-2">Meja {{ qr.table_number }}</div>
              <div class="bg-white p-2 inline-block">
                <canvas :id="`qr-${qr.table_number}`" class="mx-auto"></canvas>
              </div>
              <div class="text-xs text-gray-600 mt-2 break-all">{{ qr.url }}</div>
            </div>
          </div>
          
          <div class="mt-4 flex justify-end">
            <button @click="closeQrModal" class="btn-secondary">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useOutletStore } from '@/stores/outlet'
import QRCode from 'qrcode'

const outletStore = useOutletStore()

const showModal = ref(false)
const showQrModal = ref(false)
const editingOutlet = ref(null)
const selectedOutlet = ref(null)
const tableCount = ref(10)
const qrCodes = ref(null)

const form = ref({
  name: '',
  code: '',
  business_type: '',
  address: '',
  phone: '',
  enable_qr_order: false
})

const outlets = computed(() => outletStore.outlets)

const getBusinessTypeLabel = (type) => {
  const labels = {
    retail: 'Retail',
    minimarket: 'Minimarket',
    fnb: 'F&B'
  }
  return labels[type] || type
}

const openAddModal = () => {
  editingOutlet.value = null
  form.value = {
    name: '',
    code: '',
    business_type: '',
    address: '',
    phone: '',
    enable_qr_order: false
  }
  showModal.value = true
}

const editOutlet = (outlet) => {
  editingOutlet.value = outlet
  form.value = { ...outlet }
  showModal.value = true
}

const saveOutlet = async () => {
  try {
    if (editingOutlet.value) {
      await outletStore.updateOutlet(editingOutlet.value.id, form.value)
    } else {
      await outletStore.createOutlet(form.value)
    }
    alert('Outlet berhasil disimpan')
    closeModal()
  } catch (error) {
    alert('Gagal menyimpan outlet: ' + (error.response?.data?.message || error.message))
  }
}

const deleteOutletConfirm = async (outlet) => {
  if (!confirm(`Hapus outlet "${outlet.name}"?`)) return
  
  try {
    await outletStore.deleteOutlet(outlet.id)
    alert('Outlet berhasil dihapus')
  } catch (error) {
    alert('Gagal menghapus outlet: ' + (error.response?.data?.message || error.message))
  }
}

const closeModal = () => {
  showModal.value = false
  editingOutlet.value = null
}

const openQrModal = (outlet) => {
  selectedOutlet.value = outlet
  qrCodes.value = null
  tableCount.value = 10
  showQrModal.value = true
}

const generateQr = async () => {
  try {
    const response = await outletStore.generateQrCodes(selectedOutlet.value.id, tableCount.value)
    qrCodes.value = response.qr_codes
    
    // Generate QR codes after DOM update
    setTimeout(() => {
      qrCodes.value.forEach(qr => {
        const canvas = document.getElementById(`qr-${qr.table_number}`)
        if (canvas) {
          QRCode.toCanvas(canvas, qr.qr_data, { width: 150 })
        }
      })
    }, 100)
  } catch (error) {
    alert('Gagal generate QR codes')
  }
}

const printQrCodes = () => {
  window.print()
}

const closeQrModal = () => {
  showQrModal.value = false
  selectedOutlet.value = null
  qrCodes.value = null
}

onMounted(async () => {
  await outletStore.fetchOutlets()
})
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  #qr-codes-container,
  #qr-codes-container * {
    visibility: visible;
  }
  #qr-codes-container {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .print-qr-item {
    page-break-inside: avoid;
    break-inside: avoid;
  }
}
</style>
