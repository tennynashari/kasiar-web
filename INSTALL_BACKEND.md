# Instalasi Backend (Laravel)

## Prerequisites
- PHP 8.2 atau lebih baru
- Composer
- MySQL 8.0 atau lebih baru
- Node.js & NPM (untuk Vite)

## Langkah-langkah Instalasi

### 1. Install Dependencies
```bash
cd backend
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_pos
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Buat Database
Buat database baru di MySQL:
```sql
CREATE DATABASE kasir_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

Ini akan membuat:
- Semua tabel database
- Sample data (outlets, users, categories, products)
- Default users:
  - Owner: owner@kasir.app / password
  - Supervisor: supervisor@kasir.app / password
  - Kasir: kasir@kasir.app / password

### 6. Jalankan Server
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## Testing API
Gunakan Postman atau curl untuk test API:

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"kasir@kasir.app","password":"password"}'
```

### Get Products (dengan token)
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Struktur Database

### Tables
- `outlets` - Data outlet/toko
- `users` - Pengguna sistem (owner, supervisor, kasir, kitchen)
- `categories` - Kategori produk
- `products` - Master produk
- `transactions` - Transaksi penjualan
- `transaction_items` - Detail item transaksi
- `tables` - Meja (untuk F&B)
- `cash_flows` - Arus kas
- `activity_logs` - Log aktivitas user

## Troubleshooting

### Error: "Access denied for user"
Pastikan username dan password MySQL di `.env` sudah benar.

### Error: "SQLSTATE[HY000] [2002]"
Pastikan MySQL service sudah running.

### Error: Composer dependencies
Jalankan: `composer update`

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```
