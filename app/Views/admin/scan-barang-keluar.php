<?= $this->extend('templates/users/main') ?>
<?= $this->section('MainContent') ?>

<style>
  .scan-card { border: 0; border-radius: 16px; }

  #scanner {
    width: 100%;
    border-radius: 14px;
    overflow: hidden;
    background: #0b1220;
    position: relative;
    aspect-ratio: 16 / 9;
    max-height: 360px;
    min-height: 220px;
  }
  @media (max-width: 576px) {
    #scanner { aspect-ratio: 4 / 3; max-height: 320px; min-height: 220px; }
  }

  #scanner video,
  #scanner canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
  }

  .scan-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
  }

  .scan-box {
    width: min(82%, 380px);
    height: clamp(90px, 24vw, 140px);
    border: 2px solid rgba(255,255,255,.85);
    border-radius: 12px;
    box-shadow: 0 0 0 9999px rgba(0,0,0,.22);
  }

  .scan-muted { font-size: .9rem; }
</style>

<div class="container-fluid px-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
      <h2 class="fw-bold text-primary mb-1">Scan Barang Keluar</h2>
      <p class="text-muted mb-0">Arahkan kamera ke barcode (kode batang), lalu sistem akan memilih barang otomatis.</p>
      <small class="text-muted scan-muted d-block mt-1">
        Tips: paling stabil di pencahayaan cukup, dan barcode diperbesar (kalau dari layar).
      </small>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('admin/data-barang-keluar/tambah') ?>"
         class="btn btn-dark btn-sm rounded-pill px-3 py-2">
        <i class="fa-solid fa-pen-to-square me-1"></i> Isi Manual
      </a>
      <a href="<?= base_url('admin/data-barang-keluar') ?>"
         class="btn btn-secondary btn-sm rounded-pill px-3 py-2">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
      </a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="card scan-card shadow-sm">
        <div class="card-body p-4">

          <div id="scanner">
            <div class="scan-overlay"><div class="scan-box"></div></div>
          </div>

          <div id="alertWrap" class="mt-3 d-none">
            <div id="alertBox" class="alert alert-info d-flex align-items-center mb-0" role="alert">
              <i class="fa-solid fa-circle-info me-2"></i>
              <span id="alertText">Klik "Start Scan" untuk mulai.</span>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            <button id="btnStart" type="button" class="btn btn-primary btn-sm rounded-pill px-3">
              <i class="fa-solid fa-camera me-1"></i> Start Scan
            </button>
            <button id="btnStop" type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" disabled>
              <i class="fa-solid fa-circle-stop me-1"></i> Stop
            </button>
          </div>

          <small class="text-muted d-block mt-2">
            Kalau barcode sulit terbaca: coba print label / zoom barcode, luruskan kamera, jangan terlalu dekat.
          </small>

        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card scan-card shadow-sm">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-2">
            <i class="fa-solid fa-list-check me-2 text-primary"></i>Cara pakai
          </h6>
          <ol class="text-muted ps-3 mb-0">
            <li class="mb-2">Klik <b>Start Scan</b> dan izinkan kamera.</li>
            <li class="mb-2">Arahkan barcode ke kotak panduan.</li>
            <li class="mb-2">Jika ketemu, otomatis diarahkan ke form tambah (barang terpilih).</li>
          </ol>

          <hr>

          <div class="alert alert-warning mb-0" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation mt-1 flex-shrink-0"></i>
                <div class="flex-grow-1" style="min-width:0; overflow-wrap:anywhere; word-break:break-word;">
                Jika error 404 <code style="white-space:normal;">/hybridaction/zybTrackerStatisticsAction</code> muncul di console,
                itu biasanya dari extension/tracker browser dan aman diabaikan.
                </div>
            </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quagga2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>

<script>
  const alertWrap = document.getElementById('alertWrap');
  const alertBox  = document.getElementById('alertBox');
  const alertText = document.getElementById('alertText');

  const btnStart  = document.getElementById('btnStart');
  const btnStop   = document.getElementById('btnStop');

  let running = false;
  let lastCode = null;
  let detectedHandlerInstalled = false;

  function showAlert(type, text) {
    alertWrap.classList.remove('d-none');
    alertBox.className = 'alert d-flex align-items-center mb-0 alert-' + type;
    alertText.textContent = text;
  }

  function cleanCode(code) {
    if (!code) return '';
    return String(code).trim().replace(/[^\x20-\x7E]/g, '');
  }

  async function handleFound(rawCode) {
    const code = cleanCode(rawCode);

    if (!code || code === lastCode) return;
    lastCode = code;

    showAlert('success', `Barcode terbaca: ${code}. Mencari barang...`);

    try {
      const url = `<?= base_url('admin/api/barang/by-barcode') ?>/${encodeURIComponent(code)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

      const ct = (res.headers.get('content-type') || '').toLowerCase();
      if (!ct.includes('application/json')) {
        await res.text();
        throw new Error('API tidak mengembalikan JSON. Kemungkinan route salah / kena redirect filter.');
      }

      const json = await res.json();

      if (!res.ok || !json || !json.ok) {
        throw new Error((json && json.message) ? json.message : 'Barang tidak ditemukan / API error');
      }

      if (!json.data || !json.data.id_barang) {
        console.log('Response API:', json);
        throw new Error('API tidak mengembalikan id_barang. Cek controller BarangApi.');
      }

      const target = `<?= base_url('admin/data-barang-keluar/tambah') ?>?id_barang=${encodeURIComponent(json.data.id_barang)}`;
      window.location.assign(target);

    } catch (err) {
      showAlert('danger', err.message);
      lastCode = null;
    }
  }

  function startScan() {
    if (running) return;

    showAlert('info', 'Menyiapkan kamera... (izinkan akses kamera)');
    btnStart.disabled = true;

    Quagga.init({
      inputStream: {
        type: "LiveStream",
        target: document.querySelector('#scanner'),
        constraints: {
          facingMode: "environment",
          width: { ideal: 1280 },
          height: { ideal: 720 }
        },
        area: { top: "15%", right: "8%", left: "8%", bottom: "15%" }
      },
      locator: {
        halfSample: true,
        patchSize: "medium"
      },
      numOfWorkers: navigator.hardwareConcurrency ? Math.min(4, navigator.hardwareConcurrency) : 2,
      decoder: {
        readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
      },
      locate: true
    }, function(err) {
      if (err) {
        showAlert('danger', 'Gagal membuka kamera: ' + (err.message || err));
        btnStart.disabled = false;
        return;
      }

      Quagga.start();
      running = true;

      btnStop.disabled = false;
      showAlert('info', 'Arahkan kamera ke barcode...');

      if (!detectedHandlerInstalled) {
        Quagga.onDetected(function(result) {
          const code = result?.codeResult?.code;
          if (code) handleFound(code);
        });
        detectedHandlerInstalled = true;
      }
    });
  }

  function stopScan() {
    if (!running) return;

    Quagga.stop();
    running = false;

    btnStart.disabled = false;
    btnStop.disabled = true;
    showAlert('secondary', 'Scan dihentikan. Klik "Start Scan" untuk mulai lagi.');
  }

  btnStart.addEventListener('click', startScan);
  btnStop.addEventListener('click', stopScan);

  window.addEventListener('load', () => {
    showAlert('info', 'Klik "Start Scan" untuk mulai.');
  });
</script>

<?= $this->endSection() ?>
