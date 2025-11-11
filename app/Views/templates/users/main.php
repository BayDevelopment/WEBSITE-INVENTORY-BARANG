<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->include('templates/users/header') ?>
</head>

<body class="sb-nav-fixed bg-light">
    <?php
    $s = session();
    $flashSuccess = $s->getFlashdata('success');
    $flashError   = $s->getFlashdata('error');
    $flashWarn    = $s->getFlashdata('logout');
    ?>

    <?= $this->include('templates/users/navbar') ?>

    <div id="layoutSidenav">
        <?= $this->include('templates/users/sidebar') ?>

        <div id="layoutSidenav_content">
            <main class="p-3">
                <?= $this->renderSection('MainContent') ?>
            </main>

            <?= $this->include('templates/users/footer') ?>
        </div>
    </div>
    <!-- ========== Modal Logout Modern ========== -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold text-primary" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3 text-secondary">Apakah Anda yakin ingin keluar dari akun ini?</p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <a href="<?= base_url('auth/logout'); ?>" class="btn btn-danger px-4">
                            <i class="fas fa-check me-1"></i> Ya, Logout
                        </a>
                    </div>
                </div>
                <div class="modal-footer border-0"></div>
            </div>
        </div>
    </div>

    <!-- sweealert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JS Global -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
    <!-- Tempat untuk script tambahan -->
    <?= $this->renderSection('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const msgSuccess = JSON.parse('<?= json_encode($flashSuccess ?? null) ?>');
            const msgError = JSON.parse('<?= json_encode($flashError ?? null) ?>');
            const msgWarn = JSON.parse('<?= json_encode($flashWarn ?? null) ?>');

            if (!msgSuccess && !msgError && !msgWarn) return;

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (t) => {
                    t.onmouseenter = Swal.stopTimer;
                    t.onmouseleave = Swal.resumeTimer;
                }
            });

            if (msgSuccess) Toast.fire({
                icon: "success",
                title: msgSuccess
            });
            if (msgError) Toast.fire({
                icon: "error",
                title: msgError
            });
            if (msgWarn) Toast.fire({
                icon: "warning",
                title: msgWarn
            });
        });
    </script>

</body>

</html>