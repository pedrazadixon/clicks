<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
// use CodeIgniter\Database\RawSql;

class AddSettingsTableMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'setting_name' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => false,
            ],
            'setting_value longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('setting_name', false, true);
        $this->forge->createTable('settings');
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
