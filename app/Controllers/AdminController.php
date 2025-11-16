<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\SatuanModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $ModelBarang;
    protected $ModelSatuan;
    public function __construct()
    {
        $this->ModelBarang = new BarangModel();
        $this->ModelSatuan = new SatuanModel();
    }
    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard | Inventory Barang',
            'navlink' => 'dashboard',
            'breadcrumb' => 'Dashboard'
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
        $barangModel      = new \App\Models\BarangModel();

        // Ambil filter dari GET request
        $filterNama    = $this->request->getGet('nama_barang');
        $filterKeyword = $this->request->getGet('keyword');

        // ==========================
        // LIST NAMA BARANG UNTUK DROPDOWN
        // ==========================
        $listNamaBarang = $barangModel
            ->select('nama_barang')
            ->orderBy('nama_barang', 'ASC')
            ->findAll();

        // ==========================
        // QUERY JOIN BARANG MASUK
        // ==========================
        $db = db_connect();
        $builder = $db->table('tb_barang_masuk bm');
        $builder->select('bm.*, b.nama_barang, u.nama_lengkap AS user');
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

        $builder->orderBy('bm.id_barang_masuk', 'DESC');
        $d_barangMasuk = $builder->get()->getResultArray();

        // ==========================
        // DATA TO VIEW
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
}
