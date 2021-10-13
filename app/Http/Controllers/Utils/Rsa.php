<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;

/**
 * Class Rsa
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Utils
 */
class Rsa extends Controller
{
    /**
     * @var static $instance
     */
    protected static $instance;
    /**
     * @var false|string
     */
    protected $publicKey = '';
    /**
     * @var false|string
     */
    protected $privateKey = '';

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Rsa
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Rsa constructor.
     */
    public function __construct()
    {
        $this->privateKey = file_get_contents(public_path(config('app.rsa_private')));
        $this->publicKey = file_get_contents(public_path(config('app.rsa_public')));
    }

    /**
     * TODO:获取私钥
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        return openssl_pkey_get_private($this->privateKey);
    }

    /**
     * TODO：获取公钥
     * @return bool|resource
     */
    protected function getPublicKey()
    {
        return openssl_pkey_get_public($this->publicKey);
    }

    /**
     * TODO：私钥加密
     * @param string $data
     * @return null|string
     */
    public function privateEncrypt(string $data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_private_encrypt($data, $encrypted, self::getPrivateKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * TODO：公钥加密
     * @param string $data
     * @return null|string
     */
    public function publicEncrypt(string $data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data, $encrypted, self::getPublicKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * TODO：私钥解密
     * @param string $encrypted
     * @return null
     */
    public function privateDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey())) ? $decrypted : null;
    }

    /**
     * TODO：公钥解密
     * @param string $encrypted
     * @return null
     */
    public function publicDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;
    }

    /**
     * TODO：私钥生成签名
     * @param $data
     * @return string|null
     */
    public function makeSign($data)
    {
        if (!is_string($data)) {
            return null;
        }
        // 摘要及签名的算法
        $digestAlgo = 'sha512';
        $algo = OPENSSL_ALGO_SHA1;
        // 生成摘要
        $digest = openssl_digest($data, $digestAlgo);
        // 签名
        $signature = '';
        //生成签名
        openssl_sign($digest, $signature, self::getPrivateKey(), $algo);
        return base64_encode($signature);
    }

    /**
     * TODO：公钥验证签名
     * @param string $data 参数
     * @param string $signature 签名
     * @return int|null
     */
    public function checkSign($data, $signature)
    {
        if (!is_string($data)) {
            return null;
        }
        // 摘要及签名的算法，同上面一致
        $digestAlgo = 'sha512';
        $algo = OPENSSL_ALGO_SHA1;
        // 生成摘要
        $digest = openssl_digest($data, $digestAlgo);
        // 验签
        return openssl_verify($digest, base64_decode($signature), self::getPublicKey(), $algo);
    }
}
