@echo off
setlocal

echo ======================================================
echo =   Memulai Instalasi dan Setup Proyek Laravel Ini   =
echo ======================================================
echo.
echo Pastikan koneksi internet stabil. Proses ini mungkin memakan waktu.
echo.

REM --- 1. Menjalankan Composer Update ---
echo ====================================
echo =   1. Memperbarui Dependensi PHP (Composer)   =
echo ====================================
echo.
call composer update
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal menjalankan Composer Update. Pastikan Composer sudah terinstal dan koneksi internet baik.
    goto :end
)
echo.
echo Selesai: Composer Update.
echo.

REM --- 2. Menjalankan NPM Install ---
echo ====================================
echo =   2. Menginstal Dependensi JavaScript (NPM)   =
echo ====================================
echo.
call npm install
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal menjalankan NPM Install. Pastikan Node.js dan NPM sudah terinstal.
    goto :end
)
echo.
echo Selesai: NPM Install.
echo.

REM --- 3. Menjalankan NPM Run Build ---
echo ====================================
echo =   3. Membangun Asset Frontend (NPM Run Build)   =
echo ====================================
echo.
call npm run build
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal menjalankan NPM Run Build. Mungkin ada masalah dengan dependensi frontend.
    goto :end
)
echo.
echo Selesai: NPM Run Build.
echo.

REM --- 4. Menjalankan PHP Artisan Migrate:Fresh --seed ---
echo ==================================================
echo =   4. Reset Database dan Isi Data Awal (Migrate:Fresh --seed)   =
echo ==================================================
echo.
echo PERHATIAN: Semua data di database akan DIHAPUS dan dibuat ulang!
echo.
call php artisan migrate:fresh --seed
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal menjalankan Migrate:Fresh --seed. Pastikan konfigurasi database sudah benar di file .env.
    goto :end
)
echo.
echo Selesai: Migrate:Fresh --seed.
echo.

REM --- 5. Menjalankan PHP Artisan Storage:Link ---
echo ============================================
echo =   5. Membuat Symlink untuk Folder Storage   =
echo ============================================
echo.
call php artisan storage:link
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal membuat Storage Link. Coba jalankan CMD sebagai Administrator.
    goto :end
)
echo.
echo Selesai: Storage Link.
echo.

REM --- 6. Menjalankan Composer Run Dev (atau PHP Artisan Serve) ---
echo ===========================================
echo =   6. Menjalankan Server Laravel (Development)   =
echo ===========================================
echo.
echo Server akan berjalan. Untuk menghentikan server, tutup jendela ini (Ctrl+C).
echo.
REM Anda bisa memilih salah satu dari di bawah ini:
REM Jika ingin menggunakan `composer run dev` seperti yang Anda minta:
call composer run dev
REM Atau, jika ingin server langsung terbuka di browser (default Laravel):
REM call php artisan serve

if %errorlevel% neq 0 (
    echo.
    echo ERROR: Gagal menjalankan server Laravel. Periksa port yang mungkin sudah terpakai.
    goto :end
)
echo.
echo Proyek Laravel berhasil dijalankan!
echo Silakan akses di browser Anda, biasanya di: http://127.0.0.1:8000
echo.

:end
echo ======================================================
echo =             Proses Selesai / Terjadi Error           =
echo ======================================================
echo.
pause
