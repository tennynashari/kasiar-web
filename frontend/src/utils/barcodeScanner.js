// Barcode Scanner Integration
export class BarcodeScanner {
  constructor() {
    this.buffer = ''
    this.timeout = null
    this.onScan = null
  }

  init(callback) {
    this.onScan = callback
    
    document.addEventListener('keypress', (e) => {
      // Barcode scanner typically sends rapid key presses
      if (this.timeout) {
        clearTimeout(this.timeout)
      }

      // Ignore if typing in input/textarea
      if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
        return
      }

      this.buffer += e.key

      this.timeout = setTimeout(() => {
        if (this.buffer.length >= 3) { // Minimum barcode length
          if (this.onScan) {
            this.onScan(this.buffer)
          }
        }
        this.buffer = ''
      }, 100) // 100ms timeout between characters
    })
  }

  destroy() {
    if (this.timeout) {
      clearTimeout(this.timeout)
    }
    this.buffer = ''
    this.onScan = null
  }
}

// Usage example:
// const scanner = new BarcodeScanner()
// scanner.init((barcode) => {
//   console.log('Scanned:', barcode)
//   // Search product by barcode
// })
