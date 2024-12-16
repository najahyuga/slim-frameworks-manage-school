<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Sekolah</title>
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
                    <a class="nav-link link-light" href="/students">Manajemen Siswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link link-light" href="/admin">Manajemen Admin</a>
                </li>
                <li class="nav-item">
                <a id="logoutButton" class="nav-link link-dark" href="/logout">Logout</a>
                </li>
            </ul>
        </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Manajemen Sekolah</h2>
        
        <button class="btn btn-success mb-3" id="addSchoolBtn">Tambah Sekolah</button>
        
        <table id="schoolTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Sekolah</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schools as $school): ?>
                    <tr data-id="<?= $school['id'] ?>">
                        <td><?= $school['id'] ?></td>
                        <td><?= $school['school_name'] ?></td>
                        <td><?= $school['address'] ?></td>
                        <td>
                            <button class="btn btn-warning editBtn">Edit</button>
                            <button class="btn btn-danger deleteBtn">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- modal -->
        <div class="modal fade" id="schoolModal" tabindex="-1" aria-labelledby="schoolModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="schoolModalLabel">Tambah Sekolah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="schoolForm">
                            <input type="hidden" id="schoolId">
                            <div class="mb-3">
                                <label for="school_name" class="form-label">Nama Sekolah</label>
                                <input type="text" class="form-control" id="school_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="address" required>
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
            $('#schoolTable').DataTable();

            // add
            $('#addSchoolBtn').click(function() {
                $('#schoolForm')[0].reset();
                $('#schoolModalLabel').text('Tambah Sekolah');
                $('#saveBtn').text('Simpan');
                $('#schoolId').val('');
                var myModal = new bootstrap.Modal(document.getElementById('schoolModal'));
                myModal.show();
            });

            // edit
            $(document).on('click', '.editBtn', function() {
                var row = $(this).closest('tr');
                var schoolId = row.data('id');
                var schoolName = row.find('td:eq(1)').text();
                var address = row.find('td:eq(2)').text();

                $('#schoolId').val(schoolId);
                $('#school_name').val(schoolName);
                $('#address').val(address);
                $('#schoolModalLabel').text('Edit Sekolah');
                $('#saveBtn').text('Perbarui');
                var myModal = new bootstrap.Modal(document.getElementById('schoolModal'));
                myModal.show();
            });

            // delete
            $(document).on('click', '.deleteBtn', function() {
                var row = $(this).closest('tr');
                var schoolId = row.data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data sekolah ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/delete-school/' + schoolId,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', 'Sekolah telah dihapus.', 'success');
                                    row.remove();
                                } else {
                                    Swal.fire('Gagal!', 'Terjadi kesalahan', 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Simpan atau perbarui data sekolah
            $('#schoolForm').submit(function(e) {
                e.preventDefault();
                var schoolId = $('#schoolId').val();
                var schoolName = $('#school_name').val();
                var address = $('#address').val();
                var url = schoolId ? '/update-school/' + schoolId : '/add-school';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        school_name: schoolName,
                        address: address
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: schoolId ? 'Sekolah Diperbarui!' : 'Sekolah Ditambahkan!',
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
