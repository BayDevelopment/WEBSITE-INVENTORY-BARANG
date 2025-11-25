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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Buttons CSS (Export Excel, PDF, Print) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- JSZip (Wajib untuk Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


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

        // DataTables Satuan
        $(document).ready(function() {
            $('#tableSatuan').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Data_Satuan',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-md'
                }],

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    },
                    zeroRecords: "Tidak ada data yang cocok",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ data)"
                },

                columnDefs: [{
                        targets: [0, 1, 2, 3], // No & Aksi center
                        className: "text-center"
                    },
                    {
                        targets: 2, // Kolom Aksi
                        orderable: false,
                        width: "150px" // Lebarkan kolom aksi agar rapi
                    }
                ],

                order: [
                    [1, 'asc']
                ] // urut berdasarkan Nama Satuan
            });
        });

        // Datatables barang
        $(document).ready(function() {
            $('#tableBarang').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Data_Barang',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-md'
                }],

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    },
                    zeroRecords: "Tidak ada data yang cocok",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ data)"
                },

                columnDefs: [{
                        targets: [0, 3, 4, 5],
                        className: "text-center"
                    },
                    {
                        targets: 5, // kolom Aksi
                        width: "150px", // lebar kolom aksi agar rapi
                        className: "text-center"
                    },
                    {
                        targets: [0, 5],
                        orderable: false // No & Aksi tidak bisa sort
                    }
                ],

                order: [
                    [1, 'asc']
                ] // urut berdasarkan kolom Kode Barang
            });
        });

        // datatables barang masuk
        $(document).ready(function() {
            $('#tableBarangMasuk').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Data Barang Masuk',
                    text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
                    className: 'btn btn-success btn-md'
                }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    },
                    zeroRecords: "Tidak ada data yang cocok",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari total _MAX_ data)"
                },

                columnDefs: [{
                        // Kolom No, Jumlah, Tanggal, User, Status → center
                        targets: [0, 2, 3, 4, 5],
                        className: "text-center"
                    },

                    <?php if (session()->get('role') === 'admin'): ?> {
                            // Kolom No & Aksi tidak bisa di-sort
                            targets: [0, 6],
                            orderable: false
                        }
                    <?php else: ?> {
                            // Jika bukan admin, kolom No tidak bisa sort (karena kolom aksi tidak ada)
                            targets: [0],
                            orderable: false
                        }
                    <?php endif; ?>
                ],

                // Urutan default berdasarkan Nama Barang (kolom index 1)
                order: [
                    [1, 'asc']
                ]
            });
        });


        // datatables barang keluar
        $(document).ready(function() {
            $('#tableBarangKeluar').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Data Barang Masuk',
                    text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
                    className: 'btn btn-success btn-md'
                }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "›",
                        previous: "‹"
                    },
                    zeroRecords: "Tidak ada data yang cocok",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari total _MAX_ data)"
                },

                columnDefs: [{
                        // Kolom No, Jumlah, Tanggal, User, Status → center
                        targets: [0, 2, 3, 4, 5, 6],
                        className: "text-center"
                    },
                    {
                        // Kolom No dan Aksi tidak bisa di-sort
                        targets: [0, 6],
                        orderable: false
                    }
                ],

                // Urutan default berdasarkan Nama Barang (kolom index 1)
                order: [
                    [1, 'asc']
                ]
            });
        });

        // laporan data barang masuk dan keluar
        $(document).ready(function() {
            $('#tabelBarangMasukKeluar').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'Laporan_Barang_Masuk_Keluar',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-md'
                }]
            });
        });
    </script>

</body>

</html>