<!-- ======= TOP NAVBAR ======= -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark shadow-sm">
    <!-- Brand -->
    <a class="navbar-brand" href="<?= base_url('/'); ?>">
        <i class="fas fa-box-open text-primary"></i>
        <div class="brand-text">
            <strong>Inventory</strong>
            <small>PANEL</small>
        </div>
    </a>

    <!-- Sidebar Toggle -->
    <button class="btn btn-link btn-sm text-white order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fa-solid fa-gear"></i>
    </button>


    <!-- User Dropdown -->
    <ul class="navbar-nav ms-auto me-3 me-lg-4 align-items-center">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white fw-semibold"
                href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 animate__animated animate__fadeIn"
                aria-labelledby="navbarDropdown">
                <li>
                    <h6 class="dropdown-header text-muted">Akun Saya</h6>
                </li>
                <li>
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?= base_url('admin/profile'); ?>">
                            <i class="fas fa-cog text-primary"></i> Pengaturan
                        </a>
                    <?php else: ?>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?= base_url('staff/profile'); ?>">
                            <i class="fas fa-cog text-primary"></i> Pengaturan
                        </a>
                    <?php endif; ?>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#"
                        data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>