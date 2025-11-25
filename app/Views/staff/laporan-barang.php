<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="container-fluid px-4">

    <style>
        .empty-image {
            display: block;
            margin: 40px auto;
            mix-blend-mode: multiply;
            filter: brightness(0.95) contrast(1.1);
        }

        #tabelBarangMasukKeluar td:last-child {
            width: 150px !important;
            white-space: nowrap;
        }

        .dataTables_wrapper .dataTables_scrollHead table {
            margin-bottom: 0 !important;
        }

        .dataTables_wrapper .dataTables_scrollBody table {
            margin-bottom: 0 !important;
        }
    </style>

    <!-- HEADER -->
    <div class="text-start mb-4">
        <h2 class="fw-bold text-primary mb-1"><?= esc($breadcrumb) ?></h2>
        <p class="text-muted mb-0">Laporan Barang Masuk & Keluar</p>
    </div>

    <!-- FILTER -->
    <div class="card shadow-sm p-3 mb-3">
        <h5 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>

        <form method="get" action="<?= base_url('staff/laporan-barang') ?>" class="row g-2 align-items-end">

            <!-- Kategori -->
            <div class="col-md-2">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <option value="Barang Masuk" <?= ($filter_kategori == 'Barang Masuk') ? 'selected' : '' ?>>Barang Masuk</option>
                    <option value="Barang Keluar" <?= ($filter_kategori == 'Barang Keluar') ? 'selected' : '' ?>>Barang Keluar</option>
                </select>
            </div>

            <!-- Status -->
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="disetujui" <?= ($filter_status == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= ($filter_status == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                    value="<?= esc($filter_tanggal ?? '') ?>">
            </div>

            <!-- Keyword -->
            <div class="col-md-2">
                <label class="form-label">Kata Kunci</label>
                <input type="text" name="keyword" class="form-control"
                    placeholder="Cari nama atau keterangan..."
                    value="<?= esc($filter_keyword) ?>">
            </div>

            <!-- Tombol -->
            <div class="col-md-2 d-grid mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>Cari
                </button>
            </div>

            <div class="col-md-2 d-grid mt-3">
                <a href="<?= base_url('staff/laporan-barang') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- JIKA SUDAH SEARCH & ADA DATA -->
    <?php if ($is_search && !empty($hasil)): ?>

        <div class="card shadow-sm">
            <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
                <table id="tabelBarangMasukKeluar"
                    class="table table-striped table-hover align-middle text-capitalize mb-0">

                    <thead class=" text-center">
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>User</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1;
                        foreach ($hasil as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?>.</td>
                                <td><?= esc($row['kategori']) ?></td>
                                <td><?= esc($row['nama_barang']) ?></td>
                                <td><?= esc($row['jumlah']) ?></td>

                                <td class="text-capitalize">
                                    <?php if ($row['status'] == 'disetujui'): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php elseif ($row['status'] == 'ditolak'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <?= esc($row['status']) ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= esc(indo_full_date($row['tanggal_masuk'] ?? $row['tanggal_keluar'])) ?>
                                </td>

                                <td><?= esc($row['keterangan'] ?? '-') ?></td>
                                <td><?= esc($row['user']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <!-- JIKA SUDAH SEARCH TAPI DATA KOSONG -->
    <?php elseif ($is_search && empty($hasil)): ?>

        <div class="text-center py-5">
            <img src="<?= base_url('assets/img/empty.jpg') ?>" class="empty-image" style="max-width: 300px;">
            <p class="text-muted mt-3 fs-5">Tidak ada data sesuai filter.</p>
        </div>

        <!-- HALAMAN AWAL (BELUM SEARCH) -->
    <?php else: ?>

        <div class="text-center py-5">
            <img src="<?= base_url('assets/img/empty.jpg') ?>" class="empty-image" style="max-width: 300px;">
            <p class="text-muted mt-3 fs-5">Gunakan filter di atas untuk melihat laporan.</p>
        </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>