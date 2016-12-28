<?php

/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/20
 * Time: 17:20
 */

require_once("LxAESHandler.class.php");
require_once("LxRSAHandler.class.php");

class LxSecretManager
{

    private $appId;
    private $lxAESHandler;
    private $lxRSAHandler;

    /**
     * LxSecretManager constructor.
     * @param $appId
     * @param $pub_path
     * @param $pri_path
     */
    public function __construct($appId, $pri_path, $pub_path )
    {
        $this->appId = $appId;
        $this->lxAESHandler = new LxAESHandler();
        $this->lxRSAHandler = new LxRsaHandler($pri_path, $pub_path);
    }

    /**
     * 对Json串进行加密
     * @param $json
     * @return string 返回加密后的字符串
     */
    public function encrypt($json)
    {
        $key = $this->lxRSAHandler->encryptAES($this->lxAESHandler);
        $content = $this->lxAESHandler->encrypt($json);
        $sign = $this->lxAESHandler->encrypt($this->sign_data($json));
        $ret_map = array("key" => $key, "content" => $content,"sign" => $sign);
        return json_encode($ret_map);
    }

    /**
     * 对云合同返回的json进行解密
     * @param $json
     * @return string 返回解密后的json对象
     */
    public function decrypt($json)
    {
        $data = json_decode($json,true);

        if (!array_key_exists('key', $data)){
            return $json;
        }
        $session_key = $data["key"];
        $session_key = $this->lxRSAHandler->decryptRSA($session_key);
        $aesHandler = new LxAESHandler($session_key);
        return $aesHandler->decrypt($data["content"]);
    }

    /**
     * 对数据进行签名
     * @param $data
     * @return string
     */
    public function sign_data($data){
        $sign = $this->lxRSAHandler->sign_data($data);
        return $sign;
    }

}