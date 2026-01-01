// Offline Database using Dexie (IndexedDB wrapper)
import Dexie from 'dexie'

class OfflineDatabase extends Dexie {
  constructor() {
    super('UnifiedPOS')
    
    this.version(1).stores({
      products: 'id, sku, barcode, name, category_id',
      categories: 'id, name',
      transactions: '++id, transaction_no, outlet_id, synced, created_at',
      transactionItems: '++id, transaction_id, product_id',
      settings: 'key'
    })
  }

  // Sync products from server
  async syncProducts(products) {
    await this.products.clear()
    await this.products.bulkAdd(products)
  }

  // Sync categories from server
  async syncCategories(categories) {
    await this.categories.clear()
    await this.categories.bulkAdd(categories)
  }

  // Save transaction offline
  async saveTransactionOffline(transaction) {
    const transactionId = await this.transactions.add({
      ...transaction,
      synced: false,
      created_at: new Date()
    })

    await this.transactionItems.bulkAdd(
      transaction.items.map(item => ({
        ...item,
        transaction_id: transactionId
      }))
    )

    return transactionId
  }

  // Get unsynced transactions
  async getUnsyncedTransactions() {
    return await this.transactions
      .where('synced')
      .equals(0)
      .toArray()
  }

  // Mark transaction as synced
  async markSynced(transactionId) {
    await this.transactions.update(transactionId, { synced: true })
  }

  // Get products with search
  async searchProducts(query) {
    if (!query) {
      return await this.products.toArray()
    }

    const lowerQuery = query.toLowerCase()
    return await this.products
      .filter(p =>
        p.name.toLowerCase().includes(lowerQuery) ||
        p.sku.toLowerCase().includes(lowerQuery) ||
        p.barcode?.toLowerCase().includes(lowerQuery)
      )
      .toArray()
  }

  // Find product by barcode
  async findByBarcode(barcode) {
    return await this.products
      .where('barcode')
      .equals(barcode)
      .first()
  }
}

export const db = new OfflineDatabase()

// Sync service
export class SyncService {
  constructor(api) {
    this.api = api
    this.syncing = false
  }

  // Check if online
  isOnline() {
    return navigator.onLine
  }

  // Sync products and categories when online
  async syncMasterData() {
    if (!this.isOnline()) {
      console.log('Offline: Cannot sync master data')
      return false
    }

    try {
      const [products, categories] = await Promise.all([
        this.api.get('/products?per_page=1000'),
        this.api.get('/categories')
      ])

      await db.syncProducts(products.data.data)
      await db.syncCategories(categories.data)

      console.log('Master data synced successfully')
      return true
    } catch (error) {
      console.error('Sync master data failed:', error)
      return false
    }
  }

  // Sync pending transactions
  async syncTransactions() {
    if (!this.isOnline() || this.syncing) {
      return
    }

    this.syncing = true

    try {
      const unsyncedTransactions = await db.getUnsyncedTransactions()

      for (const transaction of unsyncedTransactions) {
        try {
          await this.api.post('/transactions', transaction)
          await db.markSynced(transaction.id)
          console.log(`Transaction ${transaction.transaction_no} synced`)
        } catch (error) {
          console.error(`Failed to sync transaction ${transaction.transaction_no}:`, error)
        }
      }
    } finally {
      this.syncing = false
    }
  }

  // Start auto sync (every 30 seconds)
  startAutoSync() {
    setInterval(() => {
      if (this.isOnline()) {
        this.syncTransactions()
      }
    }, 30000)

    // Sync on online event
    window.addEventListener('online', () => {
      console.log('Back online - syncing...')
      this.syncTransactions()
    })
  }
}
