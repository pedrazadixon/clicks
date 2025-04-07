<?php

namespace App\Controllers;

use App\ThirdParty\Base62Converter;

class Home extends BaseController
{
    public function index()
    {
        helper(['form']);
        return view('index');
    }

    public function redirectOrShow($shortcode)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM `links` WHERE `shortcode` LIKE '" . $shortcode . "' LIMIT 1");
        $results = $query->getResult();

        if (count($results) == 0)
            return redirect(base_url('/'))->with('message', 'Link not found');

        $url = $results[0]->content;

        if (!is_null($results[0]->password))
            return redirect()->to(base_url('p/' . $shortcode));

        return redirect()->to($url);
    }

    public function protected($shortcode)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM `links` WHERE `shortcode` LIKE '" . $shortcode . "' LIMIT 1");
        $results = $query->getResult();

        if (count($results) == 0) {
            $this->session->setFlashdata('message', 'Link not found.');
            return redirect()->to(base_url('/'));
        }

        $link = $results[0];

        if (empty($link->password))
            return redirect()->to($link->content);

        if ($this->request->is('post')) {

            $password = $this->request->getPost('password');

            if (password_verify($password, $link->password)) {
                return redirect()->to($link->content);
            } else {
                return redirect()->back()->with('message', 'Incorrect password.');
            }
        }

        helper(['form']);

        return view('protected', [
            'link' => $link,
        ]);
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
            'shortcode' => 'permit_empty|alpha_dash|max_length[50]|min_length[4]|is_unique[links.shortcode]',
            'password' => 'permit_empty|min_length[4]|max_length[150]',
            'expiration-type' => 'permit_empty|in_list[time,visits]',
            'expiration-time' => 'permit_empty|integer',
            'expiration-unit' => 'permit_empty|in_list[minutes,hours,days,weeks,months]',
            'expiration-visits' => 'permit_empty|integer',
        ];

        $messages = [
            'shortcode' => [
                'is_unique' => 'The shortcode already exists. Try another one.',
            ],
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (! $this->validateData($data, $rules, $messages))
            return redirect()->to(base_url('/'))->withInput();


        $validData = $this->validator->getValidated();

        $url = $validData['url'];

        $customShortcode = $validData['shortcode'] ?? null;

        $db = \Config\Database::connect();

        $linksModel = model('LinksModel');

        $secret = getenv('BASE62_SECRET');
        $converter = new Base62Converter([
            'Secrect' => $secret,
            'CodeLength' => 4,
        ]);

        $db->transStart();

        $newData = [
            'content' => $url,
            'shortcode' => null,
            'is_custom_shortcode' => ! empty($customShortcode),
        ];

        if (! empty($validData['password']))
            $newData['password'] = password_hash($validData['password'], PASSWORD_BCRYPT);


        $linksModel->insert($newData);

        $newId = $db->insertID();

        $finalShortcode = null;

        if (empty($customShortcode)) {
            $autoShort = $converter->confuse($newId);

            $oldLink = $db->table('links')
                ->where('shortcode', $autoShort)
                ->get()
                ->getRow();

            if (! $oldLink) {
                $finalShortcode = $autoShort;
            } else {
                $base62OfOldLink = $converter->confuse($oldLink->id);

                if ($oldLink->shortcode === $base62OfOldLink) {
                    $db->transRollback();
                    return redirect()->back()->with('message', 'Error al generar el link 1');
                }

                $finalShortcode = $base62OfOldLink;

                $collisionCheck = $db->table('links')
                    ->where('shortcode', $base62OfOldLink)
                    ->get()
                    ->getRow();

                if ($collisionCheck) {
                    $db->transRollback();
                    return redirect()->back()->with('message', 'Error al generar el link 2');
                }
            }
        } else {
            $finalShortcode = $customShortcode;
        }

        if ($finalShortcode) {
            $linksModel
                ->where('id', $newId)
                ->set([
                    'shortcode' => $finalShortcode,
                    'updated_at' => null,
                ])
                ->update();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->with('message', 'Error al generar el link 3');
        }

        return redirect()->to(base_url('s/' . $finalShortcode));
    }
}
