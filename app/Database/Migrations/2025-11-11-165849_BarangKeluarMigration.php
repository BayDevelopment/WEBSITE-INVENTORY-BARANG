<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangKeluarMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang_keluar' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'id_barang' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'jumlah' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'tanggal_keluar' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_user_input' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'status' => [
                'type'       => 'ENUM("menunggu","disetujui","ditolak")',
                'default'    => 'menunggu',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('id_barang_keluar', true);
        $this->forge->addForeignKey('id_barang', 'tb_barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user_input', 'tb_users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_barang_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('tb_barang_keluar');
    }
}
