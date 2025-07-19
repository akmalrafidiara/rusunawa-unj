import Swal from 'sweetalert2'

window.Swal = Swal

import "trix";

// Menonaktifkan fitur upload dokumen di Trix
document.addEventListener("trix-file-accept", function (event) {
    event.preventDefault(); // Mencegah Trix menerima file
    alert("Upload dokumen tidak diizinkan."); // Opsional: Beri tahu pengguna
});

// Event listener untuk perubahan konten Trix
document.addEventListener('trix-change', function (event) {
    // Emit event Livewire dengan konten terbaru dari Trix
    // Pastikan Livewire sudah terinisialisasi.
    Livewire.dispatch('contentChanged', { content: event.target.value });
});

// Event listener untuk inisialisasi Trix Editor (untuk memanipulasi input link)
document.addEventListener("trix-initialize", function (event) {
    const toolbar = event.target.toolbarElement;
    const urlInput = toolbar.querySelector("[data-trix-input][name='href']");

    if (urlInput) {
        // 1. Ubah tipe input dari 'url' menjadi 'text' agar browser tidak melakukan validasi ketat
        urlInput.type = 'text';

        // 2. Tambahkan event listener untuk menormalisasi URL saat kehilangan fokus atau tekan enter
        urlInput.addEventListener('blur', normalizeUrlInput);
        urlInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                normalizeUrlInput.call(this);
                e.preventDefault();
            }
        });

        // 3. Tambahkan event listener ke tombol "Link" itu sendiri
        const linkButton = toolbar.querySelector("[data-trix-action='link']");
        if (linkButton) {
            linkButton.addEventListener('mousedown', normalizeUrlInputOnButtonClick);
        }
    }
});

// Fungsi untuk menormalisasi URL (menambahkan https:// jika belum ada)
function normalizeUrlInput() {
    let url = this.value.trim(); // Ambil nilai input dan hapus spasi di awal/akhir

    if (url.length === 0) {
        return; // Jangan lakukan apa-apa jika kosong
    }

    // Periksa apakah URL sudah memiliki protokol (http://, https://, ftp://, dll.)
    if (!/^(https?|ftp):\/\//i.test(url)) {
        // Jika tidak ada, tambahkan https:// secara default
        url = 'https://' + url;
    }

    this.value = url; // Setel kembali nilai input yang sudah dinormalisasi
}

// Fungsi pembantu untuk tombol link, ini memastikan nilai trix-input diperbarui sebelum tindakan link trix dilakukan
function normalizeUrlInputOnButtonClick(event) {
    const toolbar = event.target.closest('trix-toolbar');
    if (!toolbar) return;

    const urlInput = toolbar.querySelector("[data-trix-input][name='href']");
    if (urlInput) {
        normalizeUrlInput.call(urlInput);
    }
}

// Event listener untuk Livewire: pastikan Livewire sudah terinisialisasi
// Anda bisa menempatkan ini di sini atau di dalam document.addEventListener('livewire:initialized', ...)
// Tergantung pada bagaimana Livewire dimuat di proyek Anda.
// Jika `@livewireScripts` ada di bagian bawah body, biasanya ini aman.
Livewire.on('trix-reset', () => {
    const trixEditor = document.querySelector('trix-editor');
    if (trixEditor) {
        trixEditor.editor.loadHTML(''); // Mengosongkan konten
    }
});

Livewire.on('trix-load-content', (content) => {
    const trixEditor = document.querySelector('trix-editor');
    if (trixEditor) {
        trixEditor.editor.loadHTML(content); // Memuat konten yang ada
    }
});
