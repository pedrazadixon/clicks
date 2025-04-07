<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddLinksTableMigration extends Migration
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
            'shortcode varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL',
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['url', 'text'],
                'default' => 'url',
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'is_custom_shortcode' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('shortcode', false, true);
        $this->forge->createTable('links');
    }

    public function down()
    {
        $this->forge->dropTable('links');
    }
}
