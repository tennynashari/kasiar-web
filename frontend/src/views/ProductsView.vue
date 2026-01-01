<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 sm:mb-6">
      <h2 class="text-xl sm:text-2xl font-bold">Manajemen Produk</h2>
      <div class="flex gap-2 w-full sm:w-auto">
        <button @click="showCategoryModal = true" class="btn btn-secondary flex-1 sm:flex-none text-sm sm:text-base">
          + Kategori
        </button>
        <button @click="showAddModal = true" class="btn btn-primary flex-1 sm:flex-none text-sm sm:text-base">
          + Tambah Produk
        </button>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4 sm:mb-6">
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
        <input
          v-model="searchQuery"
          type="text"
          class="input flex-1 text-sm sm:text-base"
          placeholder="Cari produk..."
        >
        <select v-model="filterCategory" class="input sm:w-48 text-sm sm:text-base">
          <option value="">Semua Kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <button @click="loadProducts" class="btn btn-primary text-sm sm:text-base">
          Cari
        </button>
      </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block card overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-semibold">SKU</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Barcode</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Nama Produk</th>
            <th class="px-4 py-3 text-left text-sm font-semibold">Kategori</th>
            <th class="px-4 py-3 text-right text-sm font-semibold">Harga Modal</th>
            <th class="px-4 py-3 text-right text-sm font-semibold">Harga Jual</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Stok</th>
            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm">{{ product.sku }}</td>
            <td class="px-4 py-3 text-sm font-mono">{{ product.barcode || '-' }}</td>
            <td class="px-4 py-3 text-sm font-medium">{{ product.name }}</td>
            <td class="px-4 py-3 text-sm">{{ product.category?.name }}</td>
            <td class="px-4 py-3 text-sm text-right">{{ formatCurrency(product.cost_price) }}</td>
            <td class="px-4 py-3 text-sm text-right font-semibold">{{ formatCurrency(product.selling_price) }}</td>
            <td class="px-4 py-3 text-sm text-center">
              <span :class="product.stock <= product.min_stock ? 'text-red-600 font-bold' : ''">
                {{ product.track_stock ? product.stock : 'N/A' }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm text-center">
              <div class="flex items-center justify-center gap-2">
                <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-700 font-medium">
                  Edit
                </button>
                <button @click="printBarcode(product)" class="text-green-600 hover:text-green-700 font-medium">
                  Print Label
                </button>
                <button @click="deleteProduct(product)" class="text-red-600 hover:text-red-700 font-medium">
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="products.length === 0" class="text-center py-8 text-gray-500 text-sm">
        Tidak ada produk
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
      <div v-for="product in products" :key="product.id" class="card p-3">
        <div class="flex justify-between items-start mb-2">
          <div class="flex-1">
            <h3 class="font-semibold text-sm">{{ product.name }}</h3>
            <p class="text-xs text-gray-600">{{ product.category?.name }}</p>
          </div>
          <img v-if="product.image" :src="`http://localhost:8000/storage/${product.image}`" class="w-12 h-12 object-cover rounded ml-2">
        </div>
        
        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
          <div>
            <span class="text-gray-600">SKU:</span>
            <span class="font-medium ml-1">{{ product.sku }}</span>
          </div>
          <div>
            <span class="text-gray-600">Stok:</span>
            <span :class="['font-medium ml-1', product.stock <= product.min_stock ? 'text-red-600' : '']">
              {{ product.track_stock ? product.stock : 'N/A' }}
            </span>
          </div>
          <div>
            <span class="text-gray-600">Modal:</span>
            <span class="font-medium ml-1">{{ formatCurrency(product.cost_price) }}</span>
          </div>
          <div>
            <span class="text-gray-600">Jual:</span>
            <span class="font-semibold ml-1 text-primary-600">{{ formatCurrency(product.selling_price) }}</span>
          </div>
        </div>
        
        <div class="flex gap-2">
          <button @click="editProduct(product)" class="flex-1 py-2 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
            Edit
          </button>
          <button @click="printBarcode(product)" class="flex-1 py-2 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100">
            Label
          </button>
          <button @click="deleteProduct(product)" class="flex-1 py-2 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
            Hapus
          </button>
        </div>
      </div>
      
      <div v-if="products.length === 0" class="text-center py-8 text-gray-500 text-sm">
        Tidak ada produk
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto">
    <div class="card max-w-2xl w-full my-4 sm:my-8 max-h-[95vh] overflow-y-auto">
      <h3 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">{{ editingProduct ? 'Edit' : 'Tambah' }} Produk</h3>
      
      <form @submit.prevent="saveProduct" class="space-y-3 sm:space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
          <div>
            <label class="label text-xs sm:text-sm">Nama Produk *</label>
            <input v-model="form.name" type="text" class="input text-sm sm:text-base" required>
          </div>
          
          <div>
            <label class="label text-xs sm:text-sm">Kategori *</label>
            <select v-model.number="form.category_id" class="input text-sm sm:text-base" required>
              <option value="">Pilih Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="label text-xs sm:text-sm">SKU</label>
            <input v-model="form.sku" type="text" class="input text-sm sm:text-base" placeholder="Auto generate">
          </div>

          <div>
            <label class="label text-xs sm:text-sm">Barcode</label>
            <input v-model="form.barcode" type="text" class="input text-sm sm:text-base">
          </div>

          <div>
            <label class="label text-xs sm:text-sm">Harga Modal *</label>
            <input v-model.number="form.cost_price" type="number" class="input text-sm sm:text-base" required min="0">
          </div>

          <div>
            <label class="label text-xs sm:text-sm">Harga Jual *</label>
            <input v-model.number="form.selling_price" type="number" class="input text-sm sm:text-base" required min="0">
          </div>

          <div>
            <label class="label text-xs sm:text-sm">Stok</label>
            <input v-model.number="form.stock" type="number" class="input text-sm sm:text-base" min="0">
          </div>

          <div>
            <label class="label">Stok Minimum</label>
            <input v-model.number="form.min_stock" type="number" class="input" min="0">
          </div>
        </div>

        <div>
          <label class="label">Upload Gambar Produk</label>
          <input 
            type="file" 
            @change="handleImageUpload" 
            accept="image/*" 
            class="input"
          >
          <div v-if="imagePreview" class="mt-2">
            <img :src="imagePreview" alt="Preview" class="w-32 h-32 object-cover rounded">
          </div>
        </div>

        <div>
          <label class="flex items-center">
            <input v-model="form.track_stock" type="checkbox" class="mr-2">
            <span class="text-sm">Track Stok (uncheck untuk produk F&B)</span>
          </label>
        </div>

        <div>
          <label class="label">Deskripsi</label>
          <textarea v-model="form.description" class="input" rows="3"></textarea>
        </div>

        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary flex-1">
            Simpan
          </button>
          <button type="button" @click="closeModal" class="btn btn-secondary flex-1">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add/Edit Category Modal -->
  <div v-if="showCategoryModal" class="modal-overlay" @click.self="closeCategoryModal">
    <div class="modal-content max-w-4xl">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold">Kelola Kategori</h3>
        <button @click="closeCategoryModal" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form Add/Edit Category -->
        <div class="border-r pr-6">
          <h4 class="text-lg font-semibold mb-4">{{ editingCategory ? 'Edit' : 'Tambah' }} Kategori</h4>
          <form @submit.prevent="saveCategory">
            <div class="mb-4">
              <label class="label">Nama Kategori *</label>
              <input v-model="categoryForm.name" type="text" class="input" required>
            </div>

            <div class="mb-4">
              <label class="label">Slug *</label>
              <input v-model="categoryForm.slug" type="text" class="input" required placeholder="makanan-fnb">
              <p class="text-xs text-gray-500 mt-1">Gunakan huruf kecil dan tanda hubung (-)</p>
            </div>

            <div class="mb-4">
              <label class="label">Warna *</label>
              <div class="flex gap-2">
                <input v-model="categoryForm.color" type="color" class="h-10 w-20 rounded cursor-pointer" required>
                <input v-model="categoryForm.color" type="text" class="input flex-1" placeholder="#EF4444" required>
              </div>
            </div>

            <div class="flex gap-3">
              <button type="submit" class="btn btn-primary flex-1">
                {{ editingCategory ? 'Update' : 'Simpan' }}
              </button>
              <button v-if="editingCategory" type="button" @click="cancelEditCategory" class="btn btn-secondary flex-1">
                Batal
              </button>
            </div>
          </form>
        </div>

        <!-- List Categories -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Daftar Kategori</h4>
          <div class="space-y-2 max-h-96 overflow-y-auto">
            <div v-for="category in categories" :key="category.id" 
              class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
              <div class="flex items-center gap-3 flex-1">
                <div class="w-6 h-6 rounded" :style="{ backgroundColor: category.color }"></div>
                <div>
                  <div class="font-medium">{{ category.name }}</div>
                  <div class="text-xs text-gray-500">{{ category.slug }}</div>
                </div>
              </div>
              <div class="flex gap-2">
                <button @click="editCategory(category)" class="text-blue-600 hover:text-blue-700 text-sm">
                  Edit
                </button>
                <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-700 text-sm">
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Barcode Print Component (hidden, only for printing) -->
  <BarcodeLabelPrint v-if="printProduct" :product="printProduct" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useProductStore } from '@/stores/product'
