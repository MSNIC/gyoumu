<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/php/cdb.php');

header('Content-Type: application/json');

// POSTデータ取得
$id         = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name       = isset($_POST['name']) ? trim($_POST['name']) : '';
$kana       = isset($_POST['kana']) ? trim($_POST['kana']) : '';
$address    = isset($_POST['address']) ? trim($_POST['address']) : '';
$tel        = isset($_POST['tel']) ? trim($_POST['tel']) : '';
$mail       = isset($_POST['mail']) ? trim($_POST['mail']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    $db = cdb();

    $sql = "UPDATE employee SET 
                name = :name,
                yomigana = :kana,
                address = :address,
                tel = :tel,
                mail = :mail,
                department = :department
            WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':kana', $kana);
    $stmt->bindValue(':address', $address);
    $stmt->bindValue(':tel', $tel);
    $stmt->bindValue(':mail', $mail);
    $stmt->bindValue(':department', $department);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['result' => "success"]);
    } else {
        echo json_encode(['result' => "error", 'message' => '変更に失敗しました。']);
    }
} catch (Exception $e) {
    echo json_encode(['result' => "error", 'message' => $e->getMessage()]);
}