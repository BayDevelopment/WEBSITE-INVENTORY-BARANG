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
    public function page_barang()
    {
        $d_barang = $this->ModelBarang
            ->select('tb_barang.*, tb_satuan.nama_satuan')
            ->join('tb_satuan', 'tb_satuan.id_satuan = tb_barang.id_satuan', 'left')
            ->findAll();

        $data = [
            'title'      => 'Data Barang | Inventory Barang',
            'navlink'    => 'barang',
            'breadcrumb' => 'Data Barang',
            'd_barang'   => $d_barang,
        ];

        return view('admin/data-barang', $data);
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
                ->with('success', '✅ Data satuan berhasil ditambahkan!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Gagal menambahkan data satuan. Silakan coba lagi.');
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

        // 🔹 Cek apakah data dengan ID tersebut ada
        $cekData = $satuanModel->find($id);
        if (!$cekData) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('error', 'Data satuan tidak ditemukan atau sudah dihapus!');
        }

        // 🔹 Proses hapus data
        if ($satuanModel->delete($id)) {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('success', 'Data satuan berhasil dihapus!');
        } else {
            return redirect()->to(base_url('admin/data-satuan'))
                ->with('error', 'Gagal menghapus data satuan. Silakan coba lagi.');
        }
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
        $satuanList  = $modelSatuan->findAll(); // hasil: array berisi id_satuan & nama_satuan

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
    public function aksi_tambah_admin()
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
            'satuan'      => $this->request->getPost('id_satuan'),
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
}
