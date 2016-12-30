<?php

/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/12/30
 * Time: 14:10
 */
include_once("../library/LxSDKManager.class.php");
include_once("./resource/R.php");
header('Content-type: application/json;charset=utf-8');

$sdk_manager = R::getLxSDKManager();

echo $sdk_manager->getLastNotice();