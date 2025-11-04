<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'      => 'admin',
                'password'      => password_hash('admin123', PASSWORD_ARGON2ID),
                'nama_lengkap'  => 'Administrator',
                'role'          => 'admin',
                'status_aktif'  => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'username'      => 'staff1',
                'password'      => password_hash('staff123', PASSWORD_ARGON2ID),
                'nama_lengkap'  => 'Staff Gudang',
                'role'          => 'staff_gudang',
                'status_aktif'  => 'aktif',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('tb_users')->insertBatch($data);
    }
}
