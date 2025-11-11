<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class StaffController extends BaseController
{
    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard | Inventory Barang'
        ];
        return view('staff/dashboard-staff', $data);
    }
}
