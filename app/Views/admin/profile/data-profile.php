<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="text-start mb-5">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h2 class="fw-bold text-primary mb-1"><?= esc($breadcrumb) ?></h2>
            <p class="text-muted mb-0">Silakan cek <?= esc($breadcrumb) ?> dengan lengkap di bawah ini</p>
        </div>

        <a href="<?= base_url('admin/dashboard') ?>"
            class="btn btn-outline-secondary btn-sm rounded-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<style>
    .password-toggle {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        font-size: 1rem;
        z-index: 2;
    }

    .password-toggle:hover {
        color: #0d6efd;
    }

    .profile-wrapper {
        max-width: 400px;
        margin: 0 auto;
    }

    .list-group-modern .list-group-item {
        border: none;
        border-radius: 0.75rem;
        margin-bottom: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: default;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
    }

    .list-group-modern .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .list-group-modern .list-group-item:last-child {
        margin-bottom: 0;
    }

    /* Sidebar profil agar ngambang saat discroll */
    .profile-sticky {
        position: sticky;
        top: 70px;
        /* margin top ketika scroll */
        z-index: 10;
    }

    /* Responsif: pada layar kecil sticky dimatikan agar tidak mengganggu layout */
    @media (max-width: 992px) {
        .profile-sticky {
            position: static;
            top: auto;
        }
    }

    /* Profile Card Modern */
    .profile-card {
        border: none;
        border-radius: 20px;
        padding: 30px 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        text-align: center;
        background: #fff;
    }

    .profile-card img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 15px;
        border: 4px solid #0d6efd;
        padding: 3px;
    }

    .profile-card h1 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .profile-card h3 {
        font-size: 15px;
        color: #555;
        margin: 2px 0;
        font-weight: 500;
    }

    /* Tab Card */
    .tab-card {
        border: none;
        border-radius: 20px;
        padding: 25px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    /* Pills Style */
    .nav-pills .nav-link {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 18px;
        background: #e9ecef;
        /* abu-abu soft */
        color: #495057;
        transition: 0.25s ease-in-out;
    }

    .nav-pills .nav-link:hover {
        background: #d4d7da;
        color: #212529;
    }

    .nav-pills .nav-link.active {
        background: #0d6efd !important;
        color: #fff !important;
        box-shadow: 0 3px 10px rgba(13, 110, 253, 0.25);
    }
</style>

<div class="container-fluid px-4">
    <div class="row g-4">

        <!-- Profile Left -->
        <div class="col-lg-4 col-md-12">
            <div class="profile-wrapper p-4">

                <!-- Profile Avatar -->
                <div class="text-center mb-3">
                    <img src="<?= base_url('assets/img/icons8-user-100.png') ?>"
                        alt="User Avatar"
                        class="rounded-circle border border-2 border-primary"
                        style="width:100px; height:100px; object-fit:cover;">
                </div>

                <!-- User Info List -->
                <ul class="list-group list-group-modern shadow-sm rounded-4">
                    <li class="list-group-item">
                        <strong>Username:</strong> <?= esc(session()->get('username')) ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Nama Lengkap:</strong> <?= esc(session()->get('nama_lengkap')) ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong> <?= esc(session()->get('email')) ?>
                    </li>
                    <li class="list-group-item">
                        <strong>No Telepon:</strong> <?= esc(session()->get('no_telp')) ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Status Akun:</strong>
                        <span class="badge <?= session()->get('status_aktif') === 'aktif' ? 'bg-success' : 'bg-danger' ?>">
                            <i class="fa-solid fa-check-circle text-white me-2 text-capitalize"></i>
                            <?= esc(session()->get('status_aktif')) ?>
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        <!-- Tabs Right -->
        <div class="col-lg-8 col-md-12">
            <div class="tab-card">

                <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-home" type="button">Akun</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="pills-keamanan-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-keamanan" type="button">Keamanan</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <?php
                    $validation = \Config\Services::validation();

                    ?>

                    <!-- Tab Akun -->
                    <div class="tab-pane fade show active" id="pills-home">
                        <div class="p-3">
                            <?php $validation = \Config\Services::validation(); ?>
                            <form action="<?= base_url('admin/update-profile') ?>" method="post" class="needs-validation" novalidate>
                                <?= csrf_field() ?>

                                <!-- Username -->
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        name="username"
                                        id="username"
                                        class="form-control <?= ($validation->hasError('username') ? 'is-invalid' : '') ?>"
                                        placeholder="Username"
                                        value="<?= old('username', session()->get('username')) ?>"
                                        required>
                                    <label for="username">
                                        <i class="fa-solid fa-user me-2 text-primary"></i>Username
                                    </label>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('username') ?: 'Username wajib diisi' ?>
                                    </div>
                                </div>

                                <!-- Nama Lengkap -->
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        name="nama_lengkap"
                                        id="nama_lengkap"
                                        class="form-control <?= ($validation->hasError('nama_lengkap') ? 'is-invalid' : '') ?>"
                                        placeholder="Nama Lengkap"
                                        value="<?= old('nama_lengkap', session()->get('nama_lengkap')) ?>"
                                        required>
                                    <label for="nama_lengkap">
                                        <i class="fa-solid fa-id-card me-2 text-primary"></i>Nama Lengkap
                                    </label>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('nama_lengkap') ?: 'Nama lengkap wajib diisi' ?>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-floating mb-3">
                                    <input type="email"
                                        name="email"
                                        id="email"
                                        class="form-control <?= ($validation->hasError('email') ? 'is-invalid' : '') ?>"
                                        placeholder="Email"
                                        value="<?= old('email', session()->get('email')) ?>"
                                        required>
                                    <label for="email">
                                        <i class="fa-solid fa-envelope me-2 text-primary"></i>Email
                                    </label>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('email') ?: 'Email wajib diisi' ?>
                                    </div>
                                </div>

                                <!-- No Telepon -->
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        name="no_telp"
                                        id="no_telp"
                                        class="form-control <?= ($validation->hasError('no_telp') ? 'is-invalid' : '') ?>"
                                        placeholder="No Telepon"
                                        value="<?= old('no_telp', session()->get('no_telp')) ?>"
                                        required>
                                    <label for="no_telp">
                                        <i class="fa-solid fa-phone me-2 text-primary"></i>No Telepon
                                    </label>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('no_telp') ?: 'Nomor telepon wajib diisi' ?>
                                    </div>
                                </div>

                                <!-- Role (readonly) -->
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        class="form-control text-capitalize"
                                        value="<?= esc(session()->get('role')) ?>"
                                        readonly>
                                    <label for="role">
                                        <i class="fa-solid fa-user-shield me-2 text-primary"></i>Role
                                    </label>
                                </div>

                                <!-- Tombol -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-md rounded-3">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <!-- Tab Keamanan -->
                    <div class="tab-pane fade" id="pills-keamanan">
                        <div class="p-3">
                            <?php $validation = \Config\Services::validation(); ?>
                            <form method="post" action="<?= base_url('admin/change-password') ?>" class="needs-validation" novalidate>
                                <?= csrf_field() ?>

                                <!-- Status Akun (readonly) -->
                                <div class="form-floating mb-3">
                                    <input type="text"
                                        id="status"
                                        class="form-control text-capitalize"
                                        placeholder="Status Akun"
                                        value="<?= esc(session()->get('status_aktif')) ?>"
                                        readonly>
                                    <label for="status">
                                        <i class="fa-solid fa-circle-check me-2 text-primary"></i>Status Akun
                                    </label>
                                </div>

                                <div class="form-floating mb-3 position-relative">
                                    <input type="password"
                                        name="current_password"
                                        id="current_password"
                                        class="form-control <?= ($validation->hasError('current_password') ? 'is-invalid' : '') ?>"
                                        placeholder="Password Saat Ini"
                                        required>
                                    <label for="current_password">
                                        <i class="fa-solid fa-lock me-2 text-primary"></i>Password Saat Ini
                                    </label>
                                    <span class="password-toggle" onclick="togglePassword('current_password')">
                                        <i class="fa-solid fa-eye" id="icon_current_password"></i>
                                    </span>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('current_password') ?: 'Password saat ini wajib diisi' ?>
                                    </div>
                                </div>

                                <div class="form-floating mb-3 position-relative">
                                    <input type="password"
                                        name="new_password"
                                        id="new_password"
                                        class="form-control <?= ($validation->hasError('new_password') ? 'is-invalid' : '') ?>"
                                        placeholder="Password Baru"
                                        oninput="checkPasswordStrength()"
                                        required>
                                    <label for="new_password">
                                        <i class="fa-solid fa-key me-2 text-primary"></i>Password Baru
                                    </label>
                                    <span class="password-toggle" onclick="togglePassword('new_password')">
                                        <i class="fa-solid fa-eye" id="icon_new_password"></i>
                                    </span>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('new_password') ?: 'Password baru wajib diisi' ?>
                                    </div>

                                    <!-- Password Strength -->
                                    <div class="mt-2">
                                        <div class="progress" style="height: 6px;">
                                            <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                        </div>
                                        <small id="passwordStrengthText" class="form-text text-muted"></small>
                                    </div>
                                </div>


                                <div class="form-floating mb-3 position-relative">
                                    <input type="password"
                                        name="confirm_password"
                                        id="confirm_password"
                                        class="form-control <?= ($validation->hasError('confirm_password') ? 'is-invalid' : '') ?>"
                                        placeholder="Konfirmasi Password Baru"
                                        required>
                                    <label for="confirm_password">
                                        <i class="fa-solid fa-key me-2 text-primary"></i>Konfirmasi Password Baru
                                    </label>
                                    <span class="password-toggle" onclick="togglePassword('confirm_password')">
                                        <i class="fa-solid fa-eye" id="icon_confirm_password"></i>
                                    </span>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('confirm_password') ?: 'Konfirmasi password wajib diisi' ?>
                                    </div>
                                </div>


                                <!-- Tombol Submit -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-md rounded-3">
                                        <i class="fa-solid fa-key me-2"></i>Ganti Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById('icon_' + fieldId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function checkPasswordStrength() {
        const password = document.getElementById('new_password').value;
        const bar = document.getElementById('passwordStrengthBar');
        const text = document.getElementById('passwordStrengthText');

        let strength = 0;

        if (password.length >= 6) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[\W]/.test(password)) strength += 1;

        // Update progress bar
        const width = (strength / 5) * 100;
        bar.style.width = width + '%';

        // Update color & text
        if (strength <= 2) {
            bar.className = 'progress-bar bg-danger';
            text.textContent = 'Lemah';
            text.style.color = '#dc3545';
        } else if (strength === 3 || strength === 4) {
            bar.className = 'progress-bar bg-warning';
            text.textContent = 'Sedang';
            text.style.color = '#ffc107';
        } else if (strength === 5) {
            bar.className = 'progress-bar bg-success';
            text.textContent = 'Kuat';
            text.style.color = '#198754';
        }
    }
</script>
<?= $this->endSection() ?>