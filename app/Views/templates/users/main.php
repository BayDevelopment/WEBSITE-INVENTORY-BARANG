<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->include('templates/admin/header') ?>
</head>

<body class="sb-nav-fixed bg-light">
    <?= $this->include('templates/admin/navbar') ?>

    <div id="layoutSidenav">
        <?= $this->include('templates/admin/sidebar') ?>

        <div id="layoutSidenav_content">
            <main class="p-3">
                <?= $this->renderSection('MainContent') ?>
            </main>

            <footer class="py-3 bg-white border-top shadow-sm">
                <?= $this->include('templates/admin/footer') ?>
            </footer>

            <!-- Tempat untuk script tambahan -->
            <?= $this->renderSection('scripts') ?>
        </div>
    </div>

    <!-- JS Global -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
</body>

</html>