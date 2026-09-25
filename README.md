# Purchase Order Payment Extension for OpenCart 4

Ekstensi metode pembayaran **Purchase Order** untuk OpenCart versi 4.x yang kompatibel dengan **PHP 8.4**.

---

## 📁 Struktur Direktori

```text
purchase-order/
├── install.json
├── admin/
│   ├── controller/payment/purchase_order.php
│   ├── language/en-gb/payment/purchase_order.php
│   └── view/template/payment/purchase_order.twig
├── catalog/
│   ├── controller/payment/purchase_order.php
│   ├── language/en-gb/payment/purchase_order.php
│   ├── model/payment/purchase_order.php
│   └── view/template/payment/purchase_order.twig
└── extension/
    └── purchase_order/
        ├── install.json
        ├── admin/
        │   ├── controller/payment/purchase_order.php
        │   ├── language/en-gb/payment/purchase_order.php
        │   └── view/template/payment/purchase_order.twig
        └── catalog/
            ├── controller/payment/purchase_order.php
            ├── language/en-gb/payment/purchase_order.php
            ├── model/payment/purchase_order.php
            └── view/template/payment/purchase_order.twig
```

---

## 📦 Cara Mengompres Ekstensi (`.ocmod.zip`)

Jika Anda melakukan perubahan pada kode atau tampilan ekstensi, ikuti langkah-langkah di bawah ini untuk membuat file instalasi `.ocmod.zip` yang baru.

### 1. Menggunakan Command Line (Terminal / Linux / macOS)

Jalankan perintah berikut di dalam direktori utama proyek (`purchase-order/`):

```bash
zip -r purchase_order.ocmod.zip install.json admin catalog extension
```

> **Catatan:** Perintah di atas akan menghasilkan berkas `purchase_order.ocmod.zip` di root folder.

### 2. Menggunakan GUI / File Manager (Windows / GUI Linux / macOS)

1. Pilih berkas/folder berikut bersamaan:
   - `install.json`
   - folder `admin/`
   - folder `catalog/`
   - folder `extension/`
2. Klik kanan pada area pilihan tersebut.
3. Pilih **Compress to ZIP file** (Windows) atau **Compress** (macOS/Linux).
4. Ubah nama berkas hasil kompresi menjadi **`purchase_order.ocmod.zip`**.

> ⚠️ **Penting:** Pastikan file `install.json` berada di akar (*root*) dari file zip, bukan di dalam subfolder ekstra.

---

## 🧪 Panduan Pengujian Ekstensi (Testing Guide)

### Periksa Sintaks PHP 8.4

Sebelum mengunggah, pastikan tidak ada kesalahan sintaks PHP dengan menjalankan perintah:

```bash
find . -name "*.php" -exec php -l {} \;
```

---

### Langkah 1: Mengunggah Ekstensi ke OpenCart 4

1. Masuk ke **Admin Dashboard** OpenCart 4 Anda.
2. Buka menu **Extensions** > **Extension Installer**.
3. Klik tombol **Upload** (ikon unggah) dan pilih berkas `purchase_order.ocmod.zip`.
4. Setelah proses unggah selesai (ditandai dengan daftar ekstensi muncul), klik tombol **Install** (ikon tambah `+` berwarna hijau) di baris ekstensi *Purchase Order Payment*.

---

### Langkah 2: Mengaktifkan dan Mengonfigurasi Ekstensi

1. Buka menu **Extensions** > **Extensions**.
2. Pada dropdown *Choose the extension type*, pilih **Payments**.
3. Cari **Purchase Order** pada daftar pembayaran.
4. Klik tombol **Install** (ikon `+` hijau) jika belum terpasang.
5. Klik tombol **Edit** (ikon pensil biru).
6. Atur konfigurasi berikut:
   - **Status:** `Enabled`
   - **Total:** Setel total minimal keranjang (misal `0.00` agar selalu muncul).
   - **Order Status:** Setel status pesanan awal (misal `Pending` atau `Processing`).
   - **Geo Zone:** Pilih `All Zones` atau zone tertentu.
   - **Required:** Pilih `Yes` jika ingin mewajibkan pembeli mengisi nomor PO.
   - **Customer Group:** Centang kelompok pelanggan yang diizinkan menggunakan metode ini.
   - **Sort Order:** Atur urutan tampilan (misal `1`).
7. Klik tombol **Save** (ikon simpan biru di pojok kanan atas).

---

### Langkah 3: Pengujian di Halaman Toko (Checkout Test)

1. Buka halaman utama Toko (*Storefront*).
2. Tambahkan produk ke dalam keranjang belanja.
3. Lanjut ke halaman **Checkout** (`index.php?route=checkout/checkout`).
4. Pada bagian **Payment Method**, pilih **Purchase Order**.
5. **Uji Validasi Form:**
   - **Uji Kosong (jika Required = Yes):** Kosongkan field *PO Number* dan klik **Confirm Order**. Sistem harus menampilkan pesan kesalahan: *"You must enter a PO Number!"*.
   - **Uji Karakter Spesial:** Masukkan karakter ilegal seperti `<script>` atau `PO#123!@#` dan klik **Confirm Order**. Sistem harus menampilkan pesan kesalahan format tidak valid.
   - **Uji Valid:** Masukkan nomor PO yang sah (contoh: `PO-2026-001`).
6. Klik **Confirm Order**.
7. Pastikan sistem memproses pembayaran dan mengarahkan ke halaman **Checkout Success** (`index.php?route=checkout/success`).

---

### Langkah 4: Verifikasi Pesanan di Admin Sales

1. Kembali ke **Admin Dashboard**.
2. Buka menu **Sales** > **Orders**.
3. Cari pesanan yang baru saja dibuat dan klik ikon **View** (ikon mata).
4. Verifikasi hal berikut:
   - **Payment Method:** Harus menampilkan nama metode beserta nomor PO, contoh: `Purchase Order (#PO-2026-001)`.
   - **History Tab:** Memiliki riwayat catatan nomor PO dan status pesanan sesuai yang diatur pada konfigurasi admin.

---

### 🔧 Troubleshooting FAQ

- **Error `Could not find install.json` saat upload:**
  Pastikan saat membuat berkas `.ocmod.zip`, Anda mengompres isi foldernya langsung (termasuk `install.json`), **bukan** mengompres folder induk luar (`purchase-order/`).
- **Ekstensi tidak muncul di pilihan Payment Method saat Checkout:**
  1. Pastikan status ekstensi di Admin sudah `Enabled`.
  2. Periksa setting *Customer Group* dan *Geo Zone* di admin.
  3. Pastikan total keranjang memenuhi kriteria setting *Total*.
