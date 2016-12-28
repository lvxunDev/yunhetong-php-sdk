<?php
/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/18
 * Time: 15:25
 */
include_once("../library/LxSecretManager.class.php");
header('Content-type: application/json;charset=utf-8');

if (isset($_GET["contractId"])) {
    $contractId = $_GET["contractId"];
}else{
    echo "请输入合同id";
    return;
}
$sdk_manager = R::getLxSDKManager();
echo $sdk_manager->invalidContract($contractId);

exit;
