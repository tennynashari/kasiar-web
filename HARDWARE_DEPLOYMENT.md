# Hardware Integration Guide

## Barcode Scanner

### Supported Types
1. **USB HID Scanner** (Recommended)
   - Plug & play, no driver needed
   - Acts as keyboard input
   - Compatible dengan Windows, macOS, Linux
   
2. **Bluetooth Scanner**
   - Pairing via Bluetooth settings
   - Also acts as keyboard input
   
3. **2D QR/Barcode Scanner**
   - Support EAN-13, Code-128, QR Code
   - Untuk F&B QR order

### Setup USB Scanner
1. Colokkan scanner ke USB port
2. Windows akan auto-detect sebagai HID keyboard
3. Test di Notepad - scan barcode harus muncul text
4. Di aplikasi POS, fokus di search box atau scan langsung
5. Scanner akan input barcode + Enter

### Barcode Format
Aplikasi support:
- **EAN-13**: 13 digit (2000000000016)
- **Code-128**: Variable length
- Custom format dari printer barcode

### Generate Barcode
Di aplikasi:
1. Menu Produk → Detail Produk
2. Klik "Generate Barcode"
3. System auto generate EAN-13
4. Print barcode label via printer barcode

---

## Thermal Printer (ESC/POS)

### Recommended Printers
- Epson TM-T82
- Xprinter XP-58
- Iware IP-80
- Any ESC/POS compatible printer (80mm or 58mm)

### Connection Types

#### 1. USB Connection (Recommended)
**Pros:** Reliable, fast, support cash drawer
**Cons:** Need USB cable

Setup:
1. Install printer driver dari CD atau website
2. Set as default printer di Windows
3. Di aplikasi, pilih "Browser Print" mode
4. Struk akan print via browser print dialog

#### 2. Network/LAN Connection
**Pros:** Wireless, multi device
**Cons:** Need network setup

Setup:
1. Connect printer ke WiFi/LAN
2. Set static IP address
3. Install network printer di Windows
4. Test print dari Notepad

#### 3. Bluetooth Connection
**Pros:** Wireless, portable
**Cons:** Limited range, slower

Setup:
1. Pair printer via Bluetooth settings
2. Install Bluetooth printer driver
3. Use as default printer

### Print Modes

#### Mode 1: Browser Print (Default)
```javascript
// Otomatis via browser
window.print()
```
- Compatible semua printer
- Muncul dialog preview
- User bisa pilih printer
- Support multiple printers

#### Mode 2: Direct USB (Advanced)
```javascript
// Via WebUSB API
const printer = new ThermalPrinter()
await printer.connect()
await printer.printReceipt(transaction)
```
- Langsung ke printer tanpa dialog
- Support auto cut paper
- Support cash drawer trigger
- Perlu Chrome/Edge browser
- Perlu enable WebUSB flag

### Enable WebUSB (Chrome/Edge)
1. Buka `chrome://flags`
2. Cari "WebUSB"
3. Enable experimental web platform features
4. Restart browser

### Print ESC/POS Commands
```
ESC @ - Initialize
ESC a n - Align (0=left, 1=center, 2=right)
ESC ! n - Font style (bold, double height, etc)
ESC d n - Feed n lines
GS V - Cut paper
ESC p - Open cash drawer
```

---

## Barcode Label Printer

### Recommended Printers
- Zebra GK420d
- TSC TTP-244 Pro
- HPRT HT100
- Xprinter XP-365B

### Label Sizes
- 40mm x 30mm (small products)
- 50mm x 30mm (medium)
- 100mm x 50mm (large, with product info)

### Setup
1. Install printer driver
2. Set label size di printer properties
3. Print test page
4. Adjust print density

### Print Barcode Label
Di aplikasi:
1. Menu Produk
2. Pilih produk → Print Barcode
3. Pilih quantity & label size
4. Print

Format label:
```
[Product Name]
SKU: SKU-000001
[========BARCODE========]
2000000000016
Rp 25.000
```

---

## Cash Drawer

### Connection
Cash drawer connect ke thermal printer via RJ-11/RJ-12 port.

### Supported Triggers
1. **Auto open** - Saat pembayaran tunai selesai
2. **Manual open** - Owner/supervisor bisa buka manual
3. **ESC/POS command** - Via printer command

### ESC/POS Command
```
ESC p m t1 t2
0x1B 0x70 0x00 0x19 0xFA

m = Drawer pin (0 or 1)
t1 = ON time (0x19 = 25ms * 10 = 250ms)
t2 = OFF time (0xFA = 250ms * 10 = 2500ms)
```

### Security
- Log setiap pembukaan drawer
- Track by user & timestamp
- Alert jika buka di luar transaksi

---

## Network Setup (Multi Device)

### Scenario: 1 Backend, Multiple POS Terminals

#### Server (Backend)
1. Install di PC/Server dengan MySQL
2. Set static IP: `192.168.1.100`
3. Buka firewall port 8000
4. Jalankan: `php artisan serve --host=0.0.0.0 --port=8000`

