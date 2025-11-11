<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            // Ganti field satuan lama → relasi ke tb_satuan
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Relasi ke tb_satuan.id_satuan',
            ],
            'stok' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_barang', true);
        $this->forge->addForeignKey('id_satuan', 'tb_satuan', 'id_satuan', 'CASCADE', 'SET NULL');
        $this->forge->createTable('tb_barang');
    }

    public function down()
    {
        $this->forge->dropForeignKey('tb_barang', 'tb_barang_id_satuan_foreign');
        $this->forge->dropTable('tb_barang');
    }
}
