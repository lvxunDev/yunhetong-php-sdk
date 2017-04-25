<?php
/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/18
 * Time: 15:25
 */
include_once("./resource/R.php");

if (isset($_GET["contractId"])) {
    $sdk_manager = R::getLxSDKManager();
    $result = $sdk_manager->downloadContract($_GET["contractId"]);
    if ($result['success']) {
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: " . strlen($result['body']));
        header("Content-Disposition: attachment; filename=" . $_GET["contractId"] . '.zip');
        echo $result['body'];
    } else {
        header('Content-type: application/json;charset=utf-8');
        echo $result['body'];
    }

} else {
    header('Content-type: application/json;charset=utf-8');
    echo json_encode(array("msg" => "请输入合同编号"));
}
