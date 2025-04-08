<?php

namespace App\Controllers;

use App\ThirdParty\Base62Converter;
use DeviceDetector\DeviceDetector;
use MaxMind\Db\Reader;

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
            return redirect('/')->with('message', 'Link not found');

        // check if the link has a password
        if (!is_null($results[0]->password))
            return redirect()->to('p/' . $shortcode);

        // check if the link is expired
        // TO DO

        return $this->saveVisitAndRedirect($results[0]);
    }

    public function saveVisitAndRedirect($link)
    {
        $this->saveVisit($link);

        return redirect()->to($link->content);
    }

    private function saveVisit($link)
    {
        $dd = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
        $dd->parse();

        $ipAddress = $this->request->getIPAddress();
        $osName = empty($dd->getOs()) ? 'Unknown' : $dd->getOs()['name'] ?? 'Unknown';
        $deviceName = empty($dd->getDeviceName()) ? 'Unknown' : $dd->getDeviceName();
        $client = empty($dd->getClient()) ? 'Unknown' : $dd->getClient()['name'] ?? 'Unknown';
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        $reader = new Reader(APPPATH . 'ThirdParty/GeoLite2-Country.mmdb');
        $ip_info = $reader->get($this->request->getIPAddress());
        $reader->close();

        $country = $ip_info['country']['iso_code'] ?? null;
        $continent = $ip_info['continent']['code'] ?? null;

        $visitsModel = model('VisitsModel');

        // save visit log
        $result = $visitsModel->insert([
            'link_id' => $link->id,
            'ip_address' => $ipAddress,
            'continent_code' => $continent,
            'country_code' => $country,
            'browser' => $client,
            'device' => $deviceName,
            'os' => $osName,
            'referer' => $referer,
        ]);

        // save vistit in link
        $linksModel = model('LinksModel');
        $linksModel->where('id', $link->id)
            ->set([
                'visits' => $link->visits + 1,
                'last_visit' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ])
            ->update();


        // save daily visits
        $visitsDailyModel = model('VisitsDailyModel');

        $total_visits_today = $visitsDailyModel
            ->where('link_id', $link->id)
            ->where('date', date('Y-m-d'))
            ->first();

        if ($total_visits_today) {
            $visitsDailyModel
                ->where('link_id', $link->id)
                ->where('date', date('Y-m-d'))
                ->set(['visits' => $total_visits_today['visits'] + 1])
                ->update();
        } else {
            $visitsDailyModel->insert([
                'link_id' => $link->id,
                'date' => date('Y-m-d'),
                'visits' => 1,
            ]);
        }
    }

    public function protected($shortcode)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM `links` WHERE `shortcode` LIKE '" . $shortcode . "' LIMIT 1");
        $results = $query->getResult();

        if (count($results) == 0)
            return redirect()->to('/')->with('message', 'Link not found.');


        $link = $results[0];

        if (empty($link->password))
            return $this->saveVisitAndRedirect($link->content);

        if ($this->request->is('post')) {

            $password = $this->request->getPost('password');

            if (password_verify($password, $link->password)) {
                return $this->saveVisitAndRedirect($link->content);
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

        if (count($results) == 0)
            return redirect()->to('/')->with('message', 'Link not found.');


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
            'expiration_type' => 'permit_empty|in_list[time,visits]',
            'expiration_after' => 'permit_empty|integer',
            'expiration_unit' => 'permit_empty|in_list[minutes,hours,days,weeks,months]',
            'expiration_visits' => 'permit_empty|integer',
        ];

        $messages = [
            'shortcode' => [
                'is_unique' => 'The shortcode already exists. Try another one.',
            ],
        ];

        $data = $this->request->getPost(array_keys($rules));

        if (! $this->validateData($data, $rules, $messages))
            return redirect()->to('/')->withInput();


        $validData = $this->validator->getValidated();

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
            'content' => $validData['url'],
            'shortcode' => null,
            'is_custom_shortcode' => ! empty($customShortcode),
        ];

        if (! empty($validData['password']))
            $newData['password'] = password_hash($validData['password'], PASSWORD_BCRYPT);

        if (! empty($validData['expiration_type'])) {
            $newData['expiration_type'] = $validData['expiration_type'];

            if ($validData['expiration_type'] === 'time') {
                $newData['expiration_after'] = $validData['expiration_after'];
                $newData['expiration_unit'] = $validData['expiration_unit'];
            }

            if ($validData['expiration_type'] === 'visits') {
                $newData['expiration_after'] = $validData['expiration_visits'];
            }
        }

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

        return redirect()->to('s/' . $finalShortcode);
    }
}
