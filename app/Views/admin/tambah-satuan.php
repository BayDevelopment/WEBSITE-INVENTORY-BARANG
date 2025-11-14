<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="container py-5">
    <div class="text-start mb-5">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <?= esc($breadcrumb) ?>
                </h2>
                <p class="text-muted mb-0">Silakan isi data satuan dengan lengkap di bawah ini</p>
            </div>

            <a href="<?= base_url('admin/data-satuan') ?>"
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

                    <form action="<?= base_url('admin/data-satuan/tambah') ?>" method="post" class="needs-validation" novalidate>
                        <?= csrf_field() ?>

                        <!-- 🔹 Nama Satuan -->
                        <div class="form-floating mb-3">
                            <input type="text"
                                name="nama_satuan"
                                id="nama_satuan"
                                class="form-control <?= ($validation->hasError('nama_satuan') ? 'is-invalid' : '') ?>"
                                placeholder="Nama Barang"
                                value="<?= old('nama_satuan') ?>"
                                required>
                            <label for="nama_satuan">
                                <i class="fa-solid fa-box me-2 text-primary"></i>Nama Satuan
                            </label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('nama_satuan') ?: 'Nama barang wajib diisi' ?>
                            </div>
                        </div>

                        <!-- 🔹 Keterangan -->
                        <div class="form-floating mb-4">
                            <textarea name="keterangan"
                                id="keterangan"
                                class="form-control <?= ($validation->hasError('keterangan') ? 'is-invalid' : '') ?>"
                                placeholder="Keterangan Barang"
                                style="height: 100px"><?= old('keterangan') ?></textarea>
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