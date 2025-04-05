<?php

namespace App\ThirdParty;

/**
 * Base62Converter
 */
class Base62Converter
{
    public string $base62CharSet;
    public int $codeLength;

    /**
     * Constructor
     * 
     * @param array $options Configuration options
     */
    public function __construct(array $options)
    {
        $this->base62CharSet = $options['Secrect'];
        $this->codeLength = $options['CodeLength'];
    }

    /**
     * Get the length of zeros to pad
     * 
     * @return int
     */
    private function getZeroLength(): int
    {
        return strlen((string)$this->getMaxValue());
    }

    /**
     * Get maximum value for the current code length
     * 
     * @return int
     */
    private function getMaxValue(): int
    {
        $max = pow(62, $this->codeLength) - 1;
        return pow(10, strlen((string)$max) - 1) - 1;
    }

    /**
     * Confuse an ID to create a short code
     * 1. Pad the ID with leading zeros
     * 2. Reverse the digits
     * 3. Convert the reversed decimal to base62
     * 
     * @param int $id The ID to confuse
     * @return string The confused short code
     * @throws \Exception If ID exceeds maximum value
     */
    public function confuse(int $id): string
    {

        while ($id > $this->getMaxValue()) {
            $this->codeLength++;
        }

        // Pad with zeros and reverse
        $paddedId = str_pad((string)$id, $this->getZeroLength(), '0', STR_PAD_LEFT);
        $reversedId = intval(strrev($paddedId));

        // Encode to base62
        $base62Str = $this->encode($reversedId);

        // Pad with the first character of the charset if needed
        return str_pad($base62Str, $this->codeLength, $this->base62CharSet[0], STR_PAD_LEFT);
    }

    /**
     * Recover the original ID from a confused short code
     * 1. Convert base62 to decimal (gets the reversed ID)
     * 2. Reverse the digits to get original ID
     * 
     * @param string $key The confused short code
     * @return int The recovered ID
     */
    public function recoverConfuse(string $key): int
    {
        if (strlen($key) != $this->codeLength) {
            return 0;
        }

        $confuseId = $this->decode($key);
        $reversedId = str_pad((string)$confuseId, $this->getZeroLength(), '0', STR_PAD_LEFT);
        $id = intval(strrev($reversedId));

        return $id > $this->getMaxValue() ? 0 : $id;
    }

    /**
     * Convert decimal to base62
     * 
     * @param int $value The decimal value
     * @return string The base62 string
     * @throws \ArgumentOutOfRangeException If value is negative
     */
    public function encode(int $value): string
    {
        if ($value < 0) {
            throw new \InvalidArgumentException("Value must be greater than or equal to zero");
        }

        $result = '';
        do {
            $result = $this->base62CharSet[$value % 62] . $result;
            $value = intdiv($value, 62);
        } while ($value > 0);

        return $result;
    }

    /**
     * Convert base62 to decimal
     * 
     * @param string $value The base62 string
     * @return int The decimal value
     * @throws \InvalidArgumentException If the input contains invalid characters
     */
    public function decode(string $value): int
    {
        $result = 0;
        for ($i = 0; $i < strlen($value); $i++) {
            $power = strlen($value) - $i - 1;
            $digit = strpos($this->base62CharSet, $value[$i]);

            if ($digit === false) {
                throw new \InvalidArgumentException("Invalid character in base62 string");
            }

            $result += $digit * pow(62, $power);
        }

        return $result;
    }

    /**
     * Generate a random base62 character set
     * 
     * @return string The generated secret
     */
    public static function generateSecret(): string
    {
        $chars = explode(',', '0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z');
        shuffle($chars);
        return implode('', $chars);
    }
}
