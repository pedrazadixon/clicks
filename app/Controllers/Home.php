<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        helper(['form']);
        return view('index');
    }

    public function redirectOrShow($shortcode)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM `links` WHERE `shortcode` LIKE '" . $shortcode . "' LIMIT 1");
        $results = $query->getResult();

        if (count($results) == 0) {
            $this->session->setFlashdata('message', 'Link not found.');
            return redirect()->to(base_url('/'));
        }

        $url = $results[0]->content;

        return redirect()->to($url);
    }

    public function share($shortcode)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM `links` WHERE `shortcode` LIKE '" . $shortcode . "' LIMIT 1");
        $results = $query->getResult();

        if (count($results) == 0) {
            $this->session->setFlashdata('message', 'Link not found.');
            return redirect()->to(base_url('/'));
        }

        $link = $results[0];

        return view('share', [
            'link' => $link,
        ]);
    }

    public function generate()
    {
        if (! $this->request->is('post'))
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');

        $rules = [
            'url' => 'required|valid_url_strict[http,https]',
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (! $this->validateData($data, $rules))
            return redirect()->to(base_url('/'))->withInput();


        $validData = $this->validator->getValidated();

        $url = $validData['url'];

        $next_shortcode = $this->getNextIDFromDB();

        $db = \Config\Database::connect();

        $db->query(
            "INSERT INTO `links` (`shortcode`, `content`) VALUES (" . $db->escape($next_shortcode) . ", " . $db->escape($url) . ")"
        );

        if ($db->affectedRows() > 0) {
            return redirect()->to(base_url('s/' . $next_shortcode));
        }

        $this->session->setFlashdata('message', 'Error al guardar el enlace.');

        return redirect()->to(base_url('/'));
    }

    function getNextIDFromDB()
    {
        $db = \Config\Database::connect();

        $db->transStart();

        $query = $db->query("SELECT * FROM `settings` WHERE `setting_name` LIKE 'last_shortcode' LIMIT 1 FOR UPDATE");

        $results = $query->getResult();

        $currentValue = $results[0]->setting_value;

        $nextValue = $this->getNext($currentValue);

        $reserved_shortcodes = [
            'app',
            'dixon',
            'api',
            's',
        ];

        if (in_array($nextValue, $reserved_shortcodes)) {
            $nextValue = $this->getNext($nextValue);
        }

        $shortcode_exists = true;
        do {
            $query = $db->query("SELECT * FROM `links` WHERE `shortcode` LIKE '" . $nextValue . "' LIMIT 1");
            $results = $query->getResult();
            if (count($results) == 0) {
                $shortcode_exists = false;
            } else {
                $nextValue = $this->getNext($nextValue);
            }
        } while ($shortcode_exists);


        $db->query(
            "UPDATE `settings` SET 
                `setting_value` = " . $db->escape($nextValue) . "
            WHERE 
                `setting_name` LIKE 'last_shortcode' 
            LIMIT 1"
        );

        $db->transComplete();

        return $nextValue;
    }

    function getNext($last_inserted = "")
    {
        if ($last_inserted == "") {
            return "0";
        }

        $chars = str_split($last_inserted);
        $ascii = array_map(fn($char) => ord($char), $chars);
        $reversed_ascii = array_reverse($ascii);

        $carry = true; // Empezamos con "carry" en true para incrementar el dígito menos significativo

        for ($i = 0; $i < count($reversed_ascii); $i++) {
            if ($carry) {
                $current_ascii = $reversed_ascii[$i];
                if ($current_ascii == 57) { // '9' -> 'a'
                    $reversed_ascii[$i] = 97; // 'a'
                    $carry = false;
                } elseif ($current_ascii == 122) { // 'z' -> 'A' 
                    $reversed_ascii[$i] = 65; // 'A'
                    $carry = false;
                } elseif ($current_ascii == 90) { // 'Z' -> carry
                    $reversed_ascii[$i] = 48; // '0'
                    $carry = true;
                } else {
                    $reversed_ascii[$i]++;
                    $carry = false;
                }
            }
        }

        // Si todavía hay carry, agregamos '0' al principio
        if ($carry) {
            array_push($reversed_ascii, 48); // ASCII de '0'
        }

        $result = implode('', array_map('chr', array_reverse($reversed_ascii)));
        return $result;
    }
}
