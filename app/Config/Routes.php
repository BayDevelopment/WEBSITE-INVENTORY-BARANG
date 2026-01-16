<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Jika akses ke root '/', arahkan langsung ke auth/login
$routes->get('/', function () {
    return redirect()->to('auth/login');
});

// Route login & logout
$routes->get('auth/login', 'AuthController::index');
$routes->post('auth/login', 'AuthController::aksi_auth');
$routes->get('auth/logout', 'AuthController::logout');
// lupa password
$routes->get('auth/forgot-password', 'AuthController::forget_password');
$routes->post('auth/forgot-password', 'AuthController::sendResetLink');
// proses ketika sudah di klik dari email 
$routes->get('auth/forgot-password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('auth/forgot-password/reset', 'AuthController::processResetPassword');



// Grup khusus admin
$routes->group('admin', [
    'filter' => ['auth', 'role:admin']
], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');

    // POINT SCAN BARANG MASUK DAN KELUAR 
    // API barcode
    $routes->get('api/barang/by-barcode/(:segment)', 'BarangApi::byBarcode/$1');
    
    // barang
    $routes->get('data-barang', 'AdminController::page_barang');
    $routes->get('data-barang/tambah', 'AdminController::page_tambah_barang');
    $routes->post('data-barang/tambah', 'AdminController::aksi_tambah_barang');
    $routes->get('data-barang/edit/(:num)', 'AdminController::page_edit_barang/$1');
    $routes->post('data-barang/edit/(:num)', 'AdminController::aksi_edit_barang/$1');
    $routes->get('data-barang/hapus/(:num)', 'AdminController::delete_barang/$1');
    // => Barang Barcode
    $routes->get('data-barang/barcode/(:num)', 'AdminController::BarcodeBarang/$1');

    // barang masuk
    $routes->get('data-barang-masuk', 'AdminController::BarangMasuk');
    $routes->get('data-barang-masuk/tambah', 'AdminController::page_TambahBarangMasuk');
    $routes->post('data-barang-masuk/tambah', 'AdminController::aksi_tambahBarangMasuk');
    $routes->get('data-barang-masuk/edit/(:num)', 'AdminController::page_EditBarangMasuk/$1');
    $routes->post('data-barang-masuk/update/(:num)', 'AdminController::aksi_editBarangMasuk/$1');
    $routes->get('data-barang-masuk/hapus/(:num)', 'AdminController::deleteBarangMasuk/$1');
    // => scan barang masuk
    $routes->get('scan-barang-masuk', 'AdminController::page_ScanBarangMasuk');
    
    // barang keluar
    $routes->get('data-barang-keluar', 'AdminController::BarangKeluar');
    $routes->get('data-barang-keluar/tambah', 'AdminController::page_TambahBarangKeluar');
    $routes->post('data-barang-keluar/tambah', 'AdminController::aksi_tambah_barang_keluar');
    $routes->get('data-barang-keluar/edit/(:num)', 'AdminController::page_EditBarangKeluar/$1');
    $routes->post('data-barang-keluar/update/(:num)', 'AdminController::aksi_edit_barang_keluar/$1');
    $routes->get('data-barang-keluar/hapus/(:num)', 'AdminController::delete_BarangKeluar/$1');
    // => scan barang keluar
    $routes->get('scan-barang-keluar', 'AdminController::page_ScanBarangKeluar');

    // Laporan barang keluar dan masuk
    $routes->get('laporan-barang', 'AdminController::LaporanDataBarangMasukKeluar');
    // profile
    $routes->get('profile', 'AdminController::Profile');
    $routes->post('update-profile', 'AdminController::aksi_update_profile');
    $routes->post('change-password', 'AdminController::change_password');

    // satuan
    $routes->get('data-satuan', 'AdminController::page_satuan');
    $routes->get('data-satuan/tambah', 'AdminController::page_tambah_satuan');
    $routes->post('data-satuan/tambah', 'AdminController::aksi_tambah_satuan');
    $routes->get('data-satuan/edit/(:num)', 'AdminController::page_edit_satuan/$1');
    $routes->post('data-satuan/edit/(:num)', 'AdminController::aksi_edit_satuan/$1');
    $routes->get('data-satuan/hapus/(:num)', 'AdminController::aksi_hapus_satuan/$1');
});

// Grup khusus staff
$routes->group('staff', [
    'filter' => ['auth', 'role:staff_gudang']
], function ($routes) {
    $routes->get('dashboard', 'StaffController::dashboard');

    // barang masuk
    $routes->get('data-barang-masuk', 'StaffController::BarangMasuk');
    $routes->get('data-barang-masuk/tambah', 'StaffController::page_TambahBarangMasuk');
    $routes->post('data-barang-masuk/tambah', 'StaffController::aksi_tambahBarangMasuk');
    // barang keluar
    $routes->get('data-barang-keluar', 'StaffController::BarangKeluar');
    $routes->get('data-barang-keluar/tambah', 'StaffController::page_TambahBarangKeluar');
    $routes->post('data-barang-keluar/tambah', 'StaffController::aksi_tambah_barang_keluar');
    $routes->get('laporan-barang', 'StaffController::LaporanDataBarangMasukKeluar');
    // profile
    $routes->get('profile', 'StaffController::Profile');
    $routes->post('update-profile', 'StaffController::aksi_update_profile');
    $routes->post('change-password', 'StaffController::change_password');
});
