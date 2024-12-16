<?php

session_start();
require __DIR__ . "/../vendor/autoload.php";

$app = new \Slim\App;

$app->getContainer()['settings']['displayErrorDetails'] = true;

$container = $app->getContainer();

// config template engine with php
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('../templates/');
};

// config db
$container['db'] = function ($container) {
    require __DIR__ . "/../config/database.php";
    return $database;
};

$app->get('/', function ($request, $response, $args){
    $this->view->render($response, 'index.php');
});

// route login
$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.php');
});

$app->post('/login', function ($request, $response, $args) {
    $username = $request->getParam('username');
    $password = $request->getParam('password');

    // Validasi user
    $admin = $this->db->get('tbl_admins', '*', ['username' => $username]);
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['id'] = $admin['id'];

        // Redirect ke dashboard dengan successMessage
        $_SESSION['successMessage'] = 'Selamat datang, ' . $admin['username'] . '!';
        return $response->withRedirect('/dashboard');
    }

    // Redirect ke login dengan pesan error
    return $this->view->render($response, 'login.php', ['error' => 'Username atau password salah.']);
});

// Route dashboard
$app->get('/dashboard', function ($request, $response, $args) {
    if (!isset($_SESSION['id'])) {
        return $response->withRedirect('/login');
    }

    // Ambil data jumlah siswa per sekolah
    $students = $this->db->query("
        SELECT id_school, COUNT(id) AS student_count
        FROM tbl_students
        GROUP BY id_school
        ORDER BY student_count DESC
    ")->fetchAll();

    // Ambil data sekolah
    $schools = $this->db->select('tbl_school', ['id', 'school_name']);

    // Format data untuk grafik
    $labels = [];
    $data = [];
    foreach ($schools as $school) {
        $labels[] = $school['school_name'];
        $data[] = 0; // Default jumlah siswa 0
    }

    foreach ($students as $student) {
        $index = array_search($student['id_school'], array_column($schools, 'id'));
        if ($index !== false) {
            $data[$index] = $student['student_count'];
        }
    }

    // Ambil successMessage dari session jika ada
    $successMessage = $_SESSION['successMessage'] ?? null;
    unset($_SESSION['successMessage']); // Hapus pesan setelah digunakan

    return $this->view->render($response, 'dashboard.php', [
        'labels' => json_encode($labels),
        'data' => json_encode($data),
        'schools' => count($schools),
        'students' => array_sum(array_column($students, 'student_count')),
        'successMessage' => $successMessage,
    ]);
});

// Route untuk melihat daftar sekolah
// Ambil semua data sekolah
$app->get('/schools', function ($request, $response) {
    // Ambil data sekolah dari database
    $schools = $this->db->select('tbl_school', ['id', 'school_name', 'address']);
    
    return $this->view->render($response, 'school_management.php', [
        'schools' => $schools
    ]);
});

$app->post('/add-school', function ($request, $response) {
    $data = $request->getParsedBody();
    $schoolName = $data['school_name'];
    $address = $data['address'];

    $this->db->insert('tbl_school', [
        'school_name' => $schoolName,
        'address' => $address
    ]);

    return $response->withJson(['success' => true]);
});

// Mengupdate sekolah
$app->post('/update-school/{id}', function ($request, $response, $args) {
    $schoolId = $args['id'];
    $data = $request->getParsedBody();
    $schoolName = $data['school_name'];
    $address = $data['address'];

    // Update data sekolah di database
    $this->db->update('tbl_school', [
        'school_name' => $schoolName,
        'address' => $address
    ], ['id' => $schoolId]);

    return $response->withJson(['success' => true]);
});

// Menghapus sekolah
$app->delete('/delete-school/{id}', function ($request, $response, $args) {
    $schoolId = $args['id'];
    $this->db->delete('tbl_school', ['id' => $schoolId]);

    return $response->withJson(['success' => true]);
});

// Route untuk melihat daftar siswa
// Ambil semua data siswa
$app->get('/students', function ($request, $response) {
    // Ambil data siswa dan data sekolah dari database
    $students = $this->db->select('tbl_students', ['id', 'name', 'email', 'id_school']);
    $schools = $this->db->select('tbl_school', ['id', 'school_name']);
    
    return $this->view->render($response, 'student_management.php', [
        'students' => $students,
        'schools' => $schools 
    ]);
});

$app->post('/add-student', function ($request, $response) {
    $data = $request->getParsedBody();
    $studentName = $data['name'];
    $email = $data['email'];
    $idSchool = $data['id_school'];  // Dapatkan id_school dari form

    $this->db->insert('tbl_students', [
        'name' => $studentName,
        'email' => $email,
        'id_school' => $idSchool  // Simpan relasi ke sekolah
    ]);

    return $response->withJson(['success' => true]);
});

$app->post('/update-student/{id}', function ($request, $response, $args) {
    $studentId = $args['id'];
    $data = $request->getParsedBody();
    $studentName = $data['name'];
    $email = $data['email'];
    $idSchool = $data['id_school'];

    $this->db->update('tbl_students', [
        'name' => $studentName,
        'email' => $email,
        'id_school' => $idSchool
    ], ['id' => $studentId]);

    return $response->withJson(['success' => true]);
});

$app->delete('/delete-student/{id}', function ($request, $response, $args) {
    $studentId = $args['id'];
    $this->db->delete('tbl_students', ['id' => $studentId]);

    return $response->withJson(['success' => true]);
});

// Route untuk melihat daftar admin
// Ambil semua data admin
$app->get('/admin', function ($request, $response) {
    $admins = $this->db->select('tbl_admins', ['id', 'username', 'password', 'name']);
    
    return $this->view->render($response, 'admin_management.php', [
        'admins' => $admins
    ]);
});

$app->post('/add-admin', function ($request, $response) {
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];
    $name = $data['name'];

    $hashPWD = password_hash($password, PASSWORD_DEFAULT);

    $this->db->insert('tbl_admins', [
        'username' => $username,
        'password' => $hashPWD,
        'name' => $name
    ]);

    return $response->withJson(['success' => true]);
});

// Mengupdate admin
$app->post('/update-admin/{id}', function ($request, $response, $args) {
    $adminId = $args['id'];
    $data = $request->getParsedBody();
    
    $updateData = [
        'username' => $data['username'],
        'name' => $data['name']
    ];
    
    // Jika password diisi, hash password dan tambahkan ke data
    if (!empty($data['password'])) {
        $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    // Update ke database
    $this->db->update('tbl_admins', $updateData, ['id' => $adminId]);

    return $response->withJson(['success' => true]);
});

// Menghapus admin
$app->delete('/delete-admin/{id}', function ($request, $response, $args) {
    $adminId = $args['id'];
    $this->db->delete('tbl_admins', ['id' => $adminId]);

    return $response->withJson(['success' => true]);
});

// Route logout
$app->get('/logout', function ($request, $response, $args) {
    session_unset();
    session_destroy();
    return $response->withRedirect('/login');
});

// Jalankan aplikasi
$app->run();