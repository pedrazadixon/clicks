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
        $linksModel = model('LinksModel');
        $link = $linksModel->where('shortcode', $shortcode)->first();

        if (is_null($link))
            return redirect('/')->with('message', 'Link not found');

        // check if the link has a password
        if (!is_null($link['password']))
            return redirect()->to('p/' . $shortcode);

        // check if the link is expired
        if ($link['expiration_type'] == 'time') {
            $expirationDate = date('Y-m-d H:i:s', strtotime($link['created_at'] . ' + ' . $link['expiration_after'] . ' ' . $link['expiration_unit']));
            if (date('Y-m-d H:i:s') > $expirationDate) {
                return redirect()->to('/')->with('message', 'Link expired');
            }
        }

        if ($link['expiration_type'] == 'visits') {
            if ($link['visits'] >= $link['expiration_after']) {
                return redirect()->to('/')->with('message', 'Link expired');
            }
        }

        return $this->saveVisitAndRedirect($link);
    }

    public function saveVisitAndRedirect($link)
    {
        $this->saveVisit($link);

        return redirect()->to($link['content']);
    }

    private function getIpFromServer()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            if (filter_var(explode(',', $_SERVER['HTTP_X_REAL_IP'])[0], FILTER_VALIDATE_IP))
                return explode(',', $_SERVER['HTTP_X_REAL_IP'])[0];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (filter_var(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0], FILTER_VALIDATE_IP))
                return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            if (filter_var(explode(',', $_SERVER['HTTP_CF_CONNECTING_IP'])[0], FILTER_VALIDATE_IP))
                return explode(',', $_SERVER['HTTP_CF_CONNECTING_IP'])[0];
        }

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            if (filter_var(explode(',', $_SERVER['HTTP_CF_CONNECTING_IP'])[0], FILTER_VALIDATE_IP))
                return explode(',', $_SERVER['HTTP_CF_CONNECTING_IP'])[0];
        }

        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    private function saveVisit($link)
    {
        $dd = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
        $dd->parse();

        $ipAddress = $this->getIpFromServer();
        $osName = empty($dd->getOs()) ? 'Unknown' : $dd->getOs()['name'] ?? 'Unknown';
        $deviceName = empty($dd->getDeviceName()) ? 'Unknown' : $dd->getDeviceName();
        $client = empty($dd->getClient()) ? 'Unknown' : $dd->getClient()['name'] ?? 'Unknown';
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        $reader = new Reader(APPPATH . 'ThirdParty/GeoLite2-Country.mmdb');
        $ip_info = $reader->get($ipAddress);
        $reader->close();

        $country = $ip_info['country']['iso_code'] ?? null;
        $continent = $ip_info['continent']['code'] ?? null;

        $visitsModel = model('VisitsModel');

        // save visit log
        $visitsModel->insert([
            'link_id' => $link['id'],
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
        $linksModel->where('id', $link['id'])
            ->set([
                'visits' => $link['visits'] + 1,
                'last_visit' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ])
            ->update();


        // save daily visits
        $visitsDailyModel = model('VisitsDailyModel');

        $total_visits_today = $visitsDailyModel
            ->where('link_id', $link['id'])
            ->where('date', date('Y-m-d'))
            ->first();

        if ($total_visits_today) {
            $visitsDailyModel
                ->where('link_id', $link['id'])
                ->where('date', date('Y-m-d'))
                ->set(['visits' => $total_visits_today['visits'] + 1])
                ->update();
        } else {
            $visitsDailyModel->insert([
                'link_id' => $link['id'],
                'date' => date('Y-m-d'),
                'visits' => 1,
            ]);
        }
    }

    public function protected($shortcode)
    {
        $linksModel = model('LinksModel');
        $link = $linksModel->where('shortcode', $shortcode)->first();

        if (is_null($link))
            return redirect()->to('/')->with('message', 'Link not found.');


        if (empty($link['password']))
            return $this->saveVisitAndRedirect($link['content']);

        if ($this->request->is('post')) {

            $password = $this->request->getPost('password');

            if (password_verify($password, $link['password'])) {
                return $this->saveVisitAndRedirect($link['content']);
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
        $linksModel = model('LinksModel');
        $link = $linksModel->where('shortcode', $shortcode)->first();


        if (is_null($link))
            return redirect()->to('/')->with('message', 'Link not found.');

        return view('share', [
            'link' => $link,
        ]);
    }

    public function generate()
    {
        if (! $this->request->is('post'))
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');

        if (! $this->validateData($this->request->getPost(), 'form_rules'))
            return redirect()->to('/')->withInput();

        if ($this->request->getPost('submit') === 'url' || $this->request->getPost('submit') === 'qr')
            if (! $this->validateData($this->request->getPost(), 'url_rules'))
                return redirect()->to('/')->withInput();

        if ($this->request->getPost('submit') === 'note')
            if (! $this->validateData($this->request->getPost(), 'note_rules'))
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
            'content' => $validData['content'],
            'type' => $this->request->getPost('submit'),
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
