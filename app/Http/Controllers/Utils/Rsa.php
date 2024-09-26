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
        //  Implement __clone() method.
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
     * 获取私钥
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        return openssl_pkey_get_private($this->privateKey);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    protected function getPublicKey()
    {
        return openssl_pkey_get_public($this->publicKey);
    }

    /**
     * 私钥加密
     * @param string $data
     * @return null|string
     */
    public function privateEncrypt(string $data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        $split = str_split($data, 117);  // 1024 bit && OPENSSL_PKCS1_PADDING  不大于117即可
        $crypto = '';
        foreach ($split as $item) {
            $crypto.= openssl_private_encrypt($item, $encrypted, self::getPrivateKey()) ? $encrypted : null;
        }
        return base64_encode($crypto);
    }

    /**
     * 公钥解密
     * @param string $encrypted
     * @return null
     */
    public function publicDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        $split = str_split(base64_decode($encrypted), 128);  // 1024 bit  固定128
        $crypto = '';
        foreach ($split as $item) {
            $crypto .= openssl_public_decrypt($item, $decrypted, self::getPublicKey()) ? $decrypted : null;
        }
        return $crypto;
    }

    /**
     * 公钥加密
     * @param string $data
     * @return null|string
     */
    public function publicEncrypt(string $data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        $split = str_split($data, 117);  // 1024 bit && OPENSSL_PKCS1_PADDING  不大于117即可
        $crypto = '';
        foreach ($split as $item) {
            $crypto .= openssl_public_encrypt($item, $encrypted, self::getPublicKey()) ? $encrypted : null;
        }
        return base64_encode($crypto);
    }

    /**
     * 私钥解密
     * @param string $encrypted
     * @return null
     */
    public function privateDecrypt(string $encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        $split = str_split(base64_decode($encrypted), 128);  // 1024 bit  固定128
        $crypto = '';
        foreach ($split as $item) {
            $crypto .= openssl_private_decrypt($item, $decrypted, self::getPrivateKey()) ? $decrypted : null;
        }
        return $crypto;
    }

    /**
     * 私钥生成签名
     * @param string $data
     * @return string|null
     */
    public function makeSign(string $data)
    {
        if (!is_string($data)) {
            return null;
        }
        // 摘要及签名的算法
        $digestAlgo = 'sha512';
        // 生成摘要
        $digest = openssl_digest($data, $digestAlgo);
        // 签名
        $signature = '';
        //生成签名
        openssl_sign($digest, $signature, self::getPrivateKey(), OPENSSL_ALGO_SHA1);
        return base64_encode($signature);
    }

    /**
     * 公钥验证签名
     * @param string $data 参数
     * @param string $signature 签名
     * @return int|null
     */
    public function checkSign(string $data, string $signature)
    {
        if (!is_string($data)) {
            return null;
        }
        // 摘要及签名的算法，同上面一致
        $digestAlgo = 'sha512';
        // 生成摘要
        $digest = openssl_digest($data, $digestAlgo);
        // 验签
        return openssl_verify($digest, base64_decode($signature), self::getPublicKey(), OPENSSL_ALGO_SHA1);
    }
}
