<template>
  <div class="barcode-container" v-if="product">
    <div class="barcode-label">
      <!-- Product Name -->
      <div class="product-name">{{ product.name }}</div>
      
      <!-- Barcode -->
      <div class="barcode-wrapper">
        <svg ref="barcodeEl" class="barcode"></svg>
      </div>
      
      <!-- Barcode Number -->
      <div class="barcode-number">{{ product.barcode }}</div>
      
      <!-- Price -->
      <div class="product-price">{{ formatCurrency(product.selling_price) }}</div>
      
      <!-- SKU -->
      <div class="product-sku">SKU: {{ product.sku }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import JsBarcode from 'jsbarcode'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const barcodeEl = ref(null)

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount || 0)
}

const generateBarcode = async () => {
  await nextTick()
  
  if (barcodeEl.value && props.product?.barcode) {
    try {
      console.log('Generating barcode for:', props.product.barcode)
      
      JsBarcode(barcodeEl.value, props.product.barcode, {
        format: 'CODE128',
        width: 2,
        height: 50,
        displayValue: false,
        margin: 5,
        background: '#ffffff',
        lineColor: '#000000'
      })
      
      console.log('Barcode generated successfully')
    } catch (error) {
      console.error('Error generating barcode:', error)
      console.error('Barcode value:', props.product.barcode)
    }
  } else {
    console.warn('Cannot generate barcode - element or barcode value missing', {
      element: !!barcodeEl.value,
      barcode: props.product?.barcode
    })
  }
}

onMounted(() => {
  setTimeout(() => {
    console.log('Mounted - Product:', props.product)
    generateBarcode()
  }, 500)
})

watch(() => props.product, () => {
  generateBarcode()
}, { deep: true })
</script>

<style scoped>
.barcode-container {
  display: none;
}

@media print {
  /* Set ukuran kertas sesuai label */
  @page {
    size: 50mm 30mm;
    margin: 0;
  }
  
  body * {
    visibility: hidden;
  }
  
  .barcode-container,
  .barcode-container * {
    visibility: visible;
  }
  
  .barcode-container {
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
  }
  
  .barcode-label {
    width: 100%;
    height: 100%;
    padding: 2mm;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    page-break-after: always;
    box-sizing: border-box;
  }
  
  .product-name {
    font-size: 8pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 2mm;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  .barcode-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 1mm 0;
  }
  
  .barcode {
    max-width: 100%;
  }
  
  .barcode-number {
    font-size: 7pt;
    font-family: 'Courier New', monospace;
    margin: 1mm 0;
  }
  
  .product-price {
    font-size: 10pt;
    font-weight: bold;
    margin: 1mm 0;
  }
  
  .product-sku {
    font-size: 6pt;
    color: #666;
    margin-top: 1mm;
  }
}
</style>
