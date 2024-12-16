<?php
// Include Medoo
require __DIR__ . '/vendor/autoload.php';

use Medoo\Medoo;

// Koneksi ke database
$database = new Medoo([
    'database_type' => 'mysql', 
    'database_name' => 'slim_school_management', 
    'server' => 'localhost', 
    'username' => 'root', 
    'password' => '', 
    'charset' => 'utf8',
]);

// Function untuk membuat dummy data untuk tbl_school
function createDummySchools($database, $totalSchools) {
    for ($i = 1; $i <= $totalSchools; $i++) {
        // Generate nama sekolah dan alamat dummy
        $schoolName = 'School ' . $i;
        $address = 'Address for School ' . $i;
        
        // Insert ke tabel tbl_school
        $database->insert('tbl_school', [
            'school_name' => $schoolName,
            'address' => $address,
        ]);
    }
}

// Function untuk membuat dummy data untuk tbl_students
function createDummyStudents($database, $totalStudents, $totalSchools) {
    for ($i = 1; $i <= $totalStudents; $i++) {
        // Generate nama siswa dan email dummy
        $studentName = 'Student ' . $i;
        $email = 'student' . $i . '@example.com';
        
        // Pilih school_id secara acak
        $schoolId = rand(1, $totalSchools);
        
        // Insert ke tabel tbl_students
        $database->insert('tbl_students', [
            'id_school' => $schoolId,
            'name' => $studentName,
            'email' => $email,
        ]);
    }
}

$totalSchools = 1000;
createDummySchools($database, $totalSchools);

// Membuat 1.000 data siswa (bisa lebih dari 1 siswa per sekolah)
$totalStudents = 1000;
createDummyStudents($database, $totalStudents, $totalSchools);

echo "Dummy data berhasil ditambahkan!";
?>
