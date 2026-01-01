<template>
  <div class="receipt-container" v-if="transaction">
    <div class="receipt">
      <!-- Header -->
      <div class="receipt-header">
        <h2 class="store-name">{{ transaction.outlet?.name || 'Toko' }}</h2>
        <p class="store-address">{{ transaction.outlet?.address }}</p>
        <p class="store-phone">{{ transaction.outlet?.phone }}</p>
      </div>

      <div class="divider">==========================================</div>

      <!-- Transaction Info -->
      <div class="receipt-info">
        <div class="info-row">
          <span>No. Transaksi</span>
          <span>{{ transaction.transaction_no }}</span>
        </div>
        <div class="info-row">
          <span>Tanggal</span>
          <span>{{ formatDate(transaction.created_at) }}</span>
        </div>
        <div class="info-row">
          <span>Kasir</span>
          <span>{{ transaction.user?.name || 'Customer' }}</span>
        </div>
        <div class="info-row" v-if="transaction.table">
          <span>Meja</span>
          <span>{{ transaction.table.table_number }}</span>
        </div>
      </div>

      <div class="divider">==========================================</div>

      <!-- Items -->
      <table class="receipt-items">
        <thead>
          <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in transaction.items" :key="item.id">
            <td>{{ item.product_name }}</td>
            <td>{{ item.quantity }}</td>
            <td>{{ formatCurrency(item.price) }}</td>
            <td>{{ formatCurrency(item.subtotal) }}</td>
          </tr>
        </tbody>
      </table>

      <div class="divider">==========================================</div>

      <!-- Totals -->
      <div class="receipt-totals">
        <div class="total-row">
          <span>Subtotal</span>
          <span>{{ formatCurrency(transaction.subtotal) }}</span>
        </div>
        <div class="total-row" v-if="transaction.discount > 0">
          <span>Diskon</span>
          <span>-{{ formatCurrency(transaction.discount) }}</span>
        </div>
        <div class="total-row" v-if="transaction.tax > 0">
          <span>Pajak</span>
          <span>{{ formatCurrency(transaction.tax) }}</span>
        </div>
        <div class="total-row total-amount">
          <span>TOTAL</span>
          <span>{{ formatCurrency(transaction.total) }}</span>
        </div>
        <div class="total-row" v-if="transaction.paid_amount">
          <span>Dibayar</span>
          <span>{{ formatCurrency(transaction.paid_amount) }}</span>
        </div>
        <div class="total-row" v-if="transaction.change_amount">
          <span>Kembali</span>
          <span>{{ formatCurrency(transaction.change_amount) }}</span>
        </div>
      </div>

      <div class="divider">==========================================</div>

      <!-- Payment Method -->
      <div class="receipt-payment">
        <div class="info-row">
          <span>Metode Pembayaran</span>
          <span class="uppercase">{{ transaction.payment_method || '-' }}</span>
        </div>
      </div>

      <div class="divider">==========================================</div>

      <!-- Footer -->
      <div class="receipt-footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        <p class="powered-by">Powered by KasirWeb</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  transaction: {
    type: Object,
    required: true
  }
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount || 0)
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
</script>

<style scoped>
.receipt-container {
  display: none;
}

@media print {
  body * {
    visibility: hidden;
  }
  
  .receipt-container,
  .receipt-container * {
    visibility: visible;
  }
  
  .receipt-container {
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    width: 80mm;
    margin: 0;
    padding: 0;
  }
  
  .receipt {
    width: 80mm;
    font-family: 'Courier New', monospace;
    font-size: 10pt;
    padding: 5mm;
  }
  
  .receipt-header {
    text-align: center;
    margin-bottom: 10px;
  }
  
  .store-name {
    font-size: 14pt;
    font-weight: bold;
    margin: 0 0 5px 0;
  }
  
  .store-address,
  .store-phone {
    font-size: 9pt;
    margin: 2px 0;
  }
  
  .divider {
    margin: 8px 0;
    font-size: 8pt;
  }
  
  .receipt-info,
  .receipt-payment {
    margin: 8px 0;
  }
  
  .info-row {
    display: flex;
    justify-content: space-between;
    margin: 3px 0;
    font-size: 9pt;
  }
  
  .receipt-items {
    width: 100%;
    border-collapse: collapse;
    font-size: 9pt;
  }
  
  .receipt-items th {
    text-align: left;
    padding: 3px 0;
    border-bottom: 1px solid #000;
  }
  
  .receipt-items td {
    padding: 3px 0;
  }
  
  .receipt-items th:nth-child(2),
  .receipt-items td:nth-child(2),
  .receipt-items th:nth-child(3),
  .receipt-items td:nth-child(3),
  .receipt-items th:nth-child(4),
  .receipt-items td:nth-child(4) {
    text-align: right;
  }
  
  .receipt-totals {
    margin: 8px 0;
  }
  
  .total-row {
    display: flex;
    justify-content: space-between;
    margin: 3px 0;
    font-size: 10pt;
  }
  
  .total-amount {
    font-weight: bold;
    font-size: 12pt;
    margin-top: 5px;
    padding-top: 5px;
    border-top: 1px dashed #000;
  }
  
  .receipt-footer {
    text-align: center;
    margin-top: 10px;
    font-size: 9pt;
  }
  
  .receipt-footer p {
    margin: 3px 0;
  }
  
  .powered-by {
    margin-top: 8px;
    font-size: 8pt;
    font-style: italic;
  }
  
  .uppercase {
    text-transform: uppercase;
  }
}
</style>
