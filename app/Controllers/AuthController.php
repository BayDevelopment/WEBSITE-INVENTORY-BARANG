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
        // kalau sudah login langsung arahkan ke dashboard
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            return $role === 'admin'
                ? redirect()->to('admin/dashboard')
                : redirect()->to('staff/dashboard');
        }

        $data = [
            'title' => 'Login | Inventory Barang',
        ];
        return view('auth', $data);
    }

    public function aksi_auth()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // cari user berdasarkan username
        $user = $this->UserModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Username tidak ditemukan.');
        }

        // verifikasi password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah.');
        }

        // set session login
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'login_time' => time(), // untuk cek durasi 7 hari
        ]);

        // redirect sesuai role
        if ($user['role'] === 'admin') {
            return redirect()->to('admin/dashboard');
        } elseif ($user['role'] === 'staff_gudang') {
            return redirect()->to('staff/dashboard');
        }

        // jika role tidak dikenali
        return redirect()->to('auth')->with('error', 'Role pengguna tidak dikenali.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth')->with('success', 'Kamu sudah logout.');
    }
}
