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

$db = new SQLite3('mysqlitedb.db');

if (!is_table_exists($db, 'tab_msg')) {
    $db->exec('create table tab_msg (id integer primary key autoincrement, msg string, time long)');
}


// $db->exec('insert into  tab_msg (msg,time) values("' . '123' . '","' . (time() * 1000) . '")');
$results = $db->query('SELECT * FROM tab_msg ORDER BY time DESC');

$data = array();
while ($row = $results->fetchArray()) {
    // var_dump($row);
    $temp = array();
    $temp["id"] = $row["id"];
    $temp["msg"] = $row["msg"];
    $temp["time"] = $row["time"];
    $data[] = $temp;
}
echo json_encode($data);


function is_table_exists($db, $table_name)
{
    $results = $db->query('SELECT COUNT(*) as n FROM sqlite_master where type="table" and name="' . $table_name . '"');
    if ($results->numColumns() > 0) {
        return true;
    }
    return false;
}

