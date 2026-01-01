# Quick Reference Guide - Unified POS

## 🎯 Cheat Sheet untuk Pengguna

### Login Credentials (Default)

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| Owner | owner@kasir.app | password | Full access semua fitur |
| Supervisor | supervisor@kasir.app | password | Dashboard, laporan, monitoring |
| Kasir | kasir@kasir.app | password | POS kasir, transaksi |
| Kitchen | kitchen@kasir.app | password | Kitchen display (F&B) |

### Keyboard Shortcuts

#### Global
- `F2` - Fokus ke search box
- `F9` - Quick checkout
- `Esc` - Cancel/Close modal
- `Ctrl + P` - Print struk

#### POS Kasir
- `Enter` - Tambah ke keranjang
- `+` / `-` - Tambah/Kurang quantity
- `Delete` - Hapus item
- `F12` - Clear keranjang

### Workflow Standar

#### 1. Transaksi Retail/Minimarket
```
1. Login sebagai Kasir
2. Buka menu POS
3. Scan barcode atau cari produk
4. Produk otomatis masuk keranjang
5. Set diskon (optional)
6. Pilih metode pembayaran
7. Input jumlah bayar
8. Klik "Bayar & Cetak"
9. Struk auto print
10. Cash drawer auto buka (jika tunai)
```

#### 2. Transaksi F&B (Tanpa QR)
```
1. Login sebagai Kasir
2. Buka menu POS
3. Pilih menu makanan/minuman
4. Tambah catatan khusus (pedas, dll)
5. Pilih meja (optional)
6. Checkout seperti biasa
7. Struk ke customer
8. Order ke dapur (manual/printer dapur)
```

#### 3. Tambah Produk Baru
```
1. Login sebagai Owner/Supervisor
2. Menu Produk → Tambah Produk
3. Isi: Nama, kategori, harga
4. Klik "Generate Barcode" (optional)
5. Print barcode label
6. Tempel di produk
7. Produk siap dijual
```

#### 4. Cek Laporan Harian
```
1. Login sebagai Owner/Supervisor
2. Menu Dashboard
3. Lihat stats hari ini:
   - Total omzet
   - Jumlah transaksi
   - Produk terlaris
   - Stok menipis
```

#### 5. Laporan Periodik
```
1. Menu Laporan
2. Pilih tanggal dari - sampai
3. Pilih group by (harian/mingguan/bulanan)
4. Klik "Generate Laporan"
5. Klik "Export Excel" untuk download
```

### Troubleshooting Cepat

#### ❌ Scanner tidak berfungsi
**Solusi:**
1. Check USB connection
2. Test scan di Notepad
3. Pastikan scanner mode: HID Keyboard
4. Restart browser

#### ❌ Printer tidak print
**Solusi:**
1. Check printer ON
2. Check paper ada
3. Test print dari Windows
4. Gunakan mode "Browser Print" sebagai fallback

#### ❌ Produk tidak ditemukan
**Solusi:**
1. Pastikan produk active
2. Check barcode sudah benar
3. Coba cari by nama/SKU
4. Sync offline data (jika offline mode)

#### ❌ Offline mode tidak sync
**Solusi:**
1. Check koneksi internet
2. Refresh halaman
3. Menu Settings → Manual Sync
4. Transaksi akan auto sync saat online

#### ❌ Cash drawer tidak buka
**Solusi:**
1. Check kabel RJ-11 ke printer
2. Check drawer tidak locked
3. Test manual command dari printer settings
4. Restart printer

### Tips & Tricks

#### 🎯 Mempercepat Transaksi
- Setup produk favorit di kategori pertama
- Gunakan barcode scanner untuk speed
- Shortcut: Scan → Enter → Bayar
- Setup quick amount buttons (25K, 50K, 100K)

#### 💰 Manajemen Kas
- Tutup shift: Menu Kasir → Tutup Shift
- Input kas awal shift
- Record pengeluaran (petty cash)
- Cek selisih kas otomatis

#### 📊 Optimasi Stok
- Set min stock alert untuk reorder
- Review produk terlaris weekly
- Archive produk slow moving
- Track gross profit margin

#### 🔒 Keamanan
- Ganti password default
- Logout setelah selesai shift
- Jangan share kredensial
- Enable activity log review

