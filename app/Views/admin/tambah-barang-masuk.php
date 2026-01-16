<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<div class="container py-5">
    <div class="text-start mb-5">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <?= esc($breadcrumb) ?>
                </h2>
                <p class="text-muted mb-0">Silakan isi data Barang Masuk dengan lengkap di bawah ini</p>
            </div>

            <a href="<?= base_url('admin/data-barang-masuk') ?>"
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

                    <form action="<?= base_url('admin/data-barang-masuk/tambah') ?>" method="post" class="needs-validation" novalidate>
                        <?= csrf_field() ?>

                        <!-- Dropdown Barang -->
                      <!-- Dropdown Barang -->
                        <div class="form-floating mb-3">
                            <select id="barang"
                                name="id_barang"
                                class="form-select <?= ($validation->hasError('id_barang') ? 'is-invalid' : '') ?>"
                                <?= empty($d_barang) ? 'disabled' : 'required' ?>>

                                <?php if (!empty($d_barang)): ?>
                                    <option value="" disabled
                                        <?= (empty(old('id_barang')) && empty($selectedId)) ? 'selected' : '' ?>>
                                        -- Pilih Barang --
                                    </option>

                                    <?php foreach ($d_barang as $b): ?>
                                        <?php
                                            // prioritas selected: old() dulu (kalau form error balik), kalau tidak ada baru selectedId dari scan
                                            $isSelected = old('id_barang')
                                                ? (old('id_barang') == $b['id_barang'])
                                                : (!empty($selectedId) && $selectedId == $b['id_barang']);
                                        ?>
                                        <option value="<?= esc($b['id_barang']) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= esc($b['nama_barang']) ?>
                                        </option>
                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <option value="" selected disabled>⚠️ Tidak ada data barang</option>
                                <?php endif; ?>

                            </select>

                            <label for="barang">
                                <i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Barang
                            </label>

                            <div class="invalid-feedback">
                                <?= $validation->getError('id_barang') ?: 'Pilih barang terlebih dahulu' ?>
                            </div>

                            <?php if (!empty($selectedBarang) && empty(old('id_barang'))): ?>
                                <small class="text-muted d-block mt-1">
                                    Terpilih otomatis dari scan: <strong><?= esc($selectedBarang['nama_barang']) ?></strong>
                                </small>
                            <?php endif; ?>
                        </div>


                        <!-- Jumlah -->
                        <div class="form-floating mb-3">
                            <input type="number"
                                name="jumlah"
                                id="jumlah"
                                class="form-control <?= ($validation->hasError('jumlah') ? 'is-invalid' : '') ?>"
                                placeholder="Jumlah"
                                value="<?= old('jumlah') ?>"
                                required>

                            <label for="jumlah">
                                <i class="fa-solid fa-box me-2 text-primary"></i>Jumlah
                            </label>

                            <div class="invalid-feedback">
                                <?= $validation->getError('jumlah') ?: 'Jumlah wajib diisi' ?>
                            </div>
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="form-floating mb-3">
                            <input type="date"
                                name="tanggal_masuk"
                                id="tanggal_masuk"
                                class="form-control <?= ($validation->hasError('tanggal_masuk') ? 'is-invalid' : '') ?>"
                                value="<?= old('tanggal_masuk') ?>"
                                required>

                            <label for="tanggal_masuk">
                                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>Tanggal Masuk
                            </label>

                            <div class="invalid-feedback">
                                <?= $validation->getError('tanggal_masuk') ?: 'Tanggal masuk wajib diisi' ?>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <select name="status" id="status"
                                class="form-select <?= ($validation->hasError('status') ? 'is-invalid' : '') ?>" required>

                                <option value="" disabled <?= empty($currentStatus) ? 'selected' : '' ?>>
                                    -- Pilih Status --
                                </option>

                                <?php
                                $statusList = ['menunggu', 'disetujui', 'ditolak'];
                                foreach ($statusList as $status):
                                ?>
                                    <option value="<?= $status ?>"
                                        <?= (!empty($currentStatus) && $currentStatus === $status) ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>


                            <label for="status">
                                <i class="fa-solid fa-flag-checkered me-2 text-primary"></i>Status
                            </label>

                            <div class="invalid-feedback">
                                <?= $validation->getError('status') ?: 'Status wajib dipilih.' ?>
                            </div>
                        </div>

                        <!-- Alasan Penolakan -->
                        <div id="alasanWrapper" class="form-floating mb-4 d-none">
                            <textarea name="alasan_penolakan"
                                id="alasan_penolakan"
                                class="form-control <?= ($validation->hasError('alasan_penolakan') ? 'is-invalid' : '') ?>"
                                placeholder="Masukkan alasan penolakan"
                                style="height: 100px"><?= old('alasan_penolakan') ?></textarea>

                            <label for="alasan_penolakan">
                                <i class="fa-solid fa-ban me-2 text-danger"></i>Alasan Penolakan
                            </label>

                            <div class="invalid-feedback">
                                <?= $validation->getError('alasan_penolakan') ?>
                            </div>
                        </div>


                        <!-- Keterangan -->
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

                        <!-- User Login (Disabled) -->
                        <div class="form-floating mb-3">
                            <input type="text"
                                class="form-control text-capitalize"
                                value="<?= esc(session()->get('role')) ?>"
                                disabled>

                            <label for="user">
                                <i class="fa-solid fa-user-shield me-2 text-primary"></i>User Login
                            </label>
                        </div>

                        <!-- Tombol -->
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

    function toggleAlasanPenolakan() {
        const status = document.getElementById('status').value;
        const wrapper = document.getElementById('alasanWrapper');

        if (status === 'ditolak') {
            wrapper.classList.remove('d-none');
        } else {
            wrapper.classList.add('d-none');
        }
    }

    document.getElementById('status').addEventListener('change', toggleAlasanPenolakan);

    // Saat halaman dimuat (untuk old value)
    window.addEventListener('DOMContentLoaded', toggleAlasanPenolakan);
</script>

<?= $this->endSection() ?>