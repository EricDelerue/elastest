<?php
declare(strict_types=1);


// PHP String Encryption Example with Libsodium
// If you are on PHP < 7.2 or otherwise do not have libsodium installed, you can use sodium_compat to accomplish the same result (albeit slower).

/**
 * Encrypt a message
 * 
 * @param string $message - message to encrypt
 * @param string $key - encryption key
 * @return string
 * @throws RangeException
 */
function safeEncrypt(string $message, string $key): string
{
    if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
        throw new RangeException('Key is not the correct size (must be 32 bytes).');
    }
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

    $cipher = base64_encode(
        $nonce.
        sodium_crypto_secretbox(
            $message,
            $nonce,
            $key
        )
    );
    sodium_memzero($message);
    sodium_memzero($key);
    return $cipher;
}

/**
 * Decrypt a message
 * 
 * @param string $encrypted - message encrypted with safeEncrypt()
 * @param string $key - encryption key
 * @return string
 * @throws Exception
 */
function safeDecrypt(string $encrypted, string $key): string
{   
    $decoded = base64_decode($encrypted);
    $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

    $plain = sodium_crypto_secretbox_open(
        $ciphertext,
        $nonce,
        $key
    );
    if (!is_string($plain)) {
        throw new Exception('Invalid MAC');
    }
    sodium_memzero($ciphertext);
    sodium_memzero($key);
    return $plain;
}


function RandomToken($length = 32){
    if(!isset($length) || intval($length) <= 8 ){
      $length = 32;
    }
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length));
    }
    if (function_exists('mcrypt_create_iv')) {
        return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
    } 
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}