### Best Practices

#### Tutup Kasir (End of Day)
```
1. Cetak laporan shift
2. Hitung uang fisik
3. Cocokkan dengan system
4. Input selisih (jika ada)
5. Setor ke owner/supervisor
6. Logout
```

#### Maintenance Harian
```
- Check printer paper (pagi)
- Test scanner working
- Backup data (malam)
- Clear cash drawer (setelah setor)
```

#### Maintenance Mingguan
```
- Update stok produk
- Review low stock items
- Bersihkan printer head
- Check koneksi hardware
```

### Laporan yang Sering Digunakan

#### 1. Laporan Penjualan Harian
```
Menu: Laporan → Sales Report
Filter: Date = Hari ini
Group By: Day
```

#### 2. Top 10 Produk Terlaris
```
Menu: Dashboard
Section: Produk Terlaris
Period: Last 7 days
```

#### 3. Stok Menipis
```
Menu: Dashboard
Section: Stok Menipis
Alert: Stock <= Min Stock
```

#### 4. Kas Masuk/Keluar
```
Menu: Laporan Kas
Filter: Date range
Category: Semua
```

### Integrasi Hardware

#### Setup Barcode Scanner (First Time)
```
1. Colokkan USB scanner
2. Windows auto install driver
3. Test di Notepad: Scan barcode
4. Harus muncul angka + Enter
5. Buka aplikasi POS
6. Scan barcode → OK!
```

#### Setup Thermal Printer (First Time)
```
1. Install driver printer
2. Set as default printer di Windows
3. Test print dari Notepad
4. Di aplikasi: Settings → Printer
5. Select printer
6. Test print struk
```

#### Setup Cash Drawer
```
1. Colokkan RJ-11 ke printer port
2. Connect drawer power
3. Test open: Settings → Test Drawer
4. Konfigurasi auto open on cash payment
```

### Emergency Procedures

#### 🔴 Listrik Mati
```
1. Jangan panik
2. UPS akan backup (5-10 menit)
3. Selesaikan transaksi berjalan
4. Save & logout
5. Tutup aplikasi
6. Shutdown PC dengan benar
```

#### 🔴 Internet Mati
```
1. Aplikasi auto switch ke offline mode
2. Transaksi tetap bisa jalan
3. Data tersimpan local
4. Setelah online kembali: auto sync
5. Verify transaksi ter-sync semua
```

#### 🔴 Printer Error
```
1. Check paper jam
2. Restart printer
3. Use backup printer
4. Fallback: Print via browser
5. Manual write struk (worst case)
```

### Contacts

#### Technical Support
- Email: support@unifiedpos.com
- Phone: 0812-3456-7890
- WhatsApp: +62 812-3456-7890

#### Emergency Support (24/7)
- Critical issues only
- System down
- Data recovery
- Hardware malfunction

### Update & Maintenance

#### Cek Update
```
Menu: Settings → About
Current Version: 1.0.0
Check Updates: Auto
```

#### Backup Data
```
Automatic: Daily at 2 AM
Manual: Settings → Backup Now
Location: backup/ folder
Retention: 30 days
```

### FAQ Cepat

**Q: Boleh ganti harga saat kasir?**
A: Tidak. Harus dari menu Produk. Kasir hanya bisa diskon.

**Q: Bisa void transaksi?**
A: Bisa. Owner/Supervisor only. Menu Transaksi → Void.

**Q: Cara print ulang struk?**
A: Menu Transaksi → Detail → Print Ulang Struk.

**Q: Lupa password?**
A: Hubungi Owner untuk reset password.

**Q: Bisa multi outlet?**
A: Bisa. Setup di menu Outlets (Owner only).

**Q: Kapan stok berkurang?**
A: Otomatis saat transaksi selesai.

**Q: Bisa cancel di tengah transaksi?**
A: Bisa. Klik "Clear" atau tekan ESC.

---

## 📞 Need Help?

- 📖 [Full Documentation](README.md)
- 🔧 [Hardware Guide](HARDWARE_DEPLOYMENT.md)
- 🌐 [API Docs](API_DOCUMENTATION.md)
- 💬 [Support Forum](https://forum.unifiedpos.com)
