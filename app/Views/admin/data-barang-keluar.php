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
    <div class="text-start mb-5">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <?= esc($breadcrumb) ?>
                </h2>
                <p class="text-muted mb-2">Kelola data Barang Keluar dibawah ini</p>

                <?php if (!empty($d_barangMasuk)): ?>
                    <a href="<?= base_url('admin/data-barang-masuk/tambah') ?>"
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
                <h5 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Barang Keluar</h5>

                <form method="get" action="<?= base_url('admin/data-barang-masuk') ?>" class="row g-2 align-items-end">

                    <div class="col-md-4">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <select name="nama_barang" id="nama_barang" class="form-select">
                            <option value="">-- Semua Barang --</option>
                            <?php foreach ($list_nama_barang as $item): ?>
                                <option value="<?= esc($item['nama_barang']) ?>"
                                    <?= ($item['nama_barang'] == $filter_nama) ? 'selected' : '' ?>>
                                    <?= esc($item['nama_barang']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="keyword" class="form-label">Kata Kunci</label>
                        <input type="text" name="keyword" id="keyword" class="form-control"
                            placeholder="Cari nama atau keterangan..."
                            value="<?= esc($filter_keyword) ?>">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>

                    <div class="col-md-2 d-grid">
                        <a href="<?= base_url('admin/data-barang-keluar') ?>" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i>Reset
                        </a>
                    </div>

                </form>

            </div>

            <?php if (!empty($d_barangKeluar)): ?>
                <div class="card shadow-sm">
                    <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
                        <table id="tableBarangMasuk" class="table table-striped table-hover align-middle text-capitalize mb-0" style="width: 100%;">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th scope="col" style="width:5%;">No</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Tanggal Masuk</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" style="width:10%;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($d_barangKeluar as $row): ?>
                                    <tr>
                                        <th scope="row" class="text-center"><?= $no++ ?>.</th>

                                        <td><?= esc($row['nama_barang']) ?></td>
                                        <td><?= esc($row['jumlah']) ?></td>
                                        <td><?= esc($row['tanggal_keluar']) ?></td>

                                        <td><?= esc($row['user']) ?></td>
                                        <td><?= esc($row['status']) ?></td>

                                        <td class="text-center">
                                            <a href="<?= base_url('admin/data-barang-keluar/edit/' . $row['id_barang_keluar']) ?>" class="btn btn-sm btn-primary rounded-pill px-3"><span><i class="fa-solid fa-pen-to-square"></i></span> Edit</a>
                                            <a href="javascript:void(0)" onclick="confirmDeleteBarangMasuk('<?= $row['id_barang_keluar'] ?>')" class="btn btn-sm btn-danger rounded-pill px-3" title="Hapus">
                                                <span><i class="fa-solid fa-trash"></i></span> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <img src="<?= base_url('assets/img/empty.jpg') ?>"
                        alt="Data Kosong"
                        class="img-fluid empty-image"
                        style="max-width: 350px; height: auto; opacity: 0.85;">
                    <p class="text-muted mt-3 fs-5">Belum ada data Barang Masuk yang tersedia.</p>
                    <a href="<?= base_url('admin/data-barang-keluar/tambah') ?>" class="btn btn-dark btn-sm rounded-pill py-2 text-capitalize"><span><i class="fa-solid fa-file-circle-plus"></i></span> tambah barang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    window.confirmDeleteBarangMasuk = function(id) {
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
                window.location.href = "<?= base_url('admin/data-barang-keluar/hapus/') ?>" + encodeURIComponent(id);
            }
        });
    };
</script>
<?= $this->endSection() ?>