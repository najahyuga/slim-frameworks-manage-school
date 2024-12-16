<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
        <h2>Manajemen Siswa</h2>
        
        <button class="btn btn-success mb-3" id="addStudentBtn">Tambah Siswa</button>
        
        <table id="studentTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th>Email</th>
                    <th>Sekolah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $student): ?>
                <tr data-id="<?= $student['id'] ?>">
                    <td><?= $student['id'] ?></td>
                    <td><?= $student['name'] ?></td>
                    <td><?= $student['email'] ?></td>
                    <td>
                        <?php
                            // Memastikan $schools adalah array sebelum di-filter
                            if (is_array($schools) && !empty($schools)) {
                                // Cari sekolah berdasarkan id_school milik siswa
                                $school = array_filter($schools, function($school) use ($student) {
                                    return $school['id'] == $student['id_school'];
                                });

                                if ($school) {
                                    echo reset($school)['school_name'];
                                } else {
                                    echo 'Sekolah tidak ditemukan';
                                }
                            } else {
                                echo 'Sekolah tidak ditemukan';
                            }
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-warning editBtn" data-id_school="<?= $student['id_school'] ?>">Edit</button>
                        <button class="btn btn-danger deleteBtn">Hapus</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="studentModalLabel">Tambah Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="studentForm">
                            <input type="hidden" id="studentId">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Siswa</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="id_school" class="form-label">Sekolah</label>
                                <select class="form-select" id="id_school" required>
                                    <option value="">Pilih Sekolah</option>
                                    <?php foreach ($schools as $school): ?>
                                        <option value="<?= $school['id'] ?>"><?= $school['school_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#studentTable').DataTable();

            // Initialize Select2 for search feature in the dropdown
            $('#id_school').select2({
                placeholder: 'Pilih Sekolah',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#studentModal')
            });

            // Add student
            $('#addStudentBtn').click(function() {
                $('#studentForm')[0].reset();
                $('#id_school').val(null).trigger('change');
                $('#studentModalLabel').text('Tambah Siswa');
                $('#saveBtn').text('Simpan');
                $('#studentId').val('');
                var myModal = new bootstrap.Modal(document.getElementById('studentModal'));
                myModal.show();
            });


            // Edit student
            $(document).on('click', '.editBtn', function() {
                var row = $(this).closest('tr');
                var studentId = row.data('id');
                var studentName = row.find('td:eq(1)').text();
                var email = row.find('td:eq(2)').text();
                var idSchool = $(this).data('id_school');

                $('#studentId').val(studentId);
                $('#name').val(studentName);
                $('#email').val(email);
                $('#id_school').val(idSchool).trigger('change'); // Set Select2 value
                $('#studentModalLabel').text('Edit Siswa');
                $('#saveBtn').text('Perbarui');
                var myModal = new bootstrap.Modal(document.getElementById('studentModal'));
                myModal.show();
            });

            // Delete student
            $(document).on('click', '.deleteBtn', function() {
                var row = $(this).closest('tr');
                var studentId = row.data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data siswa ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/delete-student/' + studentId,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', 'Siswa telah dihapus.', 'success');
                                    row.remove();
                                } else {
                                    Swal.fire('Gagal!', 'Terjadi kesalahan', 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Save or update student
            $('#studentForm').submit(function(e) {
                e.preventDefault();

                var idSchool = $('#id_school').val();
                if (!idSchool) {
                    Swal.fire('Error!', 'Harap pilih sekolah terlebih dahulu!', 'error');
                    return;
                }

                var studentId = $('#studentId').val();
                var studentName = $('#name').val();
                var email = $('#email').val();
                var url = studentId ? '/update-student/' + studentId : '/add-student';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        name: studentName,
                        email: email,
                        id_school: idSchool
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: studentId ? 'Siswa Diperbarui!' : 'Siswa Ditambahkan!',
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