import BarcodeLabelPrint from '@/components/BarcodeLabelPrint.vue'

const productStore = useProductStore()

const searchQuery = ref('')
const filterCategory = ref('')
const showAddModal = ref(false)
const showCategoryModal = ref(false)
const editingProduct = ref(null)
const editingCategory = ref(null)
const imageFile = ref(null)
const imagePreview = ref(null)
const printProduct = ref(null)

const form = ref({
  name: '',
  category_id: '',
  sku: '',
  barcode: '',
  description: '',
  cost_price: 0,
  selling_price: 0,
  stock: 1,
  min_stock: 10,
  track_stock: true,
  image: null
})

const categoryForm = ref({
  name: '',
  slug: '',
  color: '#3B82F6'
})

const products = computed(() => productStore.products)
const categories = computed(() => productStore.categories)

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}

const loadProducts = async () => {
  const params = {}
  if (searchQuery.value) params.search = searchQuery.value
  if (filterCategory.value) params.category_id = filterCategory.value
  
  await productStore.fetchProducts(params)
}

const editProduct = (product) => {
  editingProduct.value = product
  form.value = { ...product }
  imagePreview.value = product.image ? `http://localhost:8000/storage/${product.image}` : null
  showAddModal.value = true
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const saveProduct = async () => {
  try {
    const formData = new FormData()
    
    // Append all form fields with proper type conversion
    formData.append('name', form.value.name)
    formData.append('category_id', parseInt(form.value.category_id))
    
    if (form.value.sku) {
      formData.append('sku', form.value.sku)
    }
    
    if (form.value.barcode) {
      formData.append('barcode', form.value.barcode)
    }
    
    if (form.value.description) {
      formData.append('description', form.value.description)
    }
    
    formData.append('cost_price', parseFloat(form.value.cost_price) || 0)
    formData.append('selling_price', parseFloat(form.value.selling_price) || 0)
    formData.append('stock', parseInt(form.value.stock) || 0)
    formData.append('min_stock', parseInt(form.value.min_stock) || 10)
    formData.append('track_stock', form.value.track_stock ? '1' : '0')
    
    // Append image if exists
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }
    
    if (editingProduct.value) {
      formData.append('_method', 'PUT')
      await productStore.updateProduct(editingProduct.value.id, formData)
    } else {
      await productStore.createProduct(formData)
    }
    
    alert('Produk berhasil disimpan')
    closeModal()
    loadProducts()
  } catch (error) {
    console.error('Error saving product:', error)
    alert('Gagal menyimpan produk: ' + (error.response?.data?.message || error.message))
  }
}

