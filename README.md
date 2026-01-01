# Unified POS - Aplikasi Kasir Web

Aplikasi kasir berbasis web untuk toko retail, minimarket, dan F&B dengan fitur lengkap.

## 🎯 Fitur Utama

### Phase 1 - MVP (✅ Complete)
- ✅ **POS Core** - Sistem kasir dengan keranjang belanja, multi pembayaran
- ✅ **Manajemen Produk** - CRUD produk, kategori, stok management
- ✅ **Barcode Integration** - Scan barcode USB/Bluetooth, generate barcode EAN-13
- ✅ **Printer Support** - Print struk thermal ESC/POS, print barcode label
- ✅ **Cash Drawer** - Auto open saat pembayaran tunai
- ✅ **Dashboard** - Real-time stats, sales chart, top products
- ✅ **Laporan** - Sales report (harian/mingguan/bulanan), export Excel
- ✅ **Multi User** - Role: Owner, Supervisor, Kasir, Kitchen
- ✅ **Offline Mode** - Tetap bisa transaksi saat internet mati
- ✅ **Multi Outlet** - Support banyak outlet/toko

### Phase 2 - F&B Features (Planned)
- 🔲 QR Order - Customer order via QR code
- 🔲 Kitchen Display - Real-time order kitchen
- 🔲 Table Management - Manajemen meja
- 🔲 Order Tracking - Track order status

### Phase 3 - Advanced (Planned)
- 🔲 Online Payment - QRIS API, e-wallet integration
- 🔲 Loyalty Program - Point & reward system
- 🔲 Inventory Resep - Recipe & ingredient management
- 🔲 Multi Payment - Split payment methods

## 🛠️ Tech Stack

- **Frontend**: Vue.js 3 + Vite + Tailwind CSS + Pinia
- **Backend**: Laravel 11 + MySQL + Sanctum Auth
- **Offline**: IndexedDB (Dexie.js)
- **Hardware**: ESC/POS Printer, USB Barcode Scanner, Cash Drawer

## 📁 Struktur Project

```
kasir-web/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/   # API Controllers
│   │   └── Models/             # Eloquent Models
│   ├── database/
│   │   ├── migrations/         # Database schema
│   │   └── seeders/            # Sample data
│   ├── routes/api.php          # API routes
│   └── setup.bat/sh            # Auto setup script
│
├── frontend/                   # Vue.js SPA
│   ├── src/
│   │   ├── views/              # Pages
│   │   ├── components/         # Vue components
│   │   ├── stores/             # Pinia stores
│   │   ├── services/           # API services
│   │   ├── utils/              # Helpers (barcode, printer)
│   │   └── router/             # Vue router
│   └── setup.bat/sh            # Auto setup script
│
├── API_DOCUMENTATION.md        # API docs
├── INSTALL_BACKEND.md          # Backend setup guide
├── INSTALL_FRONTEND.md         # Frontend setup guide
├── HARDWARE_DEPLOYMENT.md      # Hardware integration guide
└── README.md                   # This file
```

## 🚀 Quick Start

### Cara Tercepat (Windows)

#### 1. Setup Backend
```bash
cd backend
setup.bat
```
Script akan otomatis:
- Install dependencies
- Copy .env file
- Generate app key
- Run migrations & seeders

#### 2. Setup Frontend
```bash
cd frontend
setup.bat
```
Script akan otomatis:
- Install dependencies
- Copy .env file
- Configure API URL

#### 3. Jalankan Aplikasi
Terminal 1 (Backend):
```bash
cd backend
php artisan serve
```

Terminal 2 (Frontend):
```bash
cd frontend
npm run dev
```

Buka browser: `http://localhost:5173`

### Manual Installation

Lihat dokumentasi lengkap:
- [Backend Installation](INSTALL_BACKEND.md)
- [Frontend Installation](INSTALL_FRONTEND.md)

## 🔐 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@kasir.app | password |
| Supervisor | supervisor@kasir.app | password |
| Kasir | kasir@kasir.app | password |
| Kitchen | kitchen@kasir.app | password |

## 📱 Screenshots

### Dashboard
- Real-time omzet hari ini
- Jumlah transaksi & rata-rata
- Chart penjualan 7 hari
- Top products & low stock alert

### POS Kasir
- Scan barcode atau cari produk
- Filter by kategori
- Keranjang belanja
- Multi metode pembayaran (Cash, QRIS, Transfer, E-Wallet)
- Print struk otomatis

### Manajemen Produk
- CRUD produk dengan kategori
- Generate barcode EAN-13 otomatis
- Track stok (optional untuk F&B)
- Harga modal & harga jual
- Print barcode label

### Riwayat Transaksi
- List semua transaksi
- Filter by tanggal & payment method
- Detail transaksi & items
- Print ulang struk

### Laporan (Owner/Supervisor)
- Sales report harian/mingguan/bulanan
- Total revenue, discount, average
- Export Excel

