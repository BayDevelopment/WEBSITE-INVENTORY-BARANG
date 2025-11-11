<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= esc($breadcrumb) ?></h1>
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
                        <?= $totalMasuk ?? 128 ?>
                    </span>
                    <i class="fa-solid fa-arrow-trend-up fs-4 opacity-75 text-success"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Lihat Detail</a>
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
                        <?= $totalKeluar ?? 64 ?>
                    </span>
                    <i class="fa-solid fa-arrow-trend-down fs-4 opacity-75 text-danger"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>


</div>
<?= $this->endSection() ?>