const closeModal = () => {
  showAddModal.value = false
  editingProduct.value = null
  imageFile.value = null
  imagePreview.value = null
  form.value = {
    name: '',
    category_id: '',
    sku: '',
    barcode: '',
    description: '',
    cost_price: 0,
    selling_price: 0,
    stock: 1,
    min_stock: 10,
    track_stock: true,
    image: null
  }
}

const printBarcode = (product) => {
  if (!product.barcode) {
    alert('Produk tidak memiliki barcode. Silakan generate barcode terlebih dahulu.')
    return
  }
  
  console.log('Print barcode for product:', product)
  
  printProduct.value = product
  // Delay lebih lama untuk memastikan barcode ter-render
  setTimeout(() => {
    console.log('Opening print dialog...')
    window.print()
    // Reset setelah print
    setTimeout(() => {
      printProduct.value = null
    }, 500)
  }, 800)
}

const saveCategory = async () => {
  try {
    if (editingCategory.value) {
      await productStore.updateCategory(editingCategory.value.id, categoryForm.value)
      alert('Kategori berhasil diupdate!')
    } else {
      await productStore.addCategory(categoryForm.value)
      alert('Kategori berhasil ditambahkan!')
    }
    cancelEditCategory()
    await productStore.fetchCategories()
  } catch (error) {
    alert('Gagal menyimpan kategori: ' + (error.response?.data?.message || error.message))
  }
}

const editCategory = (category) => {
  editingCategory.value = category
  categoryForm.value = {
    name: category.name,
    slug: category.slug,
    color: category.color
  }
}

const cancelEditCategory = () => {
  editingCategory.value = null
  categoryForm.value = {
    name: '',
    slug: '',
    color: '#3B82F6'
  }
}

const deleteCategory = async (category) => {
  if (!confirm(`Hapus kategori "${category.name}"?`)) return
  
  try {
    await productStore.deleteCategory(category.id)
    alert('Kategori berhasil dihapus!')
    await productStore.fetchCategories()
  } catch (error) {
    alert('Gagal menghapus kategori: ' + (error.response?.data?.message || error.message))
  }
}

const closeCategoryModal = () => {
  showCategoryModal.value = false
  cancelEditCategory()
}

onMounted(async () => {
  await Promise.all([
    loadProducts(),
    productStore.fetchCategories()
  ])
})
</script>
