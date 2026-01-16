<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use CodeIgniter\HTTP\ResponseInterface;

class BarangApi extends BaseController
{
    // API BARANG MASUK DAN KELUAR
    public function byBarcode(string $barcode)
    {
        $barcode = trim(urldecode($barcode)); // penting

        $barangModel = new BarangModel();
        $barang = $barangModel
            ->select('id_barang, kode_barang, nama_barang')
            ->where('kode_barang', $barcode)
            ->first();

        if (!$barang) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'ok' => false,
                    'message' => 'Barcode tidak terdaftar: ' . $barcode
                ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'data' => $barang
        ]);
    }
}
