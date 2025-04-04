<?php

namespace App\ThirdParty;

use Exception;

class Base62Converter
{
    public string $base62CharSet; // Cadena de caracteres en "orden mezclado"
    public int $codeLength; // Longitud fija de la "clave corta"

    public function __construct(string $secret, int $codeLength)
    {
        $this->base62CharSet = $secret;
        $this->codeLength = $codeLength;
    }

    /**
     * Calcula el valor máximo según la longitud de la clave (codeLength).
     * En el algoritmo original se usa: 
     *   max = (62^codeLength) - 1 -> valor con cierto número de dígitos.
     */
    private function getMaxValue(): int
    {
        return (int)(pow(62, $this->codeLength) - 1);
    }

    /**
     * Obtiene la longitud de ceros para rellenar.
     * Coincide con la cantidad de dígitos en getMaxValue().
     */
    private function getZeroLength(): int
    {
        return strlen((string)$this->getMaxValue());
    }

    /**
     * 1) Rellenar el id a la izquierda con ceros hasta getZeroLength().
     * 2) Convertir ese número decimal invertido a base62.
     * 3) Rellenar a la izquierda (si es necesario) hasta $codeLength 
     *    con el primer caracter del set base62.
     */
    public function confuse(int $id): string
    {
        $maxValue = $this->getMaxValue();
        if ($id > $maxValue) {
            throw new Exception("El valor $id excede el máximo permitido ($maxValue).", 5);
        }

        // 1) Rellenar a la izquierda con ceros
        $zeroLength = $this->getZeroLength();
        $padded = str_pad((string)$id, $zeroLength, '0', STR_PAD_LEFT);

        // 2) Convertir a base62 con el set custom
        $confuseId = (int)$padded; // parseamos a entero
        $base62Str = $this->encode($confuseId);

        // 3) Rellenar con el primer carácter de la cadena base62
        return str_pad($base62Str, $this->codeLength, $this->base62CharSet[0], STR_PAD_LEFT);
    }

    /**
     * 1) Decodificar la cadena base62 custom a un número decimal.
     * 2) Rellenar con ceros a la izquierda hasta getZeroLength().
     * 3) Obtener el número original.
     */
    public function recoverConfuse(string $key): int
    {
        if (strlen($key) !== $this->codeLength) {
            // En el código C# retorna 0 o lanza excepción. Se puede manejar a conveniencia.
            return 0;
        }

        $confuseId = $this->decode($key);

        $id = (int)$confuseId; // Ya es el ID

        // Validar también que no sea mayor al máximo
        if ($id > $this->getMaxValue()) {
            return 0;
        }
        return $id;
    }

    /**
     * Convierte un entero decimal a base62 utilizando $this->base62CharSet.
     */
    private function encode(int $value): string
    {
        if ($value < 0) {
            throw new Exception("El valor debe ser mayor o igual a 0");
        }
        $result = '';
        do {
            $digit = $value % 62;
            $result = $this->base62CharSet[$digit] . $result;
            $value = (int)($value / 62);
        } while ($value > 0);

        return $result;
    }

    /**
     * Convierte una cadena base62 (con el set custom) a un número decimal.
     */
    private function decode(string $value): int
    {
        $length = strlen($value);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $pos = strpos($this->base62CharSet, $value[$i]);
            if ($pos === false) {
                throw new Exception("Carácter inválido en la cadena base62: " . $value[$i]);
            }
            $power = $length - $i - 1;
            $result += $pos * (int)pow(62, $power);
        }
        return $result;
    }

    /**
     * Genera una clave "secreta" de 62 caracteres que contendrá 0-9, a-z, A-Z en orden aleatorio.
     */
    public static function generateSecret(): string
    {
        // Conjunto de 62 caracteres (10 dígitos + 26 minúsculas + 26 mayúsculas)
        $chars = array_merge(range('0', '9'), range('A', 'Z'), range('a', 'z'));
        shuffle($chars);
        return implode('', $chars);
    }
}
