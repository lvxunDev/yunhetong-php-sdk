<?php

/**
 * RSA 加密的相关方法
 * User: Seanwu
 * Date: 2016/5/20
 * Time: 16:53
 */
class LxRsaHandler
{

    /**
     * 公钥
     * @var bool|resource
     */
    private $publicKey;
    /**
     * 私钥
     * @var bool|resource
     */
    private $privateKey;

    /**
     * LxRsaHandler constructor.
     * @param $pri_path
     * @param $pub_path
     */
    public function __construct($pri_path, $pub_path)
    {
        $pri_file = fopen($pri_path, "r") or die("Unable to open file!");
        $pub_file = fopen($pub_path, "r") or die("Unable to open file!");
        $pri_content =  fread($pri_file, filesize ($pri_path));       // pub
        $pub_content =  fread($pub_file, filesize ($pub_path));       // pri
        $privateKey = $this->setupPriKey(trim($pri_content));
        $publicKey = $this->setupPubKey(trim($pub_content));
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        fclose($pri_file);
        fclose($pub_file);
    }

    /**
     * 将java格式的私钥转化为php格式的私钥
     * @param $der
     * @return bool|resource php格式的私钥
     */
    private function setupPriKey($der)
    {
        static $BEGIN_MARKER = "-----BEGIN PRIVATE KEY-----";
        static $END_MARKER = "-----END PRIVATE KEY-----";
        $value = $der;
        $pem = $BEGIN_MARKER . "\n";
        $pem .= chunk_split($value, 64, "\n");
        $pem .= $END_MARKER . "\n";
        return openssl_pkey_get_private($pem);
    }

    /**
     * 将java格式的公钥转化为php格式的公钥
     * @param $pub_key
     * @return bool|resource php格式的公钥
     */
    private function setupPubKey($pub_key)
    {
        if (is_resource($pub_key)) {
            return true;
        }
        $pem = chunk_split($pub_key, 64, "\n");
        $pem = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
        $pub_key = openssl_pkey_get_public($pem);
        return $pub_key;
    }

    /**
     * AES 加密
     * @param $aesHandler
     * @return string 返回加密后的字符串
     */
    public function encryptAES($aesHandler)
    {
        // 这里要加格式判断 TODO
        openssl_public_encrypt((string)$aesHandler, $encrypted, $this->publicKey);
        return base64_encode($encrypted);
    }

    /**
     * AES 解密
     * @param $data
     *      要解密的字符串
     * @return string 返回加密后的字符串
     */
    public function decryptRSA($data)
    {
        if (!$data) return $data;
        openssl_private_decrypt(base64_decode($data), $decrypted, $this->privateKey);
        return $decrypted;
    }

    /**
     * 对一串数据进行签名
     * @param $data
     * @return string 返回签名后的数据
     */
    public function sign_data($data){
        openssl_sign($data, $sign, $this->privateKey, OPENSSL_ALGO_SHA1);
        return base64_encode($sign);
    }
}