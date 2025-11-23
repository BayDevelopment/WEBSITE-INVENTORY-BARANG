<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $UserModel;

    // login proses
    protected $session;
    protected $request;

    public function __construct()
    {
        $this->UserModel = new UsersModel();
        $this->session    = session();
        $this->request    = service('request');
    }
    public function index()
    {
        // Jika sudah login, cegah akses ke halaman login
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');

            session()->setFlashdata('error', 'Anda sudah login.');

            if ($role === 'admin') {
                return redirect()->to('admin/dashboard');
            } elseif ($role === 'staff_gudang') {
                return redirect()->to('staff/dashboard');
            }
        }

        // Ambil validasi dari session kalau ada
        $validation = session()->getFlashdata('validation') ?? \Config\Services::validation();

        $data = [
            'title' => 'Login | Inventory Barang',
            'validation' => $validation
        ];

        return view('auth', $data);
    }

    public function aksi_auth()
    {
        $validationRules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'Username wajib diisi.',
                    'min_length' => 'Username minimal 3 karakter.',
                    'max_length' => 'Username maksimal 50 karakter.'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]|max_length[50]',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 6 karakter.',
                    'max_length' => 'Password maksimal 50 karakter.'
                ]
            ]
        ];

        // Jika validasi gagal
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user
        $user = $this->UserModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah.');
        }

        // Set session login
        session()->set([
            'isLoggedIn' => true,
            'id_user'    => $user['id_user'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'nama_lengkap'       => $user['nama_lengkap'],
            'email'       => $user['email'],
            'no_telp'       => $user['no_telp'],
            'status_aktif'       => $user['status_aktif'],
            'login_time' => time(),
        ]);

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            return redirect()->to('admin/dashboard');
        } elseif ($user['role'] === 'staff_gudang') {
            return redirect()->to('staff/dashboard');
        }

        return redirect()->to('auth')->with('error', 'Role pengguna tidak dikenali.');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login')->with('success', 'Kamu sudah logout.');
    }
}
