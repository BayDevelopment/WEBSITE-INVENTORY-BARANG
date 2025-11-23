<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangKeluarModel;
use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\SatuanModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class StaffController extends BaseController
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
        $barangMasukModel  = new \App\Models\BarangMasukModel();
        $barangKeluarModel = new \App\Models\BarangKeluarModel();

        // Hitung total barang masuk (hanya status "disetujui")
        $totalBarangMasuk = $barangMasukModel
            ->where('status', 'disetujui')
            ->countAllResults();

        // Hitung total barang keluar (hanya status "disetujui")
        $totalBarangKeluar = $barangKeluarModel
            ->where('status', 'disetujui')
            ->countAllResults();

        $data = [
            'title'             => 'Dashboard | Inventory Barang',
            'navlink'           => 'dashboard',
            'breadcrumb'        => 'Dashboard',
            'totalBarangMasuk'  => $totalBarangMasuk,
            'totalBarangKeluar' => $totalBarangKeluar,
        ];

        return view('staff/dashboard-staff', $data);
    }


    // Barang masuk
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

        return view('staff/data-barang-masuk', $data);
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

        return view('staff/tambah-barang-masuk', $data);
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

        // Validasi input (status tidak divalidasi lagi)
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

        // Status dibuat default (TIDAK dari form)
        $status = 'menunggu'; // default
        $alasan = null;        // tidak perlu alasan

        // Cek id_barang
        $barangData = $barangModel->find($id_barang);
        if (!$barangData) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // 🔥 Update stok karena status default "disetujui"
        $stokLama = (int)$barangData['stok'];
        $stokBaru = $stokLama + $jumlahMasuk;

        $barangModel->update($id_barang, [
            'stok' => $stokBaru
        ]);

        // 🔥 Simpan barang masuk
        $dataInsert = [
            'id_barang'          => $id_barang,
            'jumlah'             => $jumlahMasuk,
            'tanggal_masuk'      => $tanggalMasuk,
            'keterangan'         => $keterangan,
            'id_user_input'      => $idUser,
            'status'             => $status,
            'alasan_penolakan'   => $alasan,
            'kategori'           => 'Barang Masuk',
        ];

        $barangMasukModel->insert($dataInsert);

        return redirect()->to(base_url('staff/data-barang-masuk'))
            ->with('success', 'Data barang masuk berhasil disimpan.');
    }

    // barang keluar
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
        // QUERY JOIN BARANG MASUK
        // ==========================
        $db = db_connect();
        $builder = $db->table('tb_barang_keluar bm');

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
        $builder->orderBy('bm.id_barang_keluar', 'DESC');

        // GET RESULT
        $d_barangKeluar = $builder->get()->getResultArray();

        // ==========================
        // SEND DATA TO VIEW
        // ==========================
        $data = [
            'title'            => 'Data Barang Keluar | Inventory Barang',
            'navlink'          => 'barang keluar',
            'breadcrumb'       => 'Data Barang Keluar',
            'd_barangKeluar'    => $d_barangKeluar,
            'list_nama_barang' => $listNamaBarang,
            'filter_nama'      => $filterNama,
            'filter_keyword'   => $filterKeyword
        ];

        return view('staff/data-barang-keluar', $data);
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

        return view('staff/tambah-barang-keluar', $data);
    }
    public function aksi_tambah_barang_keluar()
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangModel       = new \App\Models\BarangModel();
        $validation        = \Config\Services::validation();

        // Ambil ID user dari session
        $idUser = session()->get('id_user');
        if (!$idUser) {
            return redirect()->to('/auth/login')->with('error', 'Sesi pengguna tidak ditemukan.');
        }

        // Validasi input (status & alasan tidak divalidasi karena otomatis)
        $rules = [
            'id_barang' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Pilih barang terlebih dahulu.',
                    'integer'  => 'Data barang tidak valid.'
                ]
            ],

            'jumlah' => [
                'rules' => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'Jumlah barang wajib diisi.',
                    'integer'      => 'Jumlah barang harus berupa angka.',
                    'greater_than' => 'Jumlah barang harus lebih dari 0.'
                ]
            ],

            'tanggal_keluar' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal keluar wajib diisi.',
                    'valid_date' => 'Format tanggal tidak valid.'
                ]
            ],

            'keterangan' => [
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 255 karakter.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            // dd($this->validator->getErrors());
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Ambil input
        $idBarang      = $this->request->getPost('id_barang');
        $jumlahKeluar  = (int)$this->request->getPost('jumlah');
        $tanggalKeluar = $this->request->getPost('tanggal_keluar');
        $keterangan    = $this->request->getPost('keterangan');

        // Status dibuat otomatis default (TIDAK dari form)
        $status = 'menunggu';
        $alasan = null;

        // Cek barang
        $barang = $barangModel->find($idBarang);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $stokLama = (int)$barang['stok'];

        // ❗ Stok harus mencukupi
        if ($jumlahKeluar > $stokLama) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stok tidak mencukupi untuk pengeluaran barang.');
        }

        // 🔥 Kurangi stok langsung
        $barangModel->update($idBarang, [
            'stok' => $stokLama - $jumlahKeluar
        ]);

        // 🔥 Simpan transaksi barang keluar
        $barangKeluarModel->insert([
            'id_barang'        => $idBarang,
            'jumlah'           => $jumlahKeluar,
            'tanggal_keluar'   => $tanggalKeluar,
            'keterangan'       => $keterangan,
            'id_user_input'    => $idUser,
            'status'           => $status,
            'alasan_penolakan' => $alasan,
            'kategori'         => 'Barang Keluar',
        ]);

        return redirect()->to(base_url('staff/data-barang-keluar'))
            ->with('success', 'Data barang keluar berhasil disimpan.');
    }

    // barang masuk keluar
    public function LaporanDataBarangMasukKeluar()
    {
        $barangMasukModel  = new \App\Models\BarangMasukModel();
        $barangKeluarModel = new \App\Models\BarangKeluarModel();

        // Ambil filter dari GET
        $filterNama     = $this->request->getGet('nama_barang');
        $filterKeyword  = $this->request->getGet('keyword');
        $filterStatus   = $this->request->getGet('status');     // disetujui / ditolak
        $filterKategori = $this->request->getGet('kategori');   // Barang Masuk / Barang Keluar

        // Flag apakah user menekan tombol cari
        $isSearch = !empty($filterNama) || !empty($filterKeyword) || !empty($filterStatus) || !empty($filterKategori);

        // ==========================
        // LIST NAMA BARANG DROPDOWN
        // ==========================
        $db = db_connect();
        $listNamaBarang = $db->table('tb_barang')
            ->select('nama_barang')
            ->groupBy('nama_barang')
            ->orderBy('nama_barang', 'ASC')
            ->get()->getResultArray();

        // ==========================
        // HASIL PENCARIAN
        // ==========================
        $result = [];

        if ($isSearch) {

            // --- QUERY GABUNG BARANG MASUK ---
            if ($filterKategori == "Barang Masuk" || empty($filterKategori)) {
                $builder = $db->table('tb_barang_masuk bm')
                    ->select('bm.*, b.nama_barang, u.nama_lengkap AS user, "Barang Masuk" AS kategori')
                    ->join('tb_barang b', 'b.id_barang = bm.id_barang', 'left')
                    ->join('tb_users u', 'u.id_user = bm.id_user_input', 'left');

                // filter nama barang
                if (!empty($filterNama)) {
                    $builder->where('b.nama_barang', $filterNama);
                }

                // filter keyword (nama barang + keterangan)
                if (!empty($filterKeyword)) {
                    $builder->groupStart()
                        ->like('b.nama_barang', $filterKeyword)
                        ->orLike('bm.keterangan', $filterKeyword)
                        ->groupEnd();
                }

                // filter status (disetujui / ditolak)
                if (!empty($filterStatus)) {
                    $builder->where('bm.status', $filterStatus);
                }

                $resultMasuk = $builder->get()->getResultArray();
                $result = array_merge($result, $resultMasuk);
            }

            // --- QUERY GABUNG BARANG KELUAR ---
            if ($filterKategori == "Barang Keluar" || empty($filterKategori)) {
                $builder = $db->table('tb_barang_keluar bk')
                    ->select('bk.*, b.nama_barang, u.nama_lengkap AS user, "Barang Keluar" AS kategori')
                    ->join('tb_barang b', 'b.id_barang = bk.id_barang', 'left')
                    ->join('tb_users u', 'u.id_user = bk.id_user_input', 'left');

                if (!empty($filterNama)) {
                    $builder->where('b.nama_barang', $filterNama);
                }

                if (!empty($filterKeyword)) {
                    $builder->groupStart()
                        ->like('b.nama_barang', $filterKeyword)
                        ->orLike('bk.keterangan', $filterKeyword)
                        ->groupEnd();
                }

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
            'title'            => 'Laporan Barang Masuk & Keluar | Inventory Barang',
            'navlink'          => 'laporan barang',
            'breadcrumb'       => 'Laporan Barang',
            'list_nama_barang' => $listNamaBarang,

            // Filter
            'filter_nama'      => $filterNama,
            'filter_keyword'   => $filterKeyword,
            'filter_status'    => $filterStatus,
            'filter_kategori'  => $filterKategori,

            // Data hasil pencarian
            'hasil'            => $isSearch ? $result : [],

            // Untuk kontrol tampilan apakah sudah melakukan pencarian
            'is_search'        => $isSearch
        ];

        return view('staff/laporan-barang', $data);
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

        return view('staff/profile/data-profile', $data);
    }

    public function aksi_update_profile()
    {
        $id_user = session()->get('id_user');

        if (!$id_user) {
            return redirect()->to('/auth/login')->with('error', 'Sesi pengguna tidak ditemukan.');
        }

        $validation = \Config\Services::validation();

        // ----------------------------- VALIDASI INPUT -----------------------------
        $validation->setRules([
            'username' => [
                'rules'  => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z0-9_]+$/]',
                'errors' => [
                    'required'    => 'Username wajib diisi.',
                    'min_length'  => 'Username minimal 3 karakter.',
                    'max_length'  => 'Username maksimal 50 karakter.',
                    'regex_match' => 'Username hanya boleh huruf, angka, dan underscore. Tidak boleh spasi atau simbol.'
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
            $flashError = !empty($errors) ? array_values($errors)[0] : "Input tidak valid.";

            return redirect()->back()
                ->withInput()
                ->with('validation', $validation)
                ->with('error', $flashError);
        }

        // ----------------------------- CEK DUPLIKASI USERNAME, EMAIL, NO TELP -----------------------------
        // Username
        $usernameExist = $this->ModelUser->where('username', $this->request->getPost('username'))
            ->where('id_user !=', $id_user)
            ->first();
        if ($usernameExist) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan pengguna lain.');
        }

        // Email
        $emailExist = $this->ModelUser->where('email', $this->request->getPost('email'))
            ->where('id_user !=', $id_user)
            ->first();
        if ($emailExist) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan pengguna lain.');
        }

        // No Telp
        $telpExist = $this->ModelUser->where('no_telp', $this->request->getPost('no_telp'))
            ->where('id_user !=', $id_user)
            ->first();
        if ($telpExist) {
            return redirect()->back()->withInput()->with('error', 'Nomor telepon sudah digunakan pengguna lain.');
        }

        // ----------------------------- UPDATE DATA PROFILE -----------------------------
        $data = [
            'username'     => $this->request->getPost('username'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $this->request->getPost('email'),
            'no_telp'      => $this->request->getPost('no_telp'),
        ];

        $this->ModelUser->update($id_user, $data);

        // ----------------------------- PERBARUI SESSION -----------------------------
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
