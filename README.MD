# Aplikasi Manajemen Sekolah

Aplikasi Manajemen Sekolah menggunakan **Slim Framework** dan **Meedo** untuk mengelola data siswa dan sekolah. Aplikasi ini mencakup fitur untuk menambah, mengedit, menghapus, dan menampilkan data siswa, serta mengelola data sekolah dan admin.

## 1. Persiapan

Sebelum memulai, pastikan kamu telah menyiapkan hal-hal berikut:

### Prasyarat

1. **PHP**: Pastikan PHP versi 7.+ ke atas sudah terinstall. Kamu dapat mengecek versi PHP dengan menjalankan perintah:

   ```bash
   php -v
   ```

   Pada project ini menggunakan PHP versi 7.2.19

2. **Composer**: Composer adalah manajer dependensi untuk PHP. Jika Composer belum terinstall, ikuti petunjuk di [https://getcomposer.org/download/](https://getcomposer.org/download/).

3. **Database**: Aplikasi ini memerlukan database untuk menyimpan data admin, siswa dan sekolah. Kamu bisa menggunakan MySQL atau database lainnya sesuai kebutuhan.

   - **Setup Database**:

     - Buatlah database baru, misalnya `slim_school_management`.
     - Buat tabel untuk menyimpan data admin, siswa dan sekolah. Berikut adalah contoh skema tabel yang diperlukan:

       ```sql
       CREATE TABLE tbl_admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL
       );

       CREATE TABLE tbl_school (
            id INT AUTO_INCREMENT PRIMARY KEY,
            school_name VARCHAR(255) NOT NULL,
            address TEXT NOT NULL
       );

       CREATE TABLE tbl_students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_school INT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            FOREIGN KEY (id_school) REFERENCES tbl_school(id) ON DELETE SET NULL
       );
       ```

## 2. Instalasi

### Langkah-langkah untuk menginstal aplikasi:

1. **Clone Repository**:
   Jika kamu belum memiliki salinan lokal proyek ini, clone repositori ke komputer kamu dengan menjalankan perintah berikut:
   ```
   git clone git@github.com:najahyuga/slim-frameworks-manage-school.git
   cd slim-frameworks-manage-school
   ```
2. **Install Slim Framework**: Buka terminal ketikkan composer require slim/slim "^3.0".
3. **Install Medoo untuk koneksi ke database**: Buka terminal ketikkan composer require catfan/medoo.
3. **Import Dummy Data**: Buka terminal ketikkan php dummyData.php atau cukup import file slim_school_management.sql pada phpmyadmin.
3. **Jalankan Project**: Buka terminal ketikkan php -S localhost:8000 -t public.


