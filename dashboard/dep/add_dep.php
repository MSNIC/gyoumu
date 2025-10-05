<?php
require_once __DIR__ . '/../../core/php/cdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depName   = isset($_POST['name']) ? trim($_POST['name']) : '';
    $parentDep = isset($_POST['parent']) ? (int)$_POST['parent'] : 0;
    $isParent  = isset($_POST['is_parent']) ? (int)$_POST['is_parent'] : 0;

    if ($depName !== '') {
        $pdo = cdb();
        $stmt = $pdo->prepare('INSERT INTO dep (name, parent, is_parent) VALUES (?, ?, ?)');
        $stmt->execute([$depName, $parentDep, $isParent]);
        echo(json_encode(["status" => "success"]));
    } else {
        echo(json_encode(["status" => "error", "message" => "部署名を入力してください。"]));
    }
}
?>