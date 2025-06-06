# Sistem Informasi Management Rusunawa Universitas Negeri Jakarta

Sistem informasi yang dirancang khusus untuk mengelola manajemen Rusunawa (Rumah Susun Mahasiswa) di Universitas Negeri Jakarta. Proyek ini bertujuan untuk mempermudah proses pengelolaan penyewaan kamar, pelaporan keuangan masuk dan keluar, serta maintenance fasilitas rusunawa.

🔗 [Repository GitHub](https://github.com/akmalrafidiara/rusunawa-unj)

---

## 📌 Deskripsi Singkat

Sistem ini memberikan solusi digital dalam mengelola operasional Rusunawa UNJ dengan fitur-fitur lengkap seperti:

-   Pengelolaan informasi kamar
-   Pemesanan dan penyewaan kamar
-   Pelaporan keuangan masuk dan keluar
-   Pengelolaan data penyewa
-   Monitoring dan maintenance fasilitas rusunawa

---

## ✨ Fitur Utama

| Fitur                     | Deskripsi                                                            |
| ------------------------- | -------------------------------------------------------------------- |
| **Pengelolaan Informasi** | Menampilkan daftar kamar, status ketersediaan, serta detail penyewa. |
| **Pemesanan**             | Memungkinkan mahasiswa melakukan pemesanan kamar secara online.      |
| **Penyewaan**             | Mengatur masa sewa, pembayaran, dan konfirmasi status kamar.         |
| **Pelaporan**             | Mencatat dan menampilkan laporan keuangan serta kondisi kamar.       |
| **Maintenance**           | Melaporkan kerusakan atau permintaan perbaikan fasilitas.            |

---

## 🔧 Teknologi & Library yang Digunakan

### Framework & Tools

-   [Laravel 12.x](https://laravel.com/docs/12.x)
-   [Livewire](https://laravel-livewire.com/)
-   [Alpine.js](https://alpinejs.dev/) _(Untuk interaksi frontend reaktif)_
-   [MySQL](https://mysql.com) _(Database management system)_

### UI Components & Styling

-   [Flowbite + Tailwind CSS](https://flowbite.com)
-   [Flux UI](https://fluxui.dev)

### Paket Tambahan

-   [Livewire Alert](https://github.com/jantinnerezo/livewire-alert) _(Untuk notifikasi SweetAlert2)_
-   [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction) _(Manajemen role & permission)_
-   [Spatie Laravel Filepond](https://github.com/spatie/livewire-filepond) _(Ui untuk upload file)_

---

## 🛠️ Requirements

Pastikan lingkungan pengembangan Anda sudah terinstal:

-   PHP >= 8.3
-   Composer
-   Node.js & NPM
-   MySQL Server
-   Laravel 12.x

---

## ▶️ Instalasi

### Langkah-langkah Instalasi

1. **Clone repository**

    ```bash
    git clone https://github.com/akmalrafidiara/rusunawa-unj.git
    cd rusunawa-unj
    ```

2. **Install dependency backend & frontend**

    ```bash
    composer install
    npm install
    ```

3. **Buat file `.env` dan generate key**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Konfigurasi database di `.env`**

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_kamu
    DB_USERNAME=username_mysql
    DB_PASSWORD=password_mysql
    ```

5. **Jalankan migrasi dan seeding**

    ```bash
    php artisan migrate:fresh --seed
    ```

6. **Build asset frontend**

    ```bash
    npm run build
    ```

7. **Jalankan aplikasi**
    ```bash
    composer run dev
    ```
    atau gunakan shortcut:
    ```bash
    crd # Jalankan "composer run dev"
    ```

Aplikasi akan tersedia di: [http://localhost:8000](http://localhost:8000)

---

## ⚙️ Shortcut Bash yang Digunakan

### Artisan Commands

| Perintah           | Fungsi                           |
| ------------------ | -------------------------------- |
| `pa serve`         | Jalankan server lokal            |
| `pa migrate`       | Jalankan migrasi database        |
| `pa migrate:fresh` | Reset migrasi dan jalankan ulang |
| `pa route:clear`   | Hapus cache route                |
| `pa config:clear`  | Hapus cache config               |
| `pa view:clear`    | Hapus cache view                 |

### Composer Commands

| Perintah            | Fungsi                        |
| ------------------- | ----------------------------- |
| `com update`        | Update semua package composer |
| `com install`       | Install package composer      |
| `com dump-autoload` | Regenerasi autoload class     |

### Node.js Commands

| Perintah        | Fungsi                           |
| --------------- | -------------------------------- |
| `npm install`   | Install package frontend         |
| `npm run dev`   | Jalankan Vite development server |
| `npm run build` | Build asset untuk produksi       |

---

## 👥 Kontributor

### Developer

-   **Akmal Rafi Diara Putra**  
    💻 Full-stack Development,, Database, System Design  
    📍 Jakarta, Indonesia  
    🎓 Informatika - Universitas Negeri Jakarta  
    🔗 [GitHub](https://github.com/akmalrafidiara)

-   **Faizal Rizqi Kholily**  
    💻 Full-stack Development, Authentication, Feature Implementation  
    📍 Jakarta Timur, Indonesia  
    🎓 Informatika - Universitas Negeri Jakarta  
    🔗 [GitHub](https://github.com/faizalrizqikholily)

### UI / UX Designer

-   **Rasyaad Maulana Khandias**  
    🎨 Desain antarmuka, pengalaman pengguna, prototyping  
    🔗 [GitHub](https://github.com/rasyaadmk)

### Readiness & Quality Assurance

-   **Roland Roman Topuh**  
    🔍 Testing, debugging, dan quality assurance  
    🔗 [GitHub](https://github.com/RolandRoman)

---

## 🐚 Shortcut Bash (Opsional)

Shortcut berikut dapat memudahkan pengembangan aplikasi sehari-hari:

```bash
# php artisan
pa() { php artisan "$@"; }

# composer
com() { composer "$@"; }

# Artisan shortcuts
alias pas='pa serve'
alias pam='pa migrate'
alias pamf='pa migrate:fresh'
alias pamfs='pa migrate:fresh --seed'
alias pat='pa tinker'
alias pac='pa config:clear'
alias parr='pa route:cache'
alias par='pa route:clear'
alias pav='pa view:clear'
alias pacc='pa config:cache'
alias pavc='pa view:cache'

# Composer shortcuts
alias cda='com dump-autoload'
alias cu='com update'
alias ci='com install'
alias crd='com run dev'

# Node shortcuts
alias ni='npm install'
alias nrd='npm run dev'
alias nrb='npm run build'
alias nrt='npm run test'
alias nrs='npm run serve'
```

## 📜 Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.

---
