<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/.config/default.php');
function cdb() {
    $dsn = 'mysql:host=' . DATABASE_ADDRESS . ';dbname=' . DATABASE_NAME . ';charset=' . DATABASE_CHARSET;
    try {
        $pdo = new PDO($dsn, DATABASE_USER, DATABASE_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo 'データベース接続失敗: ' . $e->getMessage();
        exit;
    }
}
?>