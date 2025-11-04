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


// Grup khusus admin
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
});

// Grup khusus staff gudang
$routes->group('staff', ['filter' => 'auth:staff_gudang'], function ($routes) {
    $routes->get('dashboard', 'StaffController::dashboard');
});
