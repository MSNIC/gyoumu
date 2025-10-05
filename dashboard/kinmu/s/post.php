<?php
require_once(__DIR__ . '/../../../core/php/cdb.php');
header('Content-Type: application/json');

if (!isset($_POST['datetime']) || !isset($_POST['haichi'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}
$result = updateHaichi($_POST['datetime'], $_POST['haichi']);

if ($result) {
    echo json_encode(['result' => 'success']);
} else {
    echo json_encode(['result' => 'fail', 'error' => 'DB update/insert failed or invalid input']);
}
exit;
function updateHaichi($datetime, $haichi) {
    $db = cdb();
    $table = 'shigyo';

    // 入力値のバリデーション
    if (!is_string($datetime) || !is_numeric($haichi)) {
        return false;
    }

    // date-timeが存在するか確認
    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE `date-time` = :datetime");
    $stmt->execute([':datetime' => $datetime]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        // 存在する場合はupdate
        $stmt = $db->prepare("UPDATE $table SET haichi = :haichi WHERE `date-time` = :datetime");
        return $stmt->execute([':haichi' => $haichi, ':datetime' => $datetime]);
    } else {
        // 存在しない場合はinsert
        $stmt = $db->prepare("INSERT INTO $table (`date-time`, haichi, shift, j_haichi) VALUES (:datetime, :haichi, :shift, :j_haichi)");
        return $stmt->execute([
            ':datetime' => $datetime,
            ':haichi' => $haichi,
            ':shift' => '{}',
            ':j_haichi' => 0
        ]);
    }
}

?>