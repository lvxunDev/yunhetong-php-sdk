<?php
/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/18
 * Time: 15:25
 */

include_once("./resource/R.php");
//header('Content-type: application/json;charset=utf-8');


$user = null;
if (isset($_GET['user']) && $_GET['user'] == 'B') {
    $user = R::getUserB();
} else {
    $user = R::getUserA();
}

$sdk_manager = R::getLxSDKManager();
echo $sdk_manager->sync_get_token($user);

exit;
