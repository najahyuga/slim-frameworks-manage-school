<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
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
                    <a class="nav-link link-light" href="/admin">Manajemen Admin</a>
                </li>
                <li class="nav-item">
                <a id="logoutButton" class="nav-link link-dark" href="#">Logout</a>
                </li>
            </ul>
        </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Dashboard</h2>

        <?php if (!empty($successMessage)): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: '<?= $successMessage ?>',
                });
            </script>
        <?php endif; ?>

        <div class="row text-center my-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Sekolah</h5>
                        <p class="card-text display-4"><?= $schools ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Siswa</h5>
                        <p class="card-text display-4"><?= $students ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Jumlah Siswa per Sekolah</h3>
                <canvas id="studentChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Data untuk Chart
        var labels = <?= $labels ?>;
        var data = <?= $data ?>;

        // Render Chart
        var ctx = document.getElementById('studentChart').getContext('2d');
        var studentChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: labels, // Label (nama sekolah)
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: data, // Data (jumlah siswa)
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
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
