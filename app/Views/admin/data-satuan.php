<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="container-fluid px-4">
    <div class="text-start mb-5">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <?= esc($breadcrumb) ?>
                </h2>
                <p class="text-muted mb-2">Kelola data satuan dibawah ini</p>
                <?php if (!empty($d_satuan)): ?>
                    <a href="<?= base_url('admin/data-satuan/tambah') ?>"
                        class="btn btn-dark btn-sm rounded-pill px-3 py-2 text-capitalize">
                        <i class="fa-solid fa-file-circle-plus me-1"></i> Tambah
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm p-3 mb-3">
                <h5 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Data Satuan</h5>

                <form method="get" action="<?= base_url('admin/data-satuan') ?>" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="nama_satuan" class="form-label">Nama Satuan</label>
                        <select name="nama_satuan" id="nama_satuan" class="form-select">
                            <option value="">-- Semua Satuan --</option>
                            <?php foreach ($list_nama_satuan as $item): ?>
                                <option value="<?= esc($item['nama_satuan']) ?>"
                                    <?= ($selected_nama_satuan == $item['nama_satuan']) ? 'selected' : '' ?>>
                                    <?= esc($item['nama_satuan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="keyword" class="form-label">Kata Kunci</label>
                        <input type="text" name="keyword" id="keyword" class="form-control"
                            placeholder="Cari nama atau keterangan..."
                            value="<?= esc($keyword) ?>">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                    </div>

                    <div class="col-md-2 d-grid">
                        <a href="<?= base_url('admin/data-satuan') ?>" class="btn btn-secondary"><i class="fas fa-undo me-1"></i>Reset</a>
                    </div>
                </form>
            </div>

            <?php if (!empty($d_satuan)): ?>
                <div class="card p-3 shadow-sm">
                    <h4 class="mb-3">Daftar Satuan</h4>
                    <table id="tableSatuan" class="display nowrap table table-striped" style="width:100%">
                        <thead class=" text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama Satuan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($d_satuan as $satuan): ?>
                                <tr>
                                    <td><?= $no++ ?> .</td>
                                    <td><?= esc($satuan['nama_satuan']) ?></td>
                                    <td><?= esc($satuan['keterangan'] ?: 'Tidak Ada') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/data-satuan/edit/' . $satuan['id_satuan']) ?>" class="btn btn-sm btn-primary rounded-pill px-3"><span><i class="fa-solid fa-pen-to-square"></i></span> Edit</a>
                                        <a href="javascript:void(0)" onclick="confirmDeleteSatuan('<?= $satuan['id_satuan'] ?>')" class="btn btn-sm btn-danger rounded-pill px-3" title="Hapus">
                                            <span><i class="fa-solid fa-trash"></i></span> Hapus
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
                    <a href="<?= base_url('admin/data-satuan/tambah') ?>" class="btn btn-dark btn-sm rounded-pill py-2 text-capitalize"><span><i class="fa-solid fa-file-circle-plus"></i></span> tambah satuan</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    window.confirmDeleteSatuan = function(id) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // arahkan ke controller hapusActivity
                window.location.href = "<?= base_url('admin/data-satuan/hapus/') ?>" + encodeURIComponent(id);
            }
        });
    };
</script>
<?= $this->endSection() ?>