<?php
/**
 * This class generate a short token value 
 */
namespace App\Service;

class TokenGenerator
{
    public function getToken()
    {
        $token = bin2hex(openssl_random_pseudo_bytes(6));

        return $token;
    }

}
