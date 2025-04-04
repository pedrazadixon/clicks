<?php

namespace App\Controllers;

use App\ThirdParty\Base62Converter;

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
            'shortcode' => 'permit_empty|alpha_dash|max_length[50]|min_length[4]|is_unique[links.shortcode]',
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

        // Tomar el shortcode personalizado si el usuario lo ingresó
        $customShortcode = $validData['shortcode'] ?? null;

        // Conexión a BD
        $db = \Config\Database::connect();
        $builder = $db->table('links');

        $secret = 'dIxOnPvmT4GwQtpcqs1SgJCkMlaYBuRKmiWAbZ358fLjU62EF7HV0z9heryDiXoN';
        $converter = new Base62Converter($secret, 4);

        // Usamos una transacción por seguridad
        $db->transStart();

        // 1. Insertar el nuevo enlace (sin shortcode si no es personalizado)
        $newData = [
            'content' => $url,
            'shortcode' => null,
            'is_custom_shortcode' => ! empty($customShortcode),
        ];

        // Insertamos
        $builder->insert($newData);

        // Obtenemos el ID recién insertado
        $newId = $db->insertID();

        // Se define una variable para el shortcode definitivo
        $finalShortcode = null;

        // 2. Si no había shortcode personalizado, generamos uno con Base62
        if (empty($customShortcode)) {
            // Genera el shortcode base62 a partir del ID
            $autoShort = $converter->confuse($newId);

            // Verificamos si hay colisión
            $oldLink = $db->table('links')
                ->where('shortcode', $autoShort)
                ->get()
                ->getRow();

            if (! $oldLink) {
                // Sin colisión -> podemos usarlo normalmente
                $finalShortcode = $autoShort;
            } else {
                // HAY colisión. 
                // 3. El enlace viejo está usando "autoShort" (ej. "app1")
                //    que es personalizado. 
                // Asumimos tu regla: si el viejo link "oldLink" usó un
                // shortcode personalizado, obtenemos el base62 "nativo" 
                // de ese ID viejo para asignarlo AL NUEVO.

                // Verificar si efectivamente ES un shortcode personalizado
                // (No coincide con confuse($oldLink->id))
                $base62OfOldLink = $converter->confuse($oldLink->id);

                if ($oldLink->shortcode === $base62OfOldLink) {
                    // Significa que su shortcode NO es personalizado,
                    // está usando el que corresponde a su ID.
                    // Entonces no podemos hacer la “estrategia” 
                    // (tendrías que buscar plan B, offset, etc.)
                    // Podrías rechazar aquí o generar un offset...
                    $db->transRollback();
                    return redirect()->back()->with('message', 'Error al generar el link 1');
                    // throw new \Exception('Colisión con un enlace que NO usa shortcode personalizado.');
                }

                // Caso contrarío: El oldLink->shortcode es algo distinto
                // => Se asume que es “personalizado”
                // => su "código base62 nativo" (confuse(oldLink->id)) 
                //    nunca se usó realmente en la BD.

                // Asignamos ese “código base62 nativo” al nuevo link
                // El viejo link NO se modifica (seguirá con "app1")
                $finalShortcode = $base62OfOldLink;

                // Pero hay que asegurarse de que $base62OfOldLink 
                // no choque con nadie más:
                $collisionCheck = $db->table('links')
                    ->where('shortcode', $base62OfOldLink)
                    ->get()
                    ->getRow();

                if ($collisionCheck) {
                    // El "base62OfOldLink" también está en uso en otra parte
                    // Toca plan B, offset, etc. 
                    $db->transRollback();
                    return redirect()->back()->with('message', 'Error al generar el link 2');
                    // throw new \Exception("Incluso el base62 nativo del ID viejo ya se usa en otra fila.");
                }
            }
        } else {
            // El usuario proporcionó un shortcode personalizado
            // => asumimos que pasa la validación "is_unique", 
            // => no hay colisión y ya quedó guardado en la inserción
            $finalShortcode = $customShortcode;
        }

        if ($finalShortcode) {
            // Hacemos un update al nuevo registro para fijar el shortcode final
            $builder->where('id', $newId)
                ->update(['shortcode' => $finalShortcode]);
        }

        // confirmamos la transacción
        $db->transComplete();

        if ($db->transStatus() === false) {
            // algo salió mal
            $db->transRollback();
            return redirect()->back()->with('message', 'Error al generar el link 3');
        }

        // Si todo va bien, redireccionamos o retornamos una vista con éxito
        return redirect()->to(base_url('s/' . $finalShortcode));
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
