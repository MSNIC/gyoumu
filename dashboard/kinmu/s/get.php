<?php
header('Content-Type: application/json');

// 日時取得
$dt = isset($_GET['dt']) ? $_GET['dt'] : null;

// フォーマット
$response = [
    'dt' => $dt,
    'haichi' => 0,
    'j_haichi' => 0
];

// 日時が指定されていない場合はそのまま返す
if (!$dt) {
    echo json_encode($response);
    exit;
}

// DB接続
require_once(__DIR__ . '/../../../core/php/cdb.php');
$pdo = cdb();

// 配置人員数取得
$sql1 = "SELECT haichi FROM shigyo WHERE `date-time` = :dt LIMIT 1";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute([':dt' => $dt]);
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
if ($row1 && isset($row1['haichi'])) {
    $response['haichi'] = (int)$row1['haichi'];
}

// 実配置人員数取得
$sql2 = "SELECT j_haichi FROM shigyo WHERE `date-time` = :dt LIMIT 1";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([':dt' => $dt]);
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
if ($row2 && isset($row2['j_haichi'])) {
    $response['j_haichi'] = (int)$row2['j_haichi'];
}

echo json_encode($response);