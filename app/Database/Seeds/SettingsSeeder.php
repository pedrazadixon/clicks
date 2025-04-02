<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'setting_name' => 'last_shortcode',
            'setting_value' => '000',
        ];

        $this->db->table('settings')->insert($data);
    }
}
