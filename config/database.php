<?php
use Medoo\Medoo;

$database = new Medoo([
    'database_type' => 'mysql', 
    'database_name' => 'slim_school_management', 
    'server' => 'localhost', 
    'username' => 'root', 
    'password' => '', 
    'charset' => 'utf8', 
]);

// cek koneksi
// try {
//     $database->query("SELECT 1");
//     echo "Koneksi ke database berhasil!";
// } catch (Exception $e) {
//     echo "Koneksi database gagal: " . $e->getMessage();
// }

return $database;