## 🖨️ Hardware Support

### Barcode Scanner
- **USB HID Scanner** (Plug & play)
- **Bluetooth Scanner**
- **2D QR Scanner** (untuk F&B QR order)
- Format: EAN-13, Code-128, QR Code

### Thermal Printer
- **Epson TM-T82, TM-T88**
- **Xprinter XP-58, XP-80**
- **Iware IP-80**
- Semua printer ESC/POS compatible
- Support: USB, Network (LAN), Bluetooth

### Barcode Label Printer
- **Zebra GK420d**
- **TSC TTP-244 Pro**
- **Xprinter XP-365B**
- Label size: 40x30mm, 50x30mm, 100x50mm

### Cash Drawer
- Connect via RJ-11 ke thermal printer
- Auto open saat bayar tunai
- Manual open (owner/supervisor)
- Audit log pembukaan drawer

**📖 Detail Hardware Setup**: [HARDWARE_DEPLOYMENT.md](HARDWARE_DEPLOYMENT.md)

## 🌐 API Documentation

Base URL: `http://localhost:8000/api`

### Auth
- `POST /login` - Login
- `POST /logout` - Logout
- `GET /me` - Current user

### Products
- `GET /products` - List products
- `POST /products` - Create product
- `PUT /products/{id}` - Update product
- `DELETE /products/{id}` - Delete product
- `POST /products/find-barcode` - Find by barcode
- `POST /products/{id}/generate-barcode` - Generate barcode

### Transactions
- `GET /transactions` - List transactions
- `POST /transactions` - Create transaction (checkout)
- `GET /transactions/{id}` - Transaction detail
- `POST /transactions/{id}/void` - Void transaction

### Dashboard & Reports
- `GET /dashboard` - Dashboard stats
- `GET /reports/sales` - Sales report

**📖 Complete API Docs**: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## 🔌 Offline Mode

Aplikasi tetap bisa digunakan saat internet mati:

1. **Data di-cache** - Produk & kategori tersimpan di IndexedDB
2. **Transaksi offline** - Disimpan local, auto sync saat online
3. **Auto sync** - Retry setiap 30 detik
4. **Indicator** - Status online/offline di UI

**Limitation**:
- Dashboard/report perlu online
- User management perlu online
- Multi-outlet sync perlu online

## 📊 Database Schema

### Main Tables
- `outlets` - Outlet/toko (support multi outlet)
- `users` - Users dengan role-based access
- `categories` - Kategori produk
- `products` - Master produk (SKU, barcode, harga, stok)
- `transactions` - Header transaksi
- `transaction_items` - Detail item transaksi
- `cash_flows` - Arus kas masuk/keluar
- `activity_logs` - Audit log aktivitas
- `tables` - Meja (untuk F&B Phase 2)

## 🔒 Security Features

- ✅ Token-based authentication (Laravel Sanctum)
- ✅ Role-based access control (RBAC)
- ✅ Password hashing (bcrypt)
- ✅ CSRF protection
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Activity logging & audit trail
- ✅ Rate limiting

## 🚢 Deployment

### Option 1: Local Server (Toko)
- PC/Mini PC + Windows/Linux
- XAMPP/Laragon (all-in-one)
- UPS untuk backup power
- **Best for**: Single outlet

### Option 2: Cloud Hosting
- DigitalOcean, AWS, Vultr
- Ubuntu + LEMP stack
- SSL/HTTPS
- **Best for**: Multi outlet, remote access

### Option 3: Shared Hosting
- cPanel hosting dengan Laravel support
- **Best for**: Budget friendly

**📖 Deployment Guide**: [HARDWARE_DEPLOYMENT.md](HARDWARE_DEPLOYMENT.md)

## 🧪 Testing

```bash
# Backend
cd backend
php artisan test

# Frontend
cd frontend
npm run test
```

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📝 License

MIT License - Feel free to use for commercial projects

## 🆘 Support

Issues? Questions?
- 📧 Email: support@unifiedpos.com
- 📖 Docs: Lihat folder docs/
- 🐛 Bug Report: Create GitHub issue

## 🗺️ Roadmap

### Version 1.0 (Current) ✅
- [x] POS Core
- [x] Product Management
- [x] Barcode & Printer
- [x] Dashboard & Reports
- [x] Offline Mode

### Version 1.1 (Q1 2025)
- [ ] QR Order F&B
- [ ] Kitchen Display
- [ ] Table Management
- [ ] Multi-language (EN, ID)

### Version 1.2 (Q2 2025)
- [ ] QRIS Payment Integration
- [ ] Loyalty Program
- [ ] Mobile App (iOS/Android)
- [ ] Inventory Management

### Version 2.0 (Q3 2025)
- [ ] Recipe Management
- [ ] Purchase Order
- [ ] Supplier Management
- [ ] Multi-warehouse

## 👨‍💻 Author

Built with ❤️ for Indonesian small businesses

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js
- Tailwind CSS
- All open source contributors
