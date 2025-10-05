<?php
//データベースのセットアップを行う
require_once __DIR__ . '/../core/php/cdb.php';
$db = cdb();
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(10) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(512),
    pwd VARCHAR(512)
)";
$db->query($sql);

//rsa鍵ペアの生成
require_once __DIR__ . '/server_key.php';
key_gen();
?>