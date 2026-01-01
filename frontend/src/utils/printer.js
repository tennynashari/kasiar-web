// ESC/POS Thermal Printer Integration
export class ThermalPrinter {
  constructor() {
    this.device = null
    this.connected = false
  }

  // Connect to USB Thermal Printer via WebUSB
  async connect() {
    try {
      // Request USB device
      this.device = await navigator.usb.requestDevice({
        filters: [
          { vendorId: 0x0416 }, // Common thermal printer vendor
          { vendorId: 0x04b8 }, // Epson
          { vendorId: 0x1504 }  // Other thermal printers
        ]
      })

      await this.device.open()
      await this.device.selectConfiguration(1)
      await this.device.claimInterface(0)
      
      this.connected = true
      return true
    } catch (error) {
      console.error('Printer connection failed:', error)
      return false
    }
  }

  // Disconnect from printer
  async disconnect() {
    if (this.device) {
      try {
        await this.device.close()
        this.connected = false
      } catch (error) {
        console.error('Disconnect error:', error)
      }
    }
  }

  // Print receipt
  async printReceipt(transaction) {
    if (!this.connected) {
      throw new Error('Printer not connected')
    }

    const commands = this.buildReceiptCommands(transaction)
    
    try {
      await this.device.transferOut(1, commands)
      return true
    } catch (error) {
      console.error('Print error:', error)
      throw error
    }
  }

  // Build ESC/POS commands for receipt
  buildReceiptCommands(transaction) {
    const ESC = 0x1b
    const GS = 0x1d
    const LF = 0x0a
    
    let commands = []
    
    // Initialize printer
    commands.push(ESC, 0x40)
    
    // Center align
    commands.push(ESC, 0x61, 0x01)
    
    // Store name (large font)
    commands.push(ESC, 0x21, 0x30) // Double height & width
    commands.push(...this.textToBytes(transaction.outlet.name))
    commands.push(LF)
    
    // Normal font
    commands.push(ESC, 0x21, 0x00)
    commands.push(...this.textToBytes(transaction.outlet.address || ''))
    commands.push(LF)
    commands.push(...this.textToBytes(transaction.outlet.phone || ''))
    commands.push(LF, LF)
    
    // Left align
    commands.push(ESC, 0x61, 0x00)
    
    // Transaction info
    commands.push(...this.textToBytes('No: ' + transaction.transaction_no))
    commands.push(LF)
    commands.push(...this.textToBytes('Tanggal: ' + new Date(transaction.created_at).toLocaleString('id-ID')))
    commands.push(LF)
    commands.push(...this.textToBytes('Kasir: ' + transaction.user.name))
    commands.push(LF)
    commands.push(...this.textToBytes('-'.repeat(48)))
    commands.push(LF)
    
    // Items
    transaction.items.forEach(item => {
      commands.push(...this.textToBytes(item.product_name))
      commands.push(LF)
      commands.push(...this.textToBytes(`  ${item.quantity} x ${this.formatCurrency(item.price)}`))
      commands.push(...this.textToBytes(this.formatCurrency(item.subtotal).padStart(20)))
      commands.push(LF)
    })
    
    commands.push(...this.textToBytes('-'.repeat(48)))
    commands.push(LF)
    
    // Totals
    commands.push(...this.textToBytes('Subtotal:'.padEnd(30) + this.formatCurrency(transaction.subtotal).padStart(18)))
    commands.push(LF)
    
    if (transaction.discount > 0) {
      commands.push(...this.textToBytes('Diskon:'.padEnd(30) + this.formatCurrency(transaction.discount).padStart(18)))
      commands.push(LF)
    }
    
    // Total (bold)
    commands.push(ESC, 0x21, 0x08) // Bold
    commands.push(...this.textToBytes('TOTAL:'.padEnd(30) + this.formatCurrency(transaction.total).padStart(18)))
    commands.push(LF)
    
    // Normal font
    commands.push(ESC, 0x21, 0x00)
    
    commands.push(...this.textToBytes('Bayar:'.padEnd(30) + this.formatCurrency(transaction.paid_amount).padStart(18)))
    commands.push(LF)
    commands.push(...this.textToBytes('Kembali:'.padEnd(30) + this.formatCurrency(transaction.change_amount).padStart(18)))
    commands.push(LF, LF)
    
    // Center align
    commands.push(ESC, 0x61, 0x01)
    commands.push(...this.textToBytes('Terima Kasih'))
    commands.push(LF, LF, LF)
    
    // Cut paper
    commands.push(GS, 0x56, 0x00)
    
    return new Uint8Array(commands)
  }

  // Helper: Convert text to bytes
  textToBytes(text) {
    return new TextEncoder().encode(text)
  }

  // Helper: Format currency
  formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount)
  }

  // Open cash drawer (connected to printer)
  async openCashDrawer() {
    if (!this.connected) {
      throw new Error('Printer not connected')
    }

    // ESC/POS command to open cash drawer
    const command = new Uint8Array([0x1b, 0x70, 0x00, 0x19, 0xfa])
    
    try {
      await this.device.transferOut(1, command)
      return true
    } catch (error) {
      console.error('Cash drawer error:', error)
      throw error
    }
  }
}

// Alternative: Print using browser's print dialog
export function printReceiptBrowser(transaction) {
  const printWindow = window.open('', '_blank')
  
  const html = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Receipt - ${transaction.transaction_no}</title>
      <style>
        @media print {
          @page { margin: 0; size: 80mm auto; }
          body { margin: 0; padding: 10mm; }
        }
        body {
          font-family: 'Courier New', monospace;
          font-size: 12px;
          width: 80mm;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 18px; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; }
        .right { text-align: right; }
      </style>
    </head>
    <body>
      <div class="center large bold">${transaction.outlet.name}</div>
      <div class="center">${transaction.outlet.address || ''}</div>
      <div class="center">${transaction.outlet.phone || ''}</div>
      <div class="line"></div>
      <div>No: ${transaction.transaction_no}</div>
      <div>Tanggal: ${new Date(transaction.created_at).toLocaleString('id-ID')}</div>
      <div>Kasir: ${transaction.user.name}</div>
      <div class="line"></div>
      ${transaction.items.map(item => `
        <div>${item.product_name}</div>
        <table>
          <tr>
            <td>${item.quantity} x ${formatCurrency(item.price)}</td>
            <td class="right">${formatCurrency(item.subtotal)}</td>
          </tr>
        </table>
      `).join('')}
      <div class="line"></div>
      <table>
        <tr>
          <td>Subtotal:</td>
          <td class="right">${formatCurrency(transaction.subtotal)}</td>
        </tr>
        ${transaction.discount > 0 ? `
        <tr>
          <td>Diskon:</td>
          <td class="right">${formatCurrency(transaction.discount)}</td>
        </tr>
        ` : ''}
        <tr class="bold">
          <td>TOTAL:</td>
          <td class="right">${formatCurrency(transaction.total)}</td>
        </tr>
        <tr>
          <td>Bayar:</td>
          <td class="right">${formatCurrency(transaction.paid_amount)}</td>
        </tr>
        <tr>
          <td>Kembali:</td>
          <td class="right">${formatCurrency(transaction.change_amount)}</td>
        </tr>
      </table>
      <div class="line"></div>
      <div class="center">Terima Kasih</div>
      <script>
        window.onload = function() {
          window.print()
          setTimeout(() => window.close(), 100)
        }
      </script>
    </body>
    </html>
  `
  
  printWindow.document.write(html)
  printWindow.document.close()
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}
