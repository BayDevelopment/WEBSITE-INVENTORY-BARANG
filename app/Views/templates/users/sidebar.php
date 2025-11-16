<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link mb-2 custom-link <?= $navlink === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">Master</div>
                <a class="nav-link mb-2 custom-link <?= $navlink === 'barang' ? 'active' : '' ?>" href="<?= base_url('admin/data-barang') ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                    Data Barang
                </a>
                <a class="nav-link mb-2 custom-link <?= $navlink === 'barang masuk' ? 'active' : '' ?>" href="<?= base_url('admin/data-barang-masuk') ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-down"></i></div>
                    Barang Masuk
                </a>
                <a class="nav-link mb-2 custom-link" href="<?= base_url() ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-arrow-circle-up"></i></div>
                    Barang Keluar
                </a>
                <a class="nav-link mb-2 custom-link" href="<?= base_url() ?>">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    Laporan Barang
                </a>
            </div>

            <!-- Card Satuan -->
            <div class="card bg-dark border-0 shadow-native mt-4 mx-3 rounded-4 mb-2">
                <div class="card-body py-3 px-3 d-flex align-items-center">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-ruler-combined fa-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold text-white">Kelola Satuan</h6>
                        <p class="small text-muted mb-2">Lihat & ubah data satuan</p>
                        <a href="<?= base_url('admin/data-satuan') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                            <i class="fa-solid fa-gear me-1"></i> Buka
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?php if (session()->get('role') === 'admin'): ?>
                <span class="fw-bold text-white">Admin</span>
            <?php elseif (session()->get('role') === 'staff_gudang'): ?>
                <span class="fw-bold text-white">Staff Gudang</span>
            <?php else: ?>
                <span class="fw-bold text-muted">Unknown</span>
            <?php endif; ?>

        </div>
    </nav>
</div>