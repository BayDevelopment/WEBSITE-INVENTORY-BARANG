<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // jika belum login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // role user saat ini
        $userRole = $session->get('role');

        // role yang diizinkan (misal 'admin' atau 'staff_gudang')
        $allowedRoles = $arguments ?? [];

        // jika role user tidak ada dalam daftar yang diizinkan
        if (!in_array($userRole, $allowedRoles)) {
            // arahkan ke dashboard sesuai perannya
            if ($userRole === 'admin') {
                return redirect()->to('admin/dashboard')->with('error', 'Akses ditolak.');
            } elseif ($userRole === 'staff_gudang') {
                return redirect()->to('staff/dashboard')->with('error', 'Akses ditolak.');
            }

            return redirect()->to('auth/login')->with('error', 'Akses ditolak.');
        }

        return;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
