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
}
