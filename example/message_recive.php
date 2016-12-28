<?php
/**
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/18
 * Time: 15:25
 */
include_once("../library/LxSecretManager.class.php");
include_once("../library/model/User.php");
header('Content-type: application/json;charset=utf-8');

$secret = $_POST['secret'];

if (!$secret) {
    echo json_encode(array('response' => false, 'msg' => "notice is empty"));
    exit;
}
// 解密接收到的请求
$appId = '2016062017020200007';
$sdk_manager = new LxSDKManager('2016062017020200007', "resource/testYhtSK.pem", "resource/test_pk.pem");
$content = $sdk_manager->decrypt($secret);

// 初始话数据库，记录信息
$db = new SQLite3('mysqlitedb.db');
if (!is_table_exists($db, 'tab_msg')) {
    $db->exec('create table tab_msg (id integer primary key autoincrement, msg string, time long);');
}
$stmt = $db->prepare("insert into tab_msg (msg,time) values( ? ,?)");
$stmt->bindParam(1, $content);
$stmt->bindParam(2, time());
$stmt->execute();
$db->close();

// 返回签名信息
echo $sdk_manager->sign_data($content);


function is_table_exists($db, $table_name)
{
    $results = $db->query('SELECT COUNT(*) as n FROM sqlite_master where type="table" and name="' . $table_name . '"');
    if ($results->numColumns() > 0) {
        return true;
    }
    return false;
}