#### Client (POS Terminal)
1. Install frontend only
2. Edit `.env`: `VITE_API_URL=http://192.168.1.100:8000/api`
3. Build & deploy: `npm run build`
4. Serve via nginx/apache atau `npm run preview`

#### Alternative: All-in-One PC
- Backend & Frontend di PC yang sama
- Printer via USB
- Scanner via USB
- No network needed

---

## Offline Mode

### How It Works
1. **Online**: Data sync dari server ke IndexedDB
2. **Offline**: Baca data dari IndexedDB, simpan transaksi local
3. **Back Online**: Auto sync transaksi ke server

### Sync Strategy
- **Master data** (produk, kategori): Sync setiap 1 jam atau manual
- **Transactions**: Sync real-time jika online, queue jika offline
- **Auto retry**: Retry failed sync setiap 30 detik

### Data Stored Locally
- Products (name, price, barcode, stock)
- Categories
- Pending transactions
- User session

### Limitations
- Report/dashboard perlu online
- Multi-outlet sync perlu online
- User management perlu online

---

## Production Deployment

### Option 1: Local Server (Recommended untuk Toko)
**Hardware:**
- PC/Mini PC dengan Windows/Linux
- Min: Intel i3, 4GB RAM, 120GB SSD
- UPS untuk backup power

**Software:**
- XAMPP/Laragon (Windows) atau LAMP (Linux)
- MySQL
- PHP 8.2
- Node.js (untuk build frontend)

**Setup:**
1. Install XAMPP
2. Clone project ke `htdocs/kasir-web`
3. Setup database & run migrations
4. Build frontend: `npm run build`
5. Copy `frontend/dist` ke `backend/public/app`
6. Access via `http://localhost/kasir-web/public`

### Option 2: Cloud Hosting
**Providers:**
- DigitalOcean Droplet ($6/month)
- AWS Lightsail ($5/month)
- Vultr ($5/month)

**Setup:**
1. Create server (Ubuntu 22.04)
2. Install LEMP stack
3. Deploy Laravel backend
4. Build & deploy Vue frontend
5. Setup SSL (Let's Encrypt)
6. Setup domain

### Option 3: Shared Hosting
- Cari hosting yang support Laravel 11 & PHP 8.2
- Upload via FTP/cPanel
- Import database via phpMyAdmin
- Setup `.env` production

---

## Security Checklist

### Backend
- [ ] Change default passwords
- [ ] Set strong `APP_KEY`
- [ ] Enable HTTPS (SSL)
- [ ] Set `APP_DEBUG=false` di production
- [ ] Restrict database access
- [ ] Enable firewall
- [ ] Backup database daily
- [ ] Update Laravel regularly

### Frontend
- [ ] Remove console.log di production
- [ ] Enable CSP (Content Security Policy)
- [ ] Minify & optimize build
- [ ] Use HTTPS only

### Hardware
- [ ] Lock cash drawer when not in use
- [ ] Secure USB ports
- [ ] CCTV di area kasir
- [ ] Password lock POS terminal

---

## Troubleshooting Hardware

### Barcode Scanner
**Problem:** Scanner tidak berfungsi
- Check USB connection
- Test di Notepad
- Check scanner mode (HID keyboard mode)
- Try different USB port

**Problem:** Scan tapi tidak input
- Fokus harus di browser/aplikasi
- Check keyboard layout (US vs local)

### Printer
**Problem:** Tidak print
- Check power & connection
- Check printer status (online/offline)
- Test print dari Windows
- Check driver installed

**Problem:** Print tapi blank
- Check ribbon/thermal paper
- Adjust print density
- Clean print head

**Problem:** Cut tidak jalan
- Check cut command in code
- Some printers need manual cut

### Cash Drawer
**Problem:** Tidak buka
- Check RJ-11 cable connection
- Check drawer lock
- Test manual command
- Check printer support drawer kick

---

## Performance Optimization

### Database
- Index pada kolom: `barcode`, `sku`, `transaction_no`, `created_at`
- Archive transaksi lama (> 1 tahun)
- Optimize tables regularly: `OPTIMIZE TABLE transactions;`

### Frontend
- Enable gzip compression
- Use CDN for static assets
- Lazy load images
- Cache API responses

### Hardware
- SSD untuk database speed
- Min 8GB RAM untuk multiple terminals
- Gigabit LAN untuk network setup

---

## Backup Strategy

### Database Backup
```bash
# Daily automatic backup
mysqldump -u root -p kasir_pos > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root -p kasir_pos < backup_20241231.sql
```

### File Backup
- Product images
- Receipt templates
- Configuration files

### Backup Schedule
- **Daily**: Database
- **Weekly**: Full system
- **Monthly**: Offsite backup

### Cloud Backup
- Google Drive
- Dropbox
- AWS S3

---

## Maintenance Schedule

### Daily
- Check printer paper
- Check cash drawer balance
- Review error logs

### Weekly
- Backup database
- Check disk space
- Update product stock
- Review low stock alerts

### Monthly
- Update software
- Archive old transactions
- Clean printer head
- Check hardware connections

### Quarterly
- Review user access
- Performance audit
- Security audit
- Hardware check
