<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="container py-5">
    <div class="text-start mb-5">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <?= esc($breadcrumb) ?>
                </h2>
                <p class="text-muted mb-0">Silakan isi data barang dengan lengkap di bawah ini</p>
            </div>

            <a href="<?= base_url('admin/data-barang') ?>"
                class="btn btn-outline-secondary btn-sm rounded-3 shadow-sm">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>


    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <?php
                    $validation = \Config\Services::validation();
                    ?>

                    <form action="<?= base_url('admin/data-barang/edit/' . esc($editData['id_barang'])) ?>"
                        method="post"
                        class="needs-validation"
                        novalidate>

                        <?= csrf_field() ?>

                        <!-- 🔹 Kode Barang -->
                        <div class="form-floating mb-3">
                            <input type="text"
                                id="kode_barang"
                                class="form-control"
                                value="<?= esc($editData['kode_barang']) ?>"
                                placeholder="Kode Barang"
                                disabled readonly>
                            <label for="kode_barang">
                                <i class="fa-solid fa-barcode me-2 text-primary"></i>Kode Barang
                            </label>
                            <div class="form-text text-muted">
                                Kode barang dibuat otomatis oleh sistem.
                            </div>
                        </div>

                        <!-- 🔹 Nama Barang -->
                        <div class="form-floating mb-3">
                            <input type="text"
                                name="nama_barang"
                                id="nama_barang"
                                class="form-control <?= $validation->hasError('nama_barang') ? 'is-invalid' : '' ?>"
                                placeholder="Nama Barang"
                                value="<?= old('nama_barang', esc($editData['nama_barang'])) ?>"
                                required>
                            <label for="nama_barang">
                                <i class="fa-solid fa-box me-2 text-primary"></i>Nama Barang
                            </label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('nama_barang') ?: 'Nama barang wajib diisi' ?>
                            </div>
                        </div>

                        <!-- 🔹 Satuan -->
                        <div class="form-floating mb-3">
                            <select id="satuan"
                                name="id_satuan"
                                class="form-select <?= $validation->hasError('id_satuan') ? 'is-invalid' : '' ?>"
                                <?= empty($satuanList) ? 'disabled' : 'required' ?>>

                                <?php if (!empty($satuanList)): ?>
                                    <option value="" disabled>-- Pilih Satuan --</option>

                                    <?php foreach ($satuanList as $satuan): ?>
                                        <option value="<?= esc($satuan['id_satuan']) ?>"
                                            <?= old('id_satuan', $editData['id_satuan']) == $satuan['id_satuan'] ? 'selected' : '' ?>>
                                            <?= esc($satuan['nama_satuan']) ?>
                                        </option>
                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <option value="" selected disabled>
                                        ⚠️ Silakan isi data satuan terlebih dahulu
                                    </option>
                                <?php endif; ?>

                            </select>

                            <label for="satuan">
                                <i class="fa-solid fa-ruler-combined me-2 text-primary"></i>Satuan
                            </label>

                            <?php if (!empty($satuanList)): ?>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('id_satuan') ?: 'Pilih satuan barang' ?>
                                </div>
                            <?php else: ?>
                                <div class="form-text text-danger fst-italic">
                                    Tidak ada data satuan. Silakan tambahkan satuan di menu <strong>Master Satuan</strong>.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 🔹 Stok -->
                        <div class="form-floating mb-4">
                            <input type="number"
                                name="stok"
                                id="stok"
                                class="form-control <?= $validation->hasError('stok') ? 'is-invalid' : '' ?>"
                                placeholder="Stok Barang"
                                min="0"
                                value="<?= old('stok', esc($editData['stok'])) ?>"
                                required>
                            <label for="stok">
                                <i class="fa-solid fa-layer-group me-2 text-primary"></i>Stok Barang
                            </label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('stok') ?: 'Stok wajib diisi' ?>
                            </div>
                        </div>

                        <!-- 🔹 Keterangan -->
                        <div class="form-floating mb-4">
                            <textarea name="keterangan"
                                id="keterangan"
                                class="form-control <?= $validation->hasError('keterangan') ? 'is-invalid' : '' ?>"
                                placeholder="Keterangan Barang"
                                style="height: 100px"><?= old('keterangan', esc($editData['keterangan'])) ?></textarea>
                            <label for="keterangan">
                                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Keterangan (Opsional)
                            </label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('keterangan') ?>
                            </div>
                        </div>

                        <!-- 🔹 Tombol -->
                        <div class="d-grid g-4">
                            <button type="submit" class="btn btn-primary btn-md rounded-3">
                                <i class="fa-solid fa-file-circle-plus me-2"></i>Simpan Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script validasi Bootstrap -->
<script>
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<?= $this->endSection() ?>