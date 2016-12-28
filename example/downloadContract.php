<?php
/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/18
 * Time: 15:25
 */
include_once("../library/LxSecretManager.class.php");
include_once("../library/model/User.php");
if (isset($_POST["contractId"])) {

    $sdk_manager = R::getLxSDKManager();
    $result = $sdk_manager->downloadContract($_POST["contractId"]);
    if ($result['success']) {
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: " . strlen($result['body']));
        header("Content-Disposition: attachment; filename=" . $_POST["contractId"] . '.zip');
        echo $result['body'];
    } else {
        echo $result['body'];
    }

} else {
    echo json_decode(array("msg" => "请输入合同标题"));
}
