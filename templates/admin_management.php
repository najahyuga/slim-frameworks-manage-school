<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Slim App Management School</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link link-light" href="/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link link-light" href="/schools">Manajemen Sekolah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link link-light" href="/students">Manajemen Siswa</a>
                </li>
                <li class="nav-item">
                <a id="logoutButton" class="nav-link link-dark" href="/logout">Logout</a>
                </li>
            </ul>
        </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Manajemen Admin</h2>
        
        <button class="btn btn-success mb-3" id="addAdminBtn">Tambah Admin</button>
        
        <table id="adminTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr data-id="<?= $admin['id'] ?>">
                        <td><?= $admin['id'] ?></td>
                        <td><?= !empty($admin['name']) ? $admin['name'] : "Nama Belum Terisi!" ?></td>
                        <td><?= $admin['username'] ?></td>
                        <td>
                            <button class="btn btn-warning editBtn">Edit</button>
                            <button class="btn btn-danger deleteBtn">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- modal -->
        <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adminModalLabel">Tambah Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="adminForm">
                            <input type="hidden" id="adminId">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#adminTable').DataTable();

            // add
            $('#addAdminBtn').click(function() {
                $('#adminForm')[0].reset();
                $('#adminModalLabel').text('Tambah Admin');
                $('#saveBtn').text('Simpan');
                $('#adminId').val('');
                $('#password').prop('required', true);
                var myModal = new bootstrap.Modal(document.getElementById('adminModal'));
                myModal.show();
            });

            // edit
            $(document).on('click', '.editBtn', function() {
                var row = $(this).closest('tr');
                var adminId = row.data('id');
                var name = row.find('td:eq(1)').text();
                var username = row.find('td:eq(2)').text();
                
                $('#adminId').val(adminId);
                $('#username').val(username);
                $('#name').val(name);
                $('#password').val('').prop('required', false);
                $('#adminModalLabel').text('Edit Admin');
                $('#saveBtn').text('Perbarui');

                // Tampilkan modal
                var myModal = new bootstrap.Modal(document.getElementById('adminModal'));
                myModal.show();
            });

            // delete
            $(document).on('click', '.deleteBtn', function() {
                var row = $(this).closest('tr');
                var adminId = row.data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data Admin ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/delete-admin/' + adminId,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', 'Admin telah dihapus.', 'success');
                                    row.remove();
                                } else {
                                    Swal.fire('Gagal!', 'Terjadi kesalahan', 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Simpan atau perbarui data admin
            $('#adminForm').submit(function(e) {
                e.preventDefault();
                var adminId = $('#adminId').val();
                var username = $('#username').val();
                var password = $('#password').val();
                var name = $('#name').val();
                var url = adminId ? '/update-admin/' + adminId : '/add-admin';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        username: username,
                        password: password,
                        name: name
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: adminId ? 'Admin Diperbarui!' : 'Admin Ditambahkan!',
                                text: 'Operasi berhasil.',
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                        }
                    }
                });
            });
        });

        // SweetAlert Logout Confirmation
        document.getElementById('logoutButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/logout';
                }
            });
        });
    </script>
</body>
</html>
