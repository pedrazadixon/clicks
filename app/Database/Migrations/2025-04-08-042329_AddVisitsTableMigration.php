<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitsTableMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'link_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'continent_code' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
                'null' => true,
            ],
            'country_code' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
                'null' => true,
            ],
            'browser' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'device' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'os' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'referer' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('link_id');
        $this->forge->addKey('ip_address');
        $this->forge->addKey('continent_code');
        $this->forge->addKey('country_code');
        $this->forge->addKey('browser');
        $this->forge->addKey('device');
        $this->forge->addKey('os');
        $this->forge->createTable('visits');
    }

    public function down()
    {
        $this->forge->dropTable('visits');
    }
}
