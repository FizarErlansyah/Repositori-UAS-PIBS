# CRUD Profil Mahasiswa – UAS PIBS

Aplikasi web sederhana berbasis **PHP + MySQL + File JSON** untuk mengelola (Create, Read, Update, Delete) data profil mahasiswa. Detail profil seperti biodata lengkap, riwayat pendidikan, pengalaman, keterampilan, hobi, dan publikasi disimpan dalam file JSON terpisah per NIM. Hal ini memisahkan data inti (identitas) di database dengan data dinamis/kompleks di format yang mudah diubah.

## 🚀 Fitur Utama
- **Manajemen Profil Mahasiswa (CRUD)**: Tambah, ubah, hapus data inti (NIM, Nama, Prodi, Foto).
- **Upload Foto**: Otomatis disimpan dengan pola `foto-{nim}.ext` (fallback ke `foto.jpg`).
- **Upload File JSON Per Mahasiswa**: Memuat section lanjutan (Education, Experience, Skills, Hobbies, Publication, Social Links).
- **Template JSON Siap Pakai**: Disediakan di `Data/template.json` untuk memudahkan format standar.
- **Halaman Tampilan Dinamis (`index.php`)**: Navigasi antar section (Biodata, Education, Experience, Skills, Publication, Hobbies & Interests).
- **Account Switcher**: Dropdown untuk berganti mahasiswa secara cepat berdasarkan NIM.
- **Social Links Dinamis**: Setiap mahasiswa dapat memiliki link media sosial berbeda (Instagram, WhatsApp, YouTube, LinkedIn) yang dikonfigurasi via JSON.
- **Fallback Data**: Jika JSON belum ada, aplikasi menampilkan nilai default yang ramah pengguna.
- **Download File JSON** dari halaman admin bila tersedia.

## 🗃️ Struktur Folder
```
Repositori-UAS-PIBS/
├── adminProfil.php      # Halaman CRUD & upload foto + JSON
├── index.php            # Halaman profil interaktif
├── koneksi.php          # Koneksi database MySQL
├── styles.css           # Styling utama
├── Data/                # Penyimpanan file JSON per NIM
│   ├── template.json    # Format dasar JSON
│   ├── 2024081015.json  # Contoh data mahasiswa
│   ├── ...              # File JSON lain sesuai NIM
├── README.md            # Dokumentasi proyek
```

## 🧱 Struktur Database
Aplikasi menggunakan database MySQL bernama `pibs` dengan tabel minimal berikut:
```sql
CREATE TABLE profil (
  nim   VARCHAR(20) PRIMARY KEY,
  nama  VARCHAR(100) NOT NULL,
  prodi VARCHAR(50) NOT NULL,
  foto  VARCHAR(120) DEFAULT 'foto.jpg'
);
```

## 📄 Format File JSON (Per Mahasiswa)
Contoh template (`Data/template.json`):
```json
{
  "biodata": {
    "nim": "2024XXXX",
    "nama": "Nama Mahasiswa",
    "tempat_lahir": "Kota",
    "tanggal_lahir": "YYYY-MM-DD",
    "alamat": "Alamat domisili"
  },
  "education": [
    {"tahun": "2020-2023", "institusi": "Nama Sekolah", "deskripsi": "Deskripsi singkat"}
  ],
  "experience": ["Posisi / Peran"],
  "skills": {"Kategori": "Daftar skill"},
  "hobbies": [{"icon": "fa-gamepad", "name": "Hobi"}],
  "publication": "Judul atau daftar publikasi",
  "social_links": {
    "instagram": "https://instagram.com/username",
    "whatsapp": "https://wa.me/628xxx",
    "youtube": "https://youtube.com/@channel",
    "linkedin": "https://linkedin.com/in/username"
  }
}
```

### **Keterangan Field JSON:**
- `biodata`: Data dasar mahasiswa (NIM, nama, tempat/tanggal lahir, alamat)
- `education`: Array riwayat pendidikan
- `experience`: Array pengalaman organisasi/kerja
- `skills`: Object kategori skill dan deskripsinya
- `hobbies`: Array hobi dengan ikon Font Awesome
- `publication`: String publikasi atau penelitian
- `social_links`: Object link media sosial (opsional, jika kosong akan pakai default atau tidak ditampilkan)

Penamaan file JSON: `Data/<nim>.json` (misal: `Data/2024081015.json`).

## 🔧 Cara Menjalankan (Laragon / XAMPP)
1. Clone repository ke direktori root server lokal (contoh Laragon: `C:/laragon/www/`).  
2. Buat database `pibs` dan jalankan SQL tabel di atas.  
3. Pastikan ekstensi PHP `mysqli` aktif.  
4. Letakkan file foto default `foto.jpg` (opsional).  
5. Akses: `http://localhost/Repositori-UAS-PIBS/adminProfil.php` untuk CRUD.  
6. Tambah mahasiswa + upload JSON (opsional).  
7. Buka `index.php` untuk melihat tampilan profil dinamis.  

## 🖼️ Konvensi File Foto
- Nama file foto mengikuti pola: `foto-{nim}.jpg` atau `foto-{nim}.png`.
- Jika tidak ada foto khusus, aplikasi menggunakan `foto.jpg` sebagai default.

## ✅ Validasi & Fallback
- File JSON diverifikasi dengan `json_decode` sebelum disimpan.
- Jika section tidak ada di JSON: aplikasi memakai nilai default (hardcoded di `index.php`).

## 🛡️ Keamanan Sederhana
Hal-hal yang bisa ditingkatkan (belum diterapkan penuh):
- Sanitasi input (SQL Injection prevention menggunakan prepared statements).
- Validasi ukuran & tipe file upload.
- Pembatasan akses halaman admin (login / session).

## 👥 Anggota Kelompok
Proyek ini dibuat oleh Kelompok UAS PIBS:

| No | NIM        | Nama Lengkap                  |
|----|------------|-------------------------------|
| 1  | 2024081015 | Arae Mahesa Almera            | 
| 2  | 2024081024 | Panji Kurnia Akbar            | 
| 3  | 2024081032 | Mochammad Lintar Arya Dwiputra| 
| 4  | 2024081041 | Fizar Erlansyah               | 

## 🌐 Dependensi Eksternal
- [Font Awesome 6](https://cdnjs.com/) untuk ikon.
- CSS custom (`styles.css`).

## 🧪 Pengujian Manual
- Tambah data via `adminProfil.php` → Cek muncul di tabel.  
- Upload JSON → Pastikan section di `index.php` berubah sesuai isi file.  
- Ganti foto → Pastikan foto tampil dan nama file ter-update.  
- Hapus data → Pastikan tidak bisa lagi diakses via `index.php?nim=...`.  
- Test Social Links → Ganti mahasiswa via Account Switcher, pastikan social media di footer berubah sesuai JSON masing-masing.
- Test Fallback → Hapus/kosongkan field di JSON, pastikan data default tetap muncul.  
---
Dibuat untuk pemenuhan tugas PIBS. Silakan ajukan saran atau perbaikan melalui pull request. 😊