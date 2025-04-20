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
            'content' => [
                'type' => 'MEDIUMTEXT',
            ],
        ]);

        $this->forge->addColumn('links', [
            'language' => [
                'type' => 'ENUM',
                'null' => true,
                'after' => 'type',
                'constraint' => [
                    'abap',
                    'ada',
                    'assembly_x86',
                    'c_cpp',
                    'clojure',
                    'cobol',
                    'csharp',
                    'css',
                    'dart',
                    'erlang',
                    'fortran',
                    'golang',
                    'groovy',
                    'haskell',
                    'html',
                    'java',
                    'javascript',
                    'json',
                    'kotlin',
                    'lua',
                    'matlab',
                    'objectivec',
                    'other',
                    'pascal',
                    'perl',
                    'php',
                    'powershell',
                    'python',
                    'r',
                    'rich_text',
                    'ruby',
                    'rust',
                    'scala',
                    'swift',
                    'typescript',
                    'xml',
                ],
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
            'content' => [
                'type' => 'TEXT',
            ],
        ]);

        $this->forge->dropColumn('links', 'language');
    }
}
