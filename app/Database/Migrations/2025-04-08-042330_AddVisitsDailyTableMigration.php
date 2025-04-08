<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitsTotalsTableMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'link_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'visits' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('date');
        $this->forge->addKey('link_id');
        $this->forge->createTable('visits_daily');
    }

    public function down()
    {
        $this->forge->dropTable('visits_daily');
    }
}
