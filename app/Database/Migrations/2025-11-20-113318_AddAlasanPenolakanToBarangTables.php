<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlasanPenolakanToBarangTables extends Migration
{
    public function up()
    {
        // Tambahkan kolom ke tb_barang_masuk
        $this->forge->addColumn('tb_barang_masuk', [
            'alasan_penolakan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status', // Letakkan setelah kolom status
            ],
        ]);

        // Tambahkan kolom ke tb_barang_keluar
        $this->forge->addColumn('tb_barang_keluar', [
            'alasan_penolakan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        // Hapus kolom saat rollback
        $this->forge->dropColumn('tb_barang_masuk', 'alasan_penolakan');
        $this->forge->dropColumn('tb_barang_keluar', 'alasan_penolakan');
    }
}
