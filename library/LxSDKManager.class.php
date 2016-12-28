<?php
/**
 * User: SeanWu
 * Date: 2016/5/18
 * Time: 9:34
 */

include_once("LxSecretManager.class.php");
include_once("model/User.php");
include_once("model/ReceiveObj.php");
include_once("Http.class.php");

/**
 * Class SDKManager
 * 对 sdk 功能进行管理的类
 */
class LxSDKManager
{

    private $appId;
    private $pub_path;
    private $pri_path;
    private $lx_secret_manager;
    private $host = "sdk.yunhetong.com/sdk";
    private $app_id;


    /**
     * SDKManager constructor.
     *     构造函数，初始话appId，公钥，私钥等信息
     * @param $appId
     *      应用Id
     * @param $pub_path
     *     公钥文件地址
     * @param $pri_path
     *     私钥文件地址
     */
    public function __construct($appId, $pub_path, $pri_path)
    {
        $this->app_id = $appId;
        $this->pub_path = $pub_path;
        $this->pri_path = $pri_path;
        $this->lx_secret_manager = new LxSecretManager($this->appId, $this->pri_path, $this->pub_path);
    }

    /**
     * 对要发给运合同的json进行加密
     * @param $json
     *     要加密的json
     * @return string 返回加密后的字符串
     */
    public function encrypt($json)
    {
        return $this->lx_secret_manager->encrypt(json_encode($json));
    }

    /**
     * 对云合同返回的json进行解密
     * @param $data
     *     要解密的数据
     * @return string  返回解密后的数据
     */
    public function decrypt($data)
    {
        return $this->lx_secret_manager->decrypt($data);
    }

    /**
     * 导入用户获取Token的方法
     * @param $user
     *     要导入的用户
     * @return string  返回运合同服务器的结果（解密后）
     */
    public function sync_get_token($user)
    {
        $url = "/third/tokenWithUser";
        $current_user = array("currentUser" => $user);
        $secret = $this->lx_secret_manager->encrypt(json_encode($current_user));
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');
        return $this->lx_secret_manager->decrypt($result);
    }

    /**
     * 修改用户信息的接口
     * @param $user
     *     要修改的用户，只能更新用户的电话号码和用户名
     * @return string  返回运合同服务器的结果（解密后）
     */
    public function update_user($user)
    {
        $url = "/third/userUpdate";
        $secret = $this->lx_secret_manager->encrypt(json_encode($user));
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');
        return $this->lx_secret_manager->decrypt($result);
    }

    /**
     * 通过创建合同来获取Token
     * @param $current_user
     *     合同的发起方
     * @param $contract
     *     合同实体类
     * @param $actors
     *     合同的参与者，Actor 数组
     * @return string 返回运合同服务器的结果（解密后）
     */
    public function sync_get_token_with_contract($current_user, $contract,/*这个要是数组*/
                                                 $actors)
    {
        $url = "/third/tokenWithContract";
        //  todo 类型判断
        $contract_form_vo = array("vo" => $contract, "attendUser" => $actors);
        $contract_info = array("currentUser" => $current_user, "contractFormVo" => $contract_form_vo);
        $secret = $this->lx_secret_manager->encrypt(json_encode($contract_info));
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');
        return $this->lx_secret_manager->decrypt($result);
    }

    /**
     * 创建合同
     * @param $contract
     * @param $actors
     * @return string  返回运合同服务器的结果（解密后）
     */
    public function sync_create_contract($contract, $actors)
    {
        $url = "/third/autoContract";
        $contract_form_vo = array("vo" => $contract, "attendUser" => $actors);
        $contract_info = array("contractFormVo" => $contract_form_vo);
        $secret = $this->lx_secret_manager->encrypt(json_encode($contract_info));
        // todo 这里加log
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');

        return $this->lx_secret_manager->decrypt($result);
    }

    /**
     * 查询合同
     * @param $pageSize
     *     每页的大小
     * @param $pageNum
     *     页数
     * @return string 返回运合同服务器的结果（解密后）
     */
    public function query_contracts($pageSize, $pageNum)
    {
        $url = "/third/listContract";
        $query_param = array(
            "flag" => time() * 1000,
            "pageSize" => $pageSize < 10 ? 10 : $pageSize,
            "pageNum" => $pageNum < 1 ? 1 : $pageNum
        );
        $secret = $this->lx_secret_manager->encrypt(json_encode($query_param));
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');
        return $this->lx_secret_manager->decrypt($result);
    }

    /**
     * 对与合同发来的签署信息进行签名
     * @param $data
     * @return string 返回签名后的数据
     */
    public function sign_data($data)
    {
        $content = json_decode($data, true);
        if ($content["noticeType"] == 1) {//1时表示普通合同签署
            return json_encode(array('response' => true, 'msg' => 'ok'));
        } else if ($content["noticeType"] == 2) {//2时表示合同签署完成
            $sign = $this->lx_secret_manager->sign_data($content["signDigest"]);
            $ret_array = array(
                'response' => true,
                'msg' => 'ok',
                "signDigest" => $this->lx_secret_manager->encrypt(json_encode($sign))
            );
            return $this->lx_secret_manager->encrypt(json_encode($ret_array));
        } else {
            return json_encode(array('response' => false, 'msg' => '不是签署成功的合同的数据，不需要签名！'));
        }
    }

    /**
     * 下载合同的方法
     * @param $contractId
     * @return array
     */
    public function downloadContract($contractId)
    {
        $url = "/third/download";
        $data = array("contractId" => $contractId, "timestamp" => time() * 1000);
        $secret = $this->lx_secret_manager->encrypt(json_encode($data));
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');
        return array(
            'success' => $result['httpInfo']['content_type'] == 'application/octet-stream;charset=UTF-8',
            'body' => $result['body']
        );
    }

    /**
     * 作废合同的方法
     * @param $contractId
     * @return string
     */
    public function invalidContract($contractId)
    {
        $url = "/third/invalidContract";
        $data = array("contractId" => $contractId, "timestamp" => time() * 1000);
        $secret = $this->lx_secret_manager->encrypt(json_encode($data));
        $data = array("appid" => $this->app_id, "secret" => $secret);
        $result = Http::send_request($this->host . $url, $data, '', 'post');
        return $this->lx_secret_manager->decrypt($result);
    }

}