<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangKeluarModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\SatuanModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $ModelBarang;
    protected $ModelSatuan;
    protected $ModelBarangMasuk;
    protected $ModelBarangKeluar;
    protected $ModelUser;
    protected $session;
    public function __construct()
    {
        $this->ModelBarang = new BarangModel();
        $this->ModelSatuan = new SatuanModel();
        $this->ModelBarangMasuk = new BarangMasukModel();
        $this->ModelBarangKeluar = new BarangKeluarModel();
        $this->ModelUser = new UsersModel();
        $this->session   = session();
    }
    public function dashboard()
    {
        // Load model
        $barangMasukModel  = new \App\Models\BarangMasukModel();
        $barangKeluarModel = new \App\Models\BarangKeluarModel();

        // Hitung total data barang masuk dengan status disetujui
        $totalBarangMasuk = $barangMasukModel
            ->where('status', 'disetujui')
            ->countAllResults();

        // Hitung total data barang keluar dengan status disetujui
        $totalBarangKeluar = $barangKeluarModel
            ->where('status', 'disetujui')
            ->countAllResults();

        // Kirim data ke view
        $data = [
            'title'            => 'Dashboard | Inventory Barang',
            'navlink'          => 'dashboard',
            'breadcrumb'       => 'Dashboard',
            'totalBarangMasuk' => $totalBarangMasuk,
            'totalBarangKeluar' => $totalBarangKeluar,
        ];

        return view('admin/dashboard-admin', $data);
    }



    // satuan
    public function page_satuan()
    {
        $model = $this->ModelSatuan;

        // Ambil parameter filter dari GET
        $nama_satuan = $this->request->getGet('nama_satuan');
        $keyword     = $this->request->getGet('keyword');

        // Query dasar
        $builder = $model->select('*');

        // Jika filter nama_satuan dipilih
        if (!empty($nama_satuan)) {
            $builder->where('nama_satuan', $nama_satuan);
        }

        // Jika ada keyword pencarian
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('nama_satuan', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        // Ambil hasilnya
        $d_satuan = $builder->orderBy('id_satuan', 'DESC')->findAll();

        // Ambil daftar nama satuan unik untuk dropdown
        $list_nama_satuan = $model->select('nama_satuan')
            ->groupBy('nama_satuan')
            ->orderBy('nama_satuan', 'ASC')
            ->findAll();

        // Kirim ke view
        $data = [
            'title'               => 'Data Satuan | Inventory Barang',
            'navlink'             => 'satuan',
            'breadcrumb'          => 'Data Satuan',
            'd_satuan'            => $d_satuan,
            'list_nama_satuan'    => $list_nama_satuan,
            'selected_nama_satuan' => $nama_satuan,
            'keyword'             => $keyword
        ];

        return view('admin/data-satuan', $data);
    }

    public function page_tambah_satuan()
    {
        $data = [
            'title' => 'Tambah Satuan | Inventory Barang',
            'navlink' => 'satuan',
            'breadcrumb' => 'Tambah Satuan'
        ];
        return view('admin/tambah-satuan', $data);
    }
    public function aksi_tambah_satuan()
    {
        $satuanModel = new \App\Models\SatuanModel();
        $validation  = \Config\Services::validation();

        // Aturan validasi
        $validation->setRules(
            [
                'nama_satuan' => [
                    'rules' => 'required|min_length[3]|is_unique[tb_satuan.nama_satuan]',
                    'errors' => [
                        'required'   => 'Nama satuan wajib diisi!',
                        'min_length' => 'Nama satuan minimal 3 karakter!',
                        'is_unique'  => 'Nama satuan sudah terdaftar!'
                    ]
                ],
                'keterangan' => [
                    'rules' => 'permit_empty|string|max_length[255]',
                    'errors' => [
                        'string'      => 'Keterangan harus berupa teks!',
                        'max_length'  => 'Keterangan maksimal 255 karakter!'
                    ]
                ]
            ]
        );

        // Jalankan validasi
        if (!$validation->withRequest($this->request)->run()) {
            // Simpan pesan error ke session dan redirect kembali
            return redirect()->back()
                ->withInput()
                ->with('validation', $validation)
                ->with('error', 'Terjadi kesalahan pada input. Periksa kembali data Anda!');
        }

        // Ambil input yang sudah divalidasi
        $data = [
            'nama_satuan' => trim($this->request->getPost('nama_satuan')),
            'keterangan'  => trim($this->request->getPost('keterangan')),
        ];

        // Proses insert
        if ($satuanModel->insert($data)) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('success', 'Data satuan berhasil ditambahkan!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data satuan. Silakan coba lagi.');
        }
    }
    public function page_edit_satuan($id)
    {
        $satuanById = $this->ModelSatuan->find($id);
        $data = [
            'title' => 'Edit Satuan | Inventory Barang',
            'navlink' => 'satuan',
            'breadcrumb' => 'Edit Satuan',
            'editData' => $satuanById
        ];
        return view('admin/edit-satuan', $data);
    }

    public function aksi_edit_satuan($id)
    {
        $satuanModel = new \App\Models\SatuanModel();
        $validation  = \Config\Services::validation();

        // 🔹 Cek apakah data dengan ID tersebut ada
        $cekData = $satuanModel->find($id);
        if (!$cekData) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('error', 'Data satuan tidak ditemukan!');
        }

        // 🔹 Aturan validasi
        $validation->setRules([
            'nama_satuan' => [
                'rules'  => "required|min_length[3]|is_unique[tb_satuan.nama_satuan,id_satuan,{$id}]",
                'errors' => [
                    'required'   => 'Nama satuan wajib diisi!',
                    'min_length' => 'Nama satuan minimal 3 karakter!',
                    'is_unique'  => 'Nama satuan sudah terdaftar!'
                ]
            ],
            'keterangan' => [
                'rules'  => 'permit_empty|string|max_length[255]',
                'errors' => [
                    'string'     => 'Keterangan harus berupa teks!',
                    'max_length' => 'Keterangan maksimal 255 karakter!'
                ]
            ]
        ]);

        // 🔹 Jalankan validasi input
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $validation)
                ->with('error', 'Terjadi kesalahan pada input. Periksa kembali data Anda!');
        }

        // 🔹 Ambil data dari form
        $data = [
            'nama_satuan' => trim($this->request->getPost('nama_satuan')),
            'keterangan'  => trim($this->request->getPost('keterangan')),
        ];

        // 🔹 Proses update
        if ($satuanModel->update($id, $data)) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('success', 'Data satuan berhasil diperbarui!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data satuan. Silakan coba lagi.');
        }
    }

    public function aksi_hapus_satuan($id)
    {
        $satuanModel = new \App\Models\SatuanModel();
        $barangModel = new \App\Models\BarangModel();

        // 🔹 1. Cek apakah data satuan ada
        $cekData = $satuanModel->find($id);
        if (!$cekData) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('error', 'Data satuan tidak ditemukan atau sudah dihapus!');
        }

        // 🔹 2. Cek apakah satuan sedang digunakan pada tb_barang
        $dipakai = $barangModel->where('id_satuan', $id)->countAllResults();

        if ($dipakai > 0) {
            // ❌ Jika digunakan → cegah hapus
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('error', 'Satuan tidak dapat dihapus karena sedang digunakan pada data barang!');
        }

        // 🔹 3. Lanjutkan proses hapus jika aman
        if ($satuanModel->delete($id)) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('success', 'Data satuan berhasil dihapus!');
        } else {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('error', 'Gagal menghapus data satuan. Silakan coba lagi.');
        }
    }


    // data barang
    public function page_barang()
    {
        $model = $this->ModelBarang;

        // Ambil parameter filter dari GET
        $nama_barang = $this->request->getGet('nama_barang');
        $keyword     = $this->request->getGet('keyword');

        // Query dengan join satuan
        $builder = $model
            ->select('tb_barang.*, tb_satuan.nama_satuan')
            ->join('tb_satuan', 'tb_satuan.id_satuan = tb_barang.id_satuan', 'left');

        // Filter nama_barang
        if (!empty($nama_barang)) {
            $builder->where('tb_barang.nama_barang', $nama_barang);
        }

        // Pencarian keyword
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('tb_barang.nama_barang', $keyword)
                ->orLike('tb_barang.keterangan', $keyword)
                ->groupEnd();
        }

        // Ambil data barang
        $d_barang = $builder->orderBy('tb_barang.id_barang', 'DESC')->findAll();

        // Untuk dropdown: ambil nama_barang unik
        $list_nama_barang = $model->select('nama_barang')
            ->groupBy('nama_barang')
            ->orderBy('nama_barang', 'ASC')
            ->findAll();

        // Kirim ke view
        $data = [
            'title'             => 'Data Barang | Inventory Barang',
            'navlink'           => 'barang',
            'breadcrumb'        => 'Data Barang',
            'd_barang'          => $d_barang,
            'filter_nama'       => $nama_barang,
            'filter_keyword'    => $keyword,
            'list_nama_barang'  => $list_nama_barang,
        ];

        return view('admin/data-barang', $data);
    }

    public function page_tambah_barang()
    {
        // ====== 1️⃣ Generate Kode Barang Otomatis ======
        $lastBarang = $this->ModelBarang
            ->select('kode_barang')
            ->orderBy('id_barang', 'DESC')
            ->first();

        if ($lastBarang) {
            // Ambil angka setelah prefix "KB"
            $lastNumber = (int) substr($lastBarang['kode_barang'], 2);
            $newNumber  = $lastNumber + 1;
            $kodeBarangBaru = 'KB' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            // Jika belum ada barang sama sekali
            $kodeBarangBaru = 'KB0001';
        }

        // ====== 2️⃣ Ambil Data Satuan dari ModelSatuan ======
        $modelSatuan = new \App\Models\SatuanModel();
        $satuanList  = $modelSatuan->findAll(); // hasil: array berisi id_satuan & nama_barang

        // ====== 3️⃣ Kirim ke View ======
        $data = [
            'title'        => 'Tambah Barang | Inventory Barang',
            'navlink'      => 'tambah barang',
            'breadcrumb'   => 'Tambah Barang',
            'kode_barang'  => $kodeBarangBaru,
            'satuanList'   => $satuanList,
        ];

        return view('admin/tambah-barang', $data);
    }
    public function aksi_tambah_barang()
    {
        $barangModel = new \App\Models\BarangModel();
        $validation  = \Config\Services::validation();

        // 🔹 1. Validasi input (tanpa kode_barang)
        $validation->setRules([
            'nama_barang' => 'required|min_length[5]',
            'id_satuan'   => 'required|integer',
            'stok'        => 'required|integer',
            'keterangan'  => 'permit_empty|string'
        ], [
            'nama_barang' => [
                'required'   => 'Nama barang wajib diisi.',
                'min_length' => 'Nama barang minimal 5 karakter.'
            ],
            'id_satuan' => [
                'required' => 'Pilih satuan barang.',
                'integer'  => 'Satuan tidak valid.'
            ],
            'stok' => [
                'required' => 'Stok wajib diisi.',
                'integer'  => 'Stok harus berupa angka.'
            ]
        ]);

        // 🔹 Jalankan validasi
        if (!$validation->withRequest($this->request)->run()) {
            // Simpan pesan error ke flashdata agar tampil SweetAlert di view
            session()->setFlashdata('error', 'Terjadi kesalahan pada input. Periksa kembali data Anda!');
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // 🔹 2. Generate kode barang baru di sisi server
        $lastBarang = $barangModel
            ->select('kode_barang')
            ->orderBy('id_barang', 'DESC')
            ->first();

        if ($lastBarang) {
            $lastNumber = (int) substr($lastBarang['kode_barang'], 2);
            $newNumber  = $lastNumber + 1;
            $kodeBarangBaru = 'KB' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $kodeBarangBaru = 'KB0001';
        }

        // 🔹 3. Siapkan data (kode_barang tidak diambil dari POST)
        $data = [
            'kode_barang' => $kodeBarangBaru,
            'nama_barang' => $this->request->getPost('nama_barang'),
            'id_satuan'      => $this->request->getPost('id_satuan'),
            'stok'        => $this->request->getPost('stok'),
            'keterangan'  => $this->request->getPost('keterangan'),
        ];

        // 🔹 4. Simpan ke database
        if ($barangModel->insert($data)) {
            return redirect()->to(base_url('admin/data-barang'))->with('success', 'Data barang berhasil ditambahkan!');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data barang.');
        }
    }
    public function page_edit_barang($id)
    {
        $barang = $this->ModelBarang->find($id);
        $satuanList = $this->ModelSatuan->findAll();

        // Jika data tidak ditemukan
        if (!$barang) {
            return redirect()->to(base_url('admin/data-barang'))
                ->with('error', 'Data barang tidak ditemukan.');
        }

        $data = [
            'title'        => 'Edit Barang | Inventory Barang',
            'navlink'      => 'barang',
            'breadcrumb'   => 'Edit Barang',
            'editData'     => $barang,         // ✔ dipakai oleh form edit
            'satuanList'   => $satuanList,     // ✔ untuk dropdown satuan
            'validation'   => \Config\Services::validation(), // ✔ untuk error form
        ];

        return view('admin/edit-barang', $data);
    }
    public function aksi_edit_barang($id)
    {
        $barangModel = new \App\Models\BarangModel();
        $validation  = \Config\Services::validation();

        // 🔹 1. Cek apakah data dengan ID tersebut ada
        $cekData = $barangModel->find($id);
        if (!$cekData) {
            return redirect()->to(base_url('admin/data-barang'))
                ->with('error', 'Data barang tidak ditemukan!');
        }

        // 🔹 2. Validasi input (tanpa kode_barang)
        $validation->setRules([
            'nama_barang' => 'required|min_length[5]',
            'id_satuan'   => 'required|integer',
            'stok'        => 'required|integer',
            'keterangan'  => 'permit_empty|string'
        ], [
            'nama_barang' => [
                'required'   => 'Nama barang wajib diisi.',
                'min_length' => 'Nama barang minimal 5 karakter.'
            ],
            'id_satuan' => [
                'required' => 'Pilih satuan barang.',
                'integer'  => 'Satuan tidak valid.'
            ],
            'stok' => [
                'required' => 'Stok wajib diisi.',
                'integer'  => 'Stok harus berupa angka.'
            ]
        ]);

        // 🔹 Jalankan validasi
        if (!$validation->withRequest($this->request)->run()) {
            session()->setFlashdata('error', 'Terjadi kesalahan pada input. Periksa kembali data Anda!');
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // 🔹 3. Ambil kode_barang dari database (AMAN, tidak dari user)
        $kodeBarangFix = $cekData['kode_barang'];

        // 🔹 4. Siapkan data untuk diupdate
        $data = [
            'kode_barang' => $kodeBarangFix, // tetap pakai yg asli
            'nama_barang' => $this->request->getPost('nama_barang'),
            'id_satuan'   => $this->request->getPost('id_satuan'),
            'stok'        => $this->request->getPost('stok'),
            'keterangan'  => $this->request->getPost('keterangan'),
        ];

        // 🔹 5. Update ke database
        if ($barangModel->update($id, $data)) {
            return redirect()->to(base_url('admin/data-barang'))
                ->with('success', 'Data barang berhasil diperbarui!');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data barang.');
        }
    }
    public function delete_barang($id)
    {
        $barangModel = new \App\Models\BarangModel();

        // 🔹 1. Cek apakah data barang ada
        $cekData = $barangModel->find($id);
        if (!$cekData) {
            return redirect()->to(base_url('admin/data-barang'))
                ->with('error', 'Data barang tidak ditemukan!');
        }

        // 🔹 2. Hapus data
        if ($barangModel->delete($id)) {
            return redirect()->to(base_url('admin/data-barang'))
                ->with('success', 'Data barang berhasil dihapus!');
        } else {
            return redirect()->to(base_url('admin/data-barang'))
                ->with('error', 'Gagal menghapus data barang!');
        }
    }

    // *Barang Masuk

    public function BarangMasuk()
    {
        $barangMasukModel = new \App\Models\BarangMasukModel();

        // Ambil filter dari GET
        $filterNama    = $this->request->getGet('nama_barang');
        $filterKeyword = $this->request->getGet('keyword');

        // ==========================
        // LIST NAMA BARANG UNTUK DROPDOWN
        // ==========================
        $listNamaBarang = $barangMasukModel
            ->select('tb_barang.nama_barang')
            ->join('tb_barang', 'tb_barang.id_barang = tb_barang_masuk.id_barang')
            ->groupBy('tb_barang.nama_barang')
            ->orderBy('tb_barang.nama_barang', 'ASC')
            ->findAll();

        // ==========================
        // QUERY JOIN BARANG MASUK
        // ==========================
        $db = db_connect();
        $builder = $db->table('tb_barang_masuk bm');

        $builder->select('
        bm.*, 
        b.nama_barang, 
        u.nama_lengkap AS user,
        u.role AS role_user
    ');

        $builder->join('tb_barang b', 'b.id_barang = bm.id_barang', 'left');
        $builder->join('tb_users u', 'u.id_user = bm.id_user_input', 'left');

        // ==========================
        // FILTER NAMA BARANG
        // ==========================
        if (!empty($filterNama)) {
            $builder->where('b.nama_barang', $filterNama);
        }

        // ==========================
        // FILTER KEYWORD
        // ==========================
        if (!empty($filterKeyword)) {
            $builder->groupStart()
                ->like('b.nama_barang', $filterKeyword)
                ->orLike('bm.keterangan', $filterKeyword)
                ->groupEnd();
        }

        // ORDER
        $builder->orderBy('bm.id_barang_masuk', 'DESC');

        // GET RESULT
        $d_barangMasuk = $builder->get()->getResultArray();

        // ==========================
        // SEND DATA TO VIEW
        // ==========================
        $data = [
            'title'            => 'Data Barang Masuk | Inventory Barang',
            'navlink'          => 'barang masuk',
            'breadcrumb'       => 'Data Barang Masuk',
            'd_barangMasuk'    => $d_barangMasuk,
            'list_nama_barang' => $listNamaBarang,
            'filter_nama'      => $filterNama,
            'filter_keyword'   => $filterKeyword
        ];

        return view('admin/data-barang-masuk', $data);
    }


    public function page_TambahBarangMasuk()
    {
        // Ambil semua data barang
        $dBarang = $this->ModelBarang->select('id_barang, nama_barang')->findAll();

        // Ambil id_user dari session (lebih aman daripada cookies)
        $session = session();
        $idUserLogin = $session->get('id_user');

        // Kalau Anda benar menggunakan cookies, gunakan:
        // $idUserLogin = get_cookie('id_user');

        $data = [
            'title'      => 'Tambah Barang Masuk',
            'navlink'    => 'tambah barang masuk',
            'breadcrumb' => 'Tambah Barang Masuk',
            'd_barang'   => $dBarang,
            'id_user'    => $idUserLogin // dikirim ke view
        ];

        return view('admin/tambah-barang-masuk', $data);
    }

    public function aksi_tambahBarangMasuk()
    {
        $barangMasukModel = new \App\Models\BarangMasukModel();
        $barangModel      = new \App\Models\BarangModel();
        $validation       = \Config\Services::validation();

        // Ambil ID user dari session
        $idUser = session()->get('id_user');

        if (!$idUser) {
            return redirect()->to('/auth/login')->with('error', 'Sesi pengguna tidak ditemukan.');
        }

        // Validasi input
        $rules = [
            'id_barang' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Pilih barang terlebih dahulu.',
                    'numeric'  => 'Barang tidak valid.'
                ]
            ],
            'jumlah' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Jumlah wajib diisi.',
                    'numeric' => 'Jumlah harus angka.',
                    'greater_than' => 'Jumlah harus lebih dari 0.'
                ]
            ],
            'tanggal_masuk' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal masuk wajib diisi.',
                    'valid_date' => 'Format tanggal tidak valid.'
                ]
            ],
            'keterangan' => [
                'rules'  => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 255 karakter.'
                ]
            ],

            // 🔥 VALIDASI alasan_penolakan jika status = ditolak
            'status' => [
                'rules' => 'required|in_list[menunggu,disetujui,ditolak]',
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list'  => 'Status tidak valid.'
                ]
            ],
            'alasan_penolakan' => [
                'rules' => 'permit_empty',
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Ambil data input
        $id_barang     = $this->request->getPost('id_barang');
        $jumlahMasuk   = (int)$this->request->getPost('jumlah');
        $tanggalMasuk  = $this->request->getPost('tanggal_masuk');
        $keterangan    = $this->request->getPost('keterangan');
        $status        = $this->request->getPost('status');
        $alasan        = $this->request->getPost('alasan_penolakan');

        // 🔥 Jika status ditolak → alasan wajib
        if ($status == 'ditolak' && empty($alasan)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Alasan penolakan wajib diisi jika status ditolak.');
        }

        // Cek id_barang
        $barangData = $barangModel->find($id_barang);
        if (!$barangData) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // ===========================
        // 🔥 LOGIKA STOK (HANYA JIKA DISETUJUI)
        // ===========================
        if ($status == 'disetujui') {
            $stokLama = (int)$barangData['stok'];
            $stokBaru = $stokLama + $jumlahMasuk;

            $barangModel->update($id_barang, [
                'stok' => $stokBaru
            ]);
        }

        // ===========================
        // 🔥 SIMPAN BARANG MASUK
        // ===========================
        $dataInsert = [
            'id_barang'          => $id_barang,
            'jumlah'             => $jumlahMasuk,
            'tanggal_masuk'      => $tanggalMasuk,
            'keterangan'         => $keterangan,
            'id_user_input'      => $idUser,
            'status'             => $status,
            'alasan_penolakan'   => $status == 'ditolak' ? $alasan : null,
            'kategori'           => 'Barang Masuk',
        ];

        $barangMasukModel->insert($dataInsert);

        return redirect()->to(base_url('admin/data-barang-masuk'))
            ->with('success', 'Data barang masuk berhasil disimpan.');
    }


    public function page_EditBarangMasuk($id)
    {
        $session = session();
        $idUserLogin = $session->get('id_user');

        // Ambil data barang masuk berdasarkan ID
        $dataBarangMasuk = $this->ModelBarangMasuk
            ->select('tb_barang_masuk.*, tb_barang.nama_barang')
            ->join('tb_barang', 'tb_barang.id_barang = tb_barang_masuk.id_barang')
            ->where('id_barang_masuk', $id)
            ->first();

        // Jika data tidak ditemukan
        if (!$dataBarangMasuk) {
            return redirect()->to(base_url('admin/data-barang-masuk'))
                ->with('error', 'Data barang masuk tidak ditemukan.');
        }

        // Ambil list barang (untuk dropdown)
        $dBarang = $this->ModelBarang
            ->select('id_barang, nama_barang')
            ->findAll();

        $data = [
            'title'           => 'Edit Barang Masuk',
            'navlink'         => 'edit barang masuk',
            'breadcrumb'      => 'Edit Barang Masuk',
            'data_masuk'      => $dataBarangMasuk,
            'd_barang'        => $dBarang,
            'id_user'         => $idUserLogin,   // aman dari session
        ];

        return view('admin/edit-barang-masuk', $data);
    }
    public function aksi_editBarangMasuk($id)
    {
        $BarangMasukModel = new \App\Models\BarangMasukModel();
        $BarangModel      = new \App\Models\BarangModel();
        $validation       = \Config\Services::validation();

        $session = session();
        $idUserLogin = $session->get('id_user');

        // Cek data barang masuk yang lama
        $existing = $BarangMasukModel
            ->where('id_barang_masuk', $id)
            ->first();

        if (!$existing) {
            return redirect()->to(base_url('admin/data-barang-masuk'))
                ->with('error', 'Data barang masuk tidak ditemukan!');
        }

        // RULES VALIDASI
        $rules = [
            'id_barang'        => 'required|numeric',
            'jumlah'           => 'required|numeric',
            'tanggal_masuk'    => 'required|valid_date',
            'keterangan'       => 'permit_empty|string',
            'status'           => 'required|in_list[menunggu,disetujui,ditolak]',
            'alasan_penolakan' => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        // Ambil input user
        $id_barang     = $this->request->getPost('id_barang');
        $jumlahBaru    = (int)$this->request->getPost('jumlah');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');
        $keterangan    = $this->request->getPost('keterangan');
        $status        = $this->request->getPost('status');
        $alasan        = $this->request->getPost('alasan_penolakan');

        // Alasan wajib diisi jika status ditolak
        if ($status == 'ditolak' && empty($alasan)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Alasan penolakan wajib diisi ketika status ditolak!');
        }

        // Ambil stok barang berdasarkan barang lama
        $barang = $BarangModel->where('id_barang', $existing['id_barang'])->first();
        if (!$barang) {
            return redirect()->back()->with('error', 'Data barang tidak ditemukan!');
        }

        $stokLama = (int)$barang['stok'];
        $jumlahLama = (int)$existing['jumlah'];

        // Perubahan stok hanya jika status disetujui
        if ($status == 'disetujui') {
            $stokBaru = $stokLama - $jumlahLama + $jumlahBaru;

            if ($stokBaru < 0) {
                return redirect()->back()->with('error', 'Stok tidak boleh minus!');
            }

            $BarangModel->update($existing['id_barang'], [
                'stok' => $stokBaru
            ]);
        }

        // Update data barang masuk
        $updateData = [
            'id_barang'        => $id_barang,
            'jumlah'           => $jumlahBaru,
            'tanggal_masuk'    => $tanggal_masuk,
            'keterangan'       => $keterangan,
            'status'           => $status,
            'alasan_penolakan' => $status == 'ditolak' ? $alasan : null,
            // TIDAK mengubah id_user_input → tetap gunakan yang lama
            'id_user_input'    => $existing['id_user_input'],
        ];

        $BarangMasukModel->update($id, $updateData);

        return redirect()->to(base_url('admin/data-barang-masuk'))
            ->with('success', 'Data barang masuk berhasil diperbarui!');
    }


    public function deleteBarangMasuk($id)
    {
        // Ambil data barang masuk berdasarkan ID
        $dataMasuk = $this->ModelBarangMasuk
            ->where('id_barang_masuk', $id)
            ->first();

        if (!$dataMasuk) {
            return redirect()->back()->with('error', 'Data barang masuk tidak ditemukan.');
        }

        // Ambil data stok barang saat ini
        $barang = $this->ModelBarang
            ->where('id_barang', $dataMasuk['id_barang'])
            ->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Data barang tidak ditemukan.');
        }

        // =============================
        // 🔥 LOGIKA STATUS
        // =============================
        // Jika status disetujui → stok harus dikurangi kembali
        // Jika status menunggu / ditolak → stok TIDAK berubah
        if ($dataMasuk['status'] === 'disetujui') {

            // rollback stok
            $stokBaru = $barang['stok'] - $dataMasuk['jumlah'];
            if ($stokBaru < 0) {
                $stokBaru = 0; // antisipasi stok negatif
            }

            // Update stok barang
            $this->ModelBarang->update(
                $dataMasuk['id_barang'],
                ['stok' => $stokBaru]
            );
        }

        // =============================
        // 🔥 Hapus data barang masuk
        // =============================
        $this->ModelBarangMasuk->delete($id);

        return redirect()->to(base_url('admin/data-barang-masuk'))
            ->with('success', 'Data barang masuk berhasil dihapus!');
    }


    // Barang Keluar
    public function BarangKeluar()
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();

        // Ambil filter dari GET
        $filterNama    = $this->request->getGet('nama_barang');
        $filterKeyword = $this->request->getGet('keyword');

        // ==========================
        // LIST NAMA BARANG UNTUK DROPDOWN
        // ==========================
        $listNamaBarang = $barangKeluarModel
            ->select('tb_barang.nama_barang')
            ->join('tb_barang', 'tb_barang.id_barang = tb_barang_keluar.id_barang')
            ->groupBy('tb_barang.nama_barang')
            ->orderBy('tb_barang.nama_barang', 'ASC')
            ->findAll();

        // ==========================
        // QUERY JOIN BARANG KELUAR
        // ==========================
        $db = db_connect();
        $builder = $db->table('tb_barang_keluar bk');

        $builder->select('
        bk.*,
        b.nama_barang,
        u.nama_lengkap AS user,
        u.role AS role_user
    ');

        $builder->join('tb_barang b', 'b.id_barang = bk.id_barang', 'left');
        $builder->join('tb_users u', 'u.id_user = bk.id_user_input', 'left');

        // ==========================
        // FILTER NAMA BARANG
        // ==========================
        if (!empty($filterNama)) {
            $builder->where('b.nama_barang', $filterNama);
        }

        // ==========================
        // FILTER KEYWORD
        // ==========================
        if (!empty($filterKeyword)) {
            $builder->groupStart()
                ->like('b.nama_barang', $filterKeyword)
                ->orLike('bk.keterangan', $filterKeyword)
                ->groupEnd();
        }

        // ORDER
        $builder->orderBy('bk.id_barang_keluar', 'DESC');

        // GET RESULT
        $d_barangKeluar = $builder->get()->getResultArray();

        // ==========================
        // SEND DATA TO VIEW
        // ==========================
        $data = [
            'title'            => 'Data Barang Keluar | Inventory Barang',
            'navlink'          => 'barang keluar',
            'breadcrumb'       => 'Data Barang Keluar',
            'd_barangKeluar'   => $d_barangKeluar,
            'list_nama_barang' => $listNamaBarang,
            'filter_nama'      => $filterNama,
            'filter_keyword'   => $filterKeyword
        ];

        return view('admin/data-barang-keluar', $data);
    }


    public function page_TambahBarangKeluar()
    {
        // Ambil semua data barang
        $dBarang = $this->ModelBarang->select('id_barang, nama_barang')->findAll();

        // Ambil id_user dari session (lebih aman daripada cookies)
        $session = session();
        $idUserLogin = $session->get('id_user');

        // Kalau Anda benar menggunakan cookies, gunakan:
        // $idUserLogin = get_cookie('id_user');

        $data = [
            'title'      => 'Tambah Barang Keluar',
            'navlink'    => 'tambah barang Keluar',
            'breadcrumb' => 'Tambah Barang Keluar',
            'd_barang'   => $dBarang,
            'id_user'    => $idUserLogin // dikirim ke view
        ];

        return view('admin/tambah-barang-keluar', $data);
    }
    public function aksi_tambah_barang_keluar()
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangModel       = new \App\Models\BarangModel();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_barang' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Pilih barang terlebih dahulu.',
                    'integer'  => 'Data barang tidak valid.'
                ]
            ],

            'jumlah' => [
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'Jumlah barang wajib diisi.',
                    'integer'      => 'Jumlah barang harus berupa angka.',
                    'greater_than' => 'Jumlah barang harus lebih dari 0.'
                ]
            ],

            'tanggal_keluar' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Tanggal barang keluar wajib diisi.'
                ]
            ],

            'status' => [
                'rules'  => 'required|in_list[menunggu,disetujui,ditolak]',
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list'  => 'Status tidak valid.'
                ]
            ],

            'alasan_penolakan' => [
                'rules' => 'permit_empty',
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {

            $errors = $validation->getErrors();
            $flashError = !empty($errors) ? array_values($errors)[0] : 'Input tidak valid.';

            return redirect()->back()
                ->withInput()
                ->with('validation', $validation)
                ->with('error', $flashError);
        }

        // ==========================================
        // 🔹 Ambil input
        // ==========================================
        $idBarang = $this->request->getPost('id_barang');
        $jumlah   = (int)$this->request->getPost('jumlah');
        $status   = $this->request->getPost('status');
        $alasan   = $this->request->getPost('alasan_penolakan');

        // 🟥 Jika status ditolak, alasan wajib
        if ($status == 'ditolak' && empty($alasan)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Alasan penolakan wajib diisi jika status ditolak!');
        }

        // ==========================================
        // 🔥 CEK STOK BARANG DI tb_barang
        // ==========================================
        $barang = $barangModel->find($idBarang);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // 🛑 Stok 0 → Tidak boleh membuat barang keluar (status apapun)
        if ((int)$barang['stok'] <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Barang tidak dapat dikeluarkan karena stok saat ini 0!');
        }

        // ==========================================
        // 🔥 UPDATE STOK HANYA SAAT STATUS DISETUJUI
        // ==========================================
        if ($status == 'disetujui') {

            // Jika stok kurang
            if ($barang['stok'] < $jumlah) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Stok tidak mencukupi!');
            }

            // Kurangi stok
            $barangModel->update($idBarang, [
                'stok' => $barang['stok'] - $jumlah
            ]);
        }

        // ==========================================
        // 🔥 SIMPAN DATA BARANG KELUAR
        // ==========================================
        $barangKeluarModel->insert([
            'id_barang'        => $idBarang,
            'jumlah'           => $jumlah,
            'tanggal_keluar'   => $this->request->getPost('tanggal_keluar'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'id_user_input'    => session()->get('id_user'),
            'status'           => $status,
            'alasan_penolakan' => $status == 'ditolak' ? $alasan : null,
            'kategori'         => 'Barang Keluar',
        ]);

        return redirect()->to(base_url('admin/data-barang-keluar'))
            ->with('success', 'Barang keluar berhasil diproses!');
    }


    public function page_EditBarangKeluar($id)
    {
        $session = session();
        $idUserLogin = $session->get('id_user');

        // Ambil data barang masuk berdasarkan ID
        $dataBarangKeluar = $this->ModelBarangKeluar
            ->select('tb_barang_keluar.*, tb_barang.nama_barang')
            ->join('tb_barang', 'tb_barang.id_barang = tb_barang_keluar.id_barang')
            ->where('id_barang_keluar', $id)
            ->first();

        // Jika data tidak ditemukan
        if (!$dataBarangKeluar) {
            return redirect()->to(base_url('admin/data-barang-keluar'))
                ->with('error', 'Data barang keluar tidak ditemukan.');
        }

        // Ambil list barang (untuk dropdown)
        $dBarang = $this->ModelBarang
            ->select('id_barang, nama_barang')
            ->findAll();

        $data = [
            'title'           => 'Edit Barang Keluar',
            'navlink'         => 'edit barang Keluar',
            'breadcrumb'      => 'Edit Barang Keluar',
            'data_keluar'      => $dataBarangKeluar,
            'd_barang'        => $dBarang,
            'id_user'         => $idUserLogin,   // aman dari session
        ];

        return view('admin/edit-barang-keluar', $data);
    }
    public function aksi_edit_barang_keluar($id)
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangModel       = new \App\Models\BarangModel();

        // Ambil data lama barang keluar
        $dataLama = $barangKeluarModel->find($id);
        if (!$dataLama) {
            return redirect()->back()->with('error', 'Data barang keluar tidak ditemukan.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_barang' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Pilih barang terlebih dahulu.',
                    'integer'  => 'Data barang tidak valid.'
                ]
            ],
            'jumlah' => [
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'Jumlah barang wajib diisi.',
                    'integer'      => 'Jumlah harus berupa angka.',
                    'greater_than' => 'Jumlah harus lebih dari 0.'
                ]
            ],
            'tanggal_keluar' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Tanggal barang keluar wajib diisi.'
                ]
            ],
            'status' => [
                'rules'  => 'required|in_list[menunggu,disetujui,ditolak]',
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list'  => 'Status tidak valid.'
                ]
            ],
            'alasan_penolakan' => [
                'rules'  => 'permit_empty'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $validation)
                ->with('error', 'Input tidak valid.');
        }

        // INPUT BARU
        $idBarangBaru  = $this->request->getPost('id_barang');
        $jumlahBaru    = (int)$this->request->getPost('jumlah');
        $statusBaru    = $this->request->getPost('status');
        $alasan        = $this->request->getPost('alasan_penolakan');

        // WAJIB ISI ALASAN JIKA DITOLAK
        if ($statusBaru === 'ditolak' && empty($alasan)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Alasan penolakan wajib diisi jika status ditolak.');
        }

        // KEMBALIKAN STOK LAMA JIKA DATA LAMA BERSTATUS DISETUJUI
        if ($dataLama['status'] === 'disetujui') {
            $barangLama = $barangModel->find($dataLama['id_barang']);
            if ($barangLama) {
                $barangModel->update($dataLama['id_barang'], [
                    'stok' => $barangLama['stok'] + $dataLama['jumlah']
                ]);
            }
        }

        // CEK BARANG BARU
        $barangBaru = $barangModel->find($idBarangBaru);
        if (!$barangBaru) {
            return redirect()->back()->with('error', 'Barang baru tidak ditemukan.');
        }

        // KURANGI STOK JIKA DISETUJUI
        if ($statusBaru === 'disetujui') {
            if ($barangBaru['stok'] < $jumlahBaru) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi setelah perubahan!');
            }

            $barangModel->update($idBarangBaru, [
                'stok' => $barangBaru['stok'] - $jumlahBaru
            ]);
        }

        // UPDATE DATA BARANG KELUAR
        $barangKeluarModel->update($id, [
            'id_barang'        => $idBarangBaru,
            'jumlah'           => $jumlahBaru,
            'tanggal_keluar'   => $this->request->getPost('tanggal_keluar'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'status'           => $statusBaru,
            'alasan_penolakan' => $statusBaru === 'ditolak' ? $alasan : null,
            // ❗ ID USER INPUT TIDAK DIUBAH
            'id_user_input'    => $dataLama['id_user_input'],
        ]);

        return redirect()->to(base_url('admin/data-barang-keluar'))
            ->with('success', 'Data barang keluar berhasil diperbarui!');
    }


    public function delete_BarangKeluar($id)
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangModel       = new \App\Models\BarangModel();

        // Cek data barang keluar
        $dataKeluar = $barangKeluarModel->find($id);
        if (!$dataKeluar) {
            return redirect()->back()->with('error', 'Data barang keluar tidak ditemukan.');
        }

        // Ambil data barang terkait
        $barang = $barangModel->find($dataKeluar['id_barang']);
        if (!$barang) {
            return redirect()->back()->with('error', 'Data barang tidak ditemukan.');
        }

        // ============================
        // LOGIKA STATUS
        // ============================
        // Jika status "disetujui" → stok harus dikembalikan
        // Jika status "menunggu" / "ditolak" → stok jangan diubah
        if ($dataKeluar['status'] === 'disetujui') {

            $stokBaru = $barang['stok'] + $dataKeluar['jumlah'];

            $barangModel->update($barang['id_barang'], [
                'stok' => $stokBaru
            ]);
        }

        // Hapus data barang keluar
        $barangKeluarModel->delete($id);

        return redirect()->to(base_url('admin/data-barang-keluar'))
            ->with('success', 'Data barang keluar berhasil dihapus!');
    }

    // laporan barang masuk dan keluar 
    public function LaporanDataBarangMasukKeluar()
    {
        $barangMasukModel  = new \App\Models\BarangMasukModel();
        $barangKeluarModel = new \App\Models\BarangKeluarModel();

        // Ambil filter dari GET (tanggal + keyword + kategori + status)
        $filterTanggal  = $this->request->getGet('tanggal');
        $filterKeyword  = $this->request->getGet('keyword');
        $filterKategori = $this->request->getGet('kategori');
        $filterStatus   = $this->request->getGet('status');   // ← DITAMBAHKAN SESUAI PERINTAH!

        // Cek apakah user menekan tombol cari
        $isSearch = !empty($filterTanggal) || !empty($filterKeyword) || !empty($filterKategori) || !empty($filterStatus);

        // Koneksi DB
        $db = db_connect();

        // Hasil pencarian
        $result = [];

        if ($isSearch) {

            // ==================================================
            //               BARANG MASUK
            // ==================================================
            if ($filterKategori == "Barang Masuk" || empty($filterKategori)) {

                $builder = $db->table('tb_barang_masuk bm')
                    ->select('bm.*, b.nama_barang, u.nama_lengkap AS user, "Barang Masuk" AS kategori')
                    ->join('tb_barang b', 'b.id_barang = bm.id_barang', 'left')
                    ->join('tb_users u', 'u.id_user = bm.id_user_input', 'left');

                // Filter berdasarkan tanggal
                if (!empty($filterTanggal)) {
                    $builder->where("DATE(bm.created_at)", $filterTanggal);
                }

                // Filter keyword
                if (!empty($filterKeyword)) {
                    $builder->groupStart()
                        ->like('b.nama_barang', $filterKeyword)
                        ->orLike('bm.keterangan', $filterKeyword)
                        ->groupEnd();
                }

                // WAJIB hanya tampil yg disetujui / ditolak
                $builder->whereIn('bm.status', ['disetujui', 'ditolak']);

                // Filter status jika dipilih di form
                if (!empty($filterStatus)) {
                    $builder->where('bm.status', $filterStatus);
                }

                $resultMasuk = $builder->get()->getResultArray();
                $result = array_merge($result, $resultMasuk);
            }

            // ==================================================
            //               BARANG KELUAR
            // ==================================================
            if ($filterKategori == "Barang Keluar" || empty($filterKategori)) {

                $builder = $db->table('tb_barang_keluar bk')
                    ->select('bk.*, b.nama_barang, u.nama_lengkap AS user, "Barang Keluar" AS kategori')
                    ->join('tb_barang b', 'b.id_barang = bk.id_barang', 'left')
                    ->join('tb_users u', 'u.id_user = bk.id_user_input', 'left');

                // Filter tanggal
                if (!empty($filterTanggal)) {
                    $builder->where("DATE(bk.created_at)", $filterTanggal);
                }

                // Filter keyword
                if (!empty($filterKeyword)) {
                    $builder->groupStart()
                        ->like('b.nama_barang', $filterKeyword)
                        ->orLike('bk.keterangan', $filterKeyword)
                        ->groupEnd();
                }

                // WAJIB hanya tampil yg disetujui / ditolak
                $builder->whereIn('bk.status', ['disetujui', 'ditolak']);

                // Filter status jika dipilih di form
                if (!empty($filterStatus)) {
                    $builder->where('bk.status', $filterStatus);
                }

                $resultKeluar = $builder->get()->getResultArray();
                $result = array_merge($result, $resultKeluar);
            }
        }

        // ==========================
        // KIRIM DATA KE VIEW
        // ==========================
        $data = [
            'title'           => 'Laporan Barang Masuk & Keluar | Inventory Barang',
            'navlink'         => 'laporan barang',
            'breadcrumb'      => 'Laporan Barang',

            // Filter
            'filter_tanggal'  => $filterTanggal,
            'filter_keyword'  => $filterKeyword,
            'filter_kategori' => $filterKategori,
            'filter_status'   => $filterStatus,   // ← PENTING! DIKIRIM KE VIEW

            // Data hasil pencarian
            'hasil'           => $isSearch ? $result : [],

            // Info search
            'is_search'       => $isSearch
        ];

        return view('admin/laporan-barang', $data);
    }



    // profile
    public function Profile()
    {

        $data = [
            'title'         => 'Profil Pengguna | Inventory Barang',
            'navlink'       => 'Profil',
            'breadcrumb'    => 'Profile Pengguna',
            'username'      => session()->get('username'),
            'nama_lengkap'  => session()->get('nama_lengkap'),
            'email'         => session()->get('email'),
            'no_telp'       => session()->get('no_telp'),
            'status_aktif'  => session()->get('status_aktif'),
        ];

        return view('admin/profile/data-profile', $data);
    }

    public function aksi_update_profile()
    {
        $id_user = session()->get('id_user');

        if (!$id_user) {
            return redirect()->to('/auth/login')->with('error', 'Sesi pengguna tidak ditemukan.');
        }

        $validation = \Config\Services::validation();

        // -----------------------------
        // Validasi input
        // -----------------------------
        $validation->setRules([
            'username' => [
                'rules'  => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_]+$/]',
                'errors' => [
                    'required'    => 'Username wajib diisi.',
                    'min_length'  => 'Username minimal 3 karakter.',
                    'max_length'  => 'Username maksimal 50 karakter.',
                    'regex_match' => 'Username hanya boleh huruf, angka, dan underscore. Tidak boleh karakter khusus.'
                ]
            ],
            'nama_lengkap' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama lengkap minimal 3 karakter.',
                    'max_length' => 'Nama lengkap maksimal 100 karakter.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.'
                ]
            ],
            'no_telp' => [
                'rules'  => 'required|min_length[6]|max_length[20]',
                'errors' => [
                    'required'   => 'Nomor telepon wajib diisi.',
                    'min_length' => 'Nomor telepon minimal 6 digit.',
                    'max_length' => 'Nomor telepon maksimal 20 digit.'
                ]
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {

            $errors = $validation->getErrors();
            $flashError = !empty($errors) ? array_values($errors)[0] : 'Input tidak valid.';

            return redirect()->back()
                ->withInput()
                ->with('validation', $validation)
                ->with('error', $flashError);
        }

        // -----------------------------
        // Cek duplikasi USERNAME, EMAIL & NO TELEPON
        // -----------------------------
        $username = $this->request->getPost('username');
        $email    = $this->request->getPost('email');
        $no_telp  = $this->request->getPost('no_telp');

        // Username
        $cekUsername = $this->ModelUser->where('username', $username)
            ->where('id_user !=', $id_user)
            ->first();
        if ($cekUsername) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan pengguna lain.');
        }

        // Email
        $cekEmail = $this->ModelUser->where('email', $email)
            ->where('id_user !=', $id_user)
            ->first();
        if ($cekEmail) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan pengguna lain.');
        }

        // No Telp
        $cekTelp = $this->ModelUser->where('no_telp', $no_telp)
            ->where('id_user !=', $id_user)
            ->first();
        if ($cekTelp) {
            return redirect()->back()->withInput()->with('error', 'Nomor telepon sudah digunakan pengguna lain.');
        }

        // -----------------------------
        // Update data user
        // -----------------------------
        $data = [
            'username'     => $username,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $email,
            'no_telp'      => $no_telp,
        ];

        $this->ModelUser->update($id_user, $data);

        // -----------------------------
        // Perbarui session
        // -----------------------------
        session()->set($data);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function change_password()
    {
        $userId = session()->get('id_user');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Sesi pengguna tidak ditemukan.');
        }

        $validationErrors = [];

        // Ambil input
        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // =========================
        // VALIDASI MANUAL FIELD
        // =========================
        // Password saat ini
        if (!$currentPassword || strlen($currentPassword) < 6) {
            $validationErrors['current_password'] = !$currentPassword
                ? 'Password saat ini wajib diisi.'
                : 'Password saat ini minimal 6 karakter.';
        }

        // Password baru
        if (!$newPassword || strlen($newPassword) < 6) {
            $validationErrors['new_password'] = !$newPassword
                ? 'Password baru wajib diisi.'
                : 'Password baru minimal 6 karakter.';
        }

        // Konfirmasi password
        if (!$confirmPassword) {
            $validationErrors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($newPassword !== $confirmPassword) {
            $validationErrors['confirm_password'] = 'Konfirmasi password tidak sesuai dengan password baru.';
        }

        // Jika ada error, redirect kembali dengan input dan error
        if (!empty($validationErrors)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $validationErrors)
                ->with('error', array_values($validationErrors)[0]); // tampilkan error pertama
        }

        // =========================
        // AMBIL USER DARI DATABASE
        // =========================
        $user = $this->ModelUser->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Cek password saat ini
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Password saat ini salah.');
        }

        // =========================
        // HASH PASSWORD BARU & UPDATE
        // =========================
        $newPasswordHash = password_hash($newPassword, PASSWORD_ARGON2ID);

        $this->ModelUser->update($userId, ['password' => $newPasswordHash]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui.');
    }
}
