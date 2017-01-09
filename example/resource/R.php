<?php
$root_path = $_SERVER['DOCUMENT_ROOT'];
include_once($root_path . "/library/LxSDKManager.class.php");
include_once($root_path . "/library/model/LxUser.php");
include_once($root_path . "/library/model/Actor.php");
include_once($root_path . "/library/model/Contract.php");

/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/12/28
 * Time: 14:55
 */
class R
{

    // 第三方应用的appId
    public static $appId = "2016121514373700002";

    /**
     * @return LxSDKManager 返回SDKManager
     */
    public static function getLxSDKManager()
    {
        $current_path = realpath(dirname(__FILE__));
        return new LxSDKManager(self::$appId, $current_path . "\\key\\yhtSK.pem", $current_path . "\\key\\rsa_private_key_pkcs8.pem");
    }

    /**
     * 创建测试合同
     * @return Contract
     */
    public static function getTestContract()
    {
        $contract = new Contract();
        $contract->appId = self::$appId;
        $contract->title = "测试合同标题" . date("s");
        $contract->overtime = time() * 1000;   // 因为 java 的时间跟php的时间不大一样，所以这里 *1000
        $contract->defContractNo = "随便写";
        $contract->templateId = 123456;
        $contract->params = R::getContractParams();
        return $contract;
    }

    /**
     * 获取测试合同的参数
     * @return array
     */
    public static function getContractParams()
    {
        $params = array();
        $params["\${nameA}"] = "nameA";
        return $params;
    }

    /**
     * 获取合同的参与方
     * @return array
     */
    public static function getActor()
    {
        return array(R::actorA(), R::actorB());
    }

    /**
     * 获取合同的参与方 A
     * @return Actor
     */
    public static function actorA()
    {
        $actor = new Actor();
        $actor->user = R::getUserA();
        $actor->locationName = "signA";   // 签名位置，在模板那边可以设置
        $actor->autoSign = 0;      // 是否自动签署合同0不自动签1自动签署
        return $actor;
    }

    /**
     * 获取合同的参与方 B
     * @return Actor
     */
    public static function actorB()
    {
        $actor = new Actor();
        $actor->user = R::getUserB();
        $actor->locationName = "signB";
        $actor->autoSign = 0;
        return $actor;
    }

    /**
     * 获取测试用户A
     * @return LxUser
     */
    public static function getUserA()
    {
        $user = new LxUser();
        $user->appId = self::$appId;
        $user->appUserId = 'phpTestUserA1';
        $user->userType = 1;
        $user->cellNum = '11111111111';
        $user->userName = 'TestA';
        $user->certifyType = 2;
        $user->certifyNumber = '52059487';
        $user->createSignature = "0";
        return $user;
    }


    /**
     * 获取测试用户A
     * @return LxUser
     */
    public static function getUserB()
    {
        $user = new LxUser();
        $user->appId = self::$appId;
        $user->appUserId = 'phpTestUserB';
        $user->userType = 1;
        $user->cellNum = '11111111122';
        $user->userName = 'TestB';
        $user->certifyType = 2;
        $user->certifyNumber = '52059487';
        $user->createSignature = "0";
        return $user;
    }

}