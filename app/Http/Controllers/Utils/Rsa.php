<?php

namespace App\Http\Controllers\Utils;

/**
 * Class Rsa
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Utils
 */
class Rsa
{
    private static $PRIVATE_KEY = '';  //私钥（用于用户加密）
    private static $PUBLIC_KEY = '';   //公钥（用于服务端数据解密）
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Rsa
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Rsa constructor.
     * @param string $pubkey 公钥
     * @param string $prikey 私钥
     */
    public function __construct($pubkey='',$prikey='')
    {
        self::$PUBLIC_KEY = empty($pubkey) ? file_get_contents(public_path('/rsa/app_public_key.pem')) : file_get_contents($pubkey);
        self::$PRIVATE_KEY = empty($prikey) ? file_get_contents(public_path('/rsa/app_private_key.pem')) : file_get_contents($prikey);
    }

    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey()
    {
        $privateKey = self::$PRIVATE_KEY;
        return openssl_pkey_get_private($privateKey);
    }

    /**
     * TODO：获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $publicKey = self::$PUBLIC_KEY;
        return openssl_pkey_get_public($publicKey);
    }

    /**
     * TODO：私钥加密
     * @param string $data
     * @return null|string
     */
    public static function privateEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * TODO：公钥加密
     * @param string $data
     * @return null|string
     */
    public static function publicEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data,$encrypted,self::getPublicKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * TODO：私钥解密
     * @param string $encrypted
     * @return null
     */
    public static function privateDecrypt($encrypted = '')
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
    public static function publicDecrypt($encrypted = '')
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
    public static function makeSign($data)
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
        $signature = base64_encode($signature);
        return $signature;
    }

    /**
     * TODO：公钥验证签名
     * @param string $data 参数
     * @param string $signature 签名
     * @return int|null
     */
    public static function checkSign($data,$signature)
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
        $verify = openssl_verify($digest, base64_decode($signature), self::getPublicKey(), $algo);
        return $verify;
    }

    /**
     * TODO：构析函数，用来释放公钥和私钥
     */
    public function __destruct() {
        openssl_free_key(self::getPrivateKey());
        openssl_free_key(self::getPublicKey());
    }
}
