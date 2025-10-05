<?php
//データベースのセットアップを行う
require_once __DIR__ . '/../core/php/cdb.php';
$db = cdb();
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(10) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(512),
    pwd VARCHAR(512)
);
CREATE TABLE IF NOT EXISTS employee (
    id INT(6) NOT NULL , `name` VARCHAR(20) NOT NULL , `yomigana` VARCHAR(20) NOT NULL , `birthday` DATE NOT NULL , `gender` INT(1) NOT NULL , `address` TEXT NOT NULL , `tel` TEXT NOT NULL , `mail` TEXT NOT NULL , `department` INT(10) NOT NULL );
CREATE TABLE IF NOT EXISTS `dep` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `parent` INT(10) NOT NULL , `is_parent` INT NOT NULL , PRIMARY KEY (`id`));";
$db->query($sql);

//rsa鍵ペアの生成
require_once __DIR__ . '/server_key.php';
key_gen();
?>