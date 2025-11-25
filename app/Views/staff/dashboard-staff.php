<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4 fw-semibold text-primary"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>
    <style>
        .card {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(255, 255, 255, 0.1);
            background-color: #1a1a1a;
        }

        .card-body {
            position: relative;
            padding: 2rem;
        }

        .jumlah {
            font-size: 2.8rem;
            font-weight: 700;
            color: green;
            margin-top: 0.8rem;
            display: block;
        }

        .card-footer a {
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
    </style>

    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <span class="d-block mb-2">
                        <i class="fa-solid fa-box me-2"></i>
                        Barang Masuk
                    </span>
                    <span class="jumlah text-success">
                        <?= $totalBarangMasuk ?>
                    </span>
                    <i class="fa-solid fa-arrow-trend-up fs-4 opacity-75 text-success"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a class="small text-white stretched-link" href="<?= base_url('admin/data-barang-masuk') ?>">Lihat Detail</a>
                    <?php else: ?>
                        <a class="small text-white stretched-link" href="<?= base_url('staff/data-barang-masuk') ?>">Lihat Detail</a>
                    <?php endif; ?>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <span class="d-block mb-2">
                        <i class="fa-solid fa-arrow-up-from-bracket me-2"></i>
                        Barang Keluar
                    </span>
                    <span class="jumlah text-danger">
                        <?= $totalBarangKeluar ?>
                    </span>
                    <i class="fa-solid fa-arrow-trend-down fs-4 opacity-75 text-danger"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a class="small text-white stretched-link" href="<?= base_url('staff/data-barang-keluar') ?>">Lihat Detail</a>
                    <?php else: ?>
                        <a class="small text-white stretched-link" href="<?= base_url('staff/data-barang-keluar') ?>">Lihat Detail</a>
                    <?php endif; ?>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card bg-dark text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Statistik Barang Masuk & Keluar</h5>
                    <canvas id="barangChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('barangChart').getContext('2d');

    const barangChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Barang Masuk', 'Barang Keluar'],
            datasets: [{
                label: 'Bar',
                data: [<?= $totalBarangMasuk ?>, <?= $totalBarangKeluar ?>],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2,
                borderRadius: 6, // modern rounded bar
                hoverBackgroundColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed.y} unit`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255,255,255,0.1)',
                        borderColor: 'rgba(255,255,255,0.2)'
                    },
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Jumlah',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Jenis Barang',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
</script>

<?= $this->endSection() ?>