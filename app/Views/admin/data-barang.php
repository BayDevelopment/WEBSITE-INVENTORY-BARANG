<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="container-fluid px-4">
    <style>
        .empty-image {
            display: block;
            margin: 40px auto;
            background: transparent;
            /* pastikan tidak ada warna latar */
            mix-blend-mode: multiply;
            /* agar gambar menyatu dengan background */
            filter: brightness(0.95) contrast(1.1);
        }
    </style>
    <h1 class="mt-4"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <div class="row">
        <div class="col-12">
            <?php if (!empty($d_barang)): ?>
                <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
                    <table class="table table-striped table-hover align-middle text-capitalize mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th scope="col" style="width:5%;">No</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Stok</th>
                                <th scope="col" style="width:10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($d_barang as $row): ?>
                                <tr>
                                    <th scope="row" class="text-center"><?= $no++ ?>.</th>
                                    <td><?= esc($row['kode_barang']) ?></td>
                                    <td><?= esc($row['nama_barang']) ?></td>
                                    <td id="liveToastBtn" class="text-center">
                                        <?= esc($row['satuan']) ?>
                                        <div class="toast-container position-fixed bottom-0 end-0 p-3">
                                            <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                                <div class="toast-header">
                                                    <img src="<?= base_url('assets/img/toast-logo.jpg') ?>" class="rounded me-2" alt="IMG" width="30" height="30">
                                                    <strong class="me-auto"><?= esc($breadcrumb) ?></strong>
                                                    <small><?= esc($row['created_at']) ?></small>
                                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                                </div>
                                                <div class="toast-body">
                                                    <?= esc($row['keterangan']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= esc($row['stok']) ?></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <img src="<?= base_url('assets/img/empty.jpg') ?>"
                        alt="Data Kosong"
                        class="img-fluid empty-image"
                        style="max-width: 350px; height: auto; opacity: 0.85;">
                    <p class="text-muted mt-3 fs-5">Belum ada data barang yang tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>