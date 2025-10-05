<?php
//データベースのセットアップを行う
require_once __DIR__ . '/../core/php/cdb.php';
$db = cdb();
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(10) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(512),
    pwd VARCHAR(512)
);
CREATE TABLE IF NOT EXISTS employee (id INT(6) NOT NULL , `name` VARCHAR(20) NOT NULL, `yomigana` VARCHAR(20) NOT NULL , `birthday` DATE NOT NULL , `gender` INT(1) NOT NULL , `address` TEXT NOT NULL , `tel` TEXT NOT NULL , `mail` TEXT NOT NULL , `department` INT(10) NOT NULL, PRIMARY KEY(id));
CREATE TABLE IF NOT EXISTS `dep` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `parent` INT(10) NOT NULL , `is_parent` INT NOT NULL , PRIMARY KEY (`id`));
CREATE TABLE IF NOT EXISTS `shigyo` ( `id` INT(255) NOT NULL AUTO_INCREMENT , `date-time` TEXT NOT NULL , `haichi` INT(5) NOT NULL , PRIMARY KEY (`id`));
CREATE TABLE `gyoumu`.`recruit_dis_yoyaku` ( `id` INT(255) NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `birth` DATE NOT NULL , `interview_date` DATE NOT NULL , PRIMARY KEY (`id`));";
$db->query($sql);

$sql = "ALTER TABLE `users` ADD IF NOT EXISTS `permission` INT(2) NOT NULL DEFAULT '0' AFTER `permission`;
ALTER TABLE `employee` ADD IF NOT EXISTS `approval` INT(1) NOT NULL DEFAULT '0' AFTER `department`;
ALTER TABLE `shigyo` ADD IF NOT EXISTS `shift` TEXT NOT NULL AFTER `haichi`, ADD `j_haichi` INT NOT NULL AFTER `shift`;";
$db->query($sql);

//rsa鍵ペアの生成
require_once __DIR__ . '/server_key.php';
key_gen();
?>