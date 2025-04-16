<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitsTotalsTableMigration extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('links', [
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['url', 'qr', 'note', 'linkgroup'],
                'default' => 'url',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('links', [
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['url', 'text'],
                'default' => 'url',
            ],
        ]);
    }
}
