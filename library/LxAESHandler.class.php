<?php

/**
 * AES 加密的相关方法
 * @author 浣溪沙
 * Date: 2016/5/18
 * Time: 17:39
 */
class LxAESHandler
{

    private $secretKey;
    private $iv;
    private $bt;

    /**
     * LxAESHandler constructor.
     * @param $key
     */
    public function __construct($key = '')
    {

        if (!$key) {
            $this->refresh_key();

        } else {
            // TODO 这里要加上 $key 格式不正确的校验 和bt的时间校验
            $key = json_decode($key, true);
            $this->secretKey = base64_decode($key['key']);
            $this->iv = base64_decode($key['iv']);
            $this->bt = $key['bt'];
        }
    }

    /**
     * 加密
     * @param $data
     * @return string 密文
     */
    function encrypt($data)
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $data = $this->pkcs5_pad($data, $size);
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->secretKey, $data,
            MCRYPT_MODE_CBC, $this->iv);
        return base64_encode($encrypted);
    }

    private function pkcs5_pad($text, $block_size)
    {
        $pad = $block_size - (strlen($text) % $block_size);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**解密
     * @param $encrypted
     * @return string
     */
    function decrypt($encrypted)
    {
        // echo $this->iv;
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->secretKey, base64_decode($encrypted),
            MCRYPT_MODE_CBC, $this->iv);
        return $this->pkcs5_unpad($decrypted);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    private function refresh_key()
    {
        $this->iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $this->secretKey = self::key_generator();
        $this->bt = time() * 1000;    // java的时间戳是13位的， php 的时间戳是10位的，所以这里要加上3位。
    }

    /**
     * toString 方法，由于key和iv生成的可能会是乱码，所以在传输的时候base64一下
     * @return string
     */
    function __toString()
    {
        //  todo mark it
        $map = array("key" => base64_encode($this->secretKey), "iv" => base64_encode($this->iv), "bt" => $this->bt);
        return json_encode($map);
    }

    /**
     * 随机生成 AES 的 key
     * @param int $pw_length
     * @return string
     */
    private function key_generator($pw_length = 16)
    {
        $randpwd = '';
        for ($i = 0; $i < $pw_length; $i++) {
            $randpwd .= chr(mt_rand(33, 126));
        }
        return $randpwd;
    }
}
