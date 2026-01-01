# Instalasi Frontend (Vue.js)

## Prerequisites
- Node.js 18.x atau lebih baru
- NPM atau Yarn

## Langkah-langkah Instalasi

### 1. Install Dependencies
```bash
cd frontend
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
```

Isi file `.env`:
```
VITE_API_URL=http://localhost:8000/api
```

### 3. Jalankan Development Server
```bash
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`

### 4. Build untuk Production
```bash
npm run build
```

File build akan ada di folder `dist/`

## Login
Buka browser ke `http://localhost:5173`

Gunakan kredensial:
- **Owner**: owner@kasir.app / password
- **Kasir**: kasir@kasir.app / password
- **Supervisor**: supervisor@kasir.app / password

## Fitur Utama

### 1. Dashboard
- Statistik hari ini (omzet, transaksi, rata-rata)
- Produk terlaris
- Stok menipis
- Chart penjualan

### 2. POS Kasir
- Scan barcode produk (USB scanner)
- Cari produk manual
- Filter by kategori
- Keranjang belanja
- Multi metode pembayaran
- Print struk

### 3. Manajemen Produk
- CRUD produk
- Generate barcode otomatis
- Track stok
- Kategori produk
- Search & filter

### 4. Riwayat Transaksi
- List semua transaksi
- Filter by tanggal, payment method
- Detail transaksi
- Print ulang struk

### 5. Laporan (Owner/Supervisor)
- Laporan penjualan (harian, mingguan, bulanan)
- Export Excel
- Revenue, discount, average analysis

## Hardware Integration

### Barcode Scanner
1. Colokkan USB barcode scanner
2. Scanner akan otomatis terdeteksi sebagai keyboard input
3. Di halaman POS, scan barcode akan otomatis mencari produk

### Thermal Printer (ESC/POS)
Ada 2 mode:

#### Mode 1: Browser Print (Default)
- Menggunakan browser print dialog
- Kompatibel dengan semua printer
- Tidak perlu driver khusus

#### Mode 2: Direct USB Print (Advanced)
- Menggunakan WebUSB API
- Langsung ke thermal printer
- Support auto cut & cash drawer
- Perlu enable WebUSB di Chrome/Edge

### Cash Drawer
- Cash drawer terhubung ke thermal printer
- Otomatis terbuka saat pembayaran tunai
- Manual open via printer command

## Offline Mode

Aplikasi support offline mode:
- Data produk & kategori di-cache di IndexedDB
- Transaksi disimpan local saat offline
- Auto sync saat online kembali

## Troubleshooting

### Port sudah digunakan
Edit `vite.config.js` dan ubah port:
```js
server: {
  port: 3000 // ubah ke port lain
}
```

### API tidak terkoneksi
Pastikan:
- Backend Laravel sudah running
- URL di `.env` sudah benar
- CORS sudah dikonfigurasi di backend

### Error saat npm install
Coba:
```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

### Barcode scanner tidak berfungsi
- Pastikan scanner dalam mode USB HID (keyboard emulation)
- Test scanner di Notepad, harus bisa ketik
- Fokus cursor harus di halaman (tidak di input)

## Development Tips

### Hot Reload
Vite support hot module replacement. Edit file Vue akan auto reload.

### Vue DevTools
Install Vue DevTools extension untuk debugging:
- Chrome: Vue.js devtools
- Firefox: Vue.js devtools

### API Testing
Frontend sudah include axios interceptor untuk:
- Auto attach bearer token
- Auto redirect ke login jika 401
- Error handling

### Component Structure
```
src/
├── views/          # Halaman utama
├── components/     # Reusable components
├── stores/         # Pinia stores (state management)
├── services/       # API services
├── utils/          # Helper functions
├── router/         # Vue Router config
└── assets/         # CSS, images
```
