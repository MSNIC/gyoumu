<?php
header('Content-Type: application/json');
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/php/cdb.php');

try {
    // POSTデータ取得
    $employee_no    = isset($_POST['employee_no'])    ? $_POST['employee_no']    : null;
    $employee_name  = isset($_POST['employee_name'])  ? $_POST['employee_name']  : null;
    $employee_kana  = isset($_POST['employee_kana'])  ? $_POST['employee_kana']  : null;
    $birthday       = isset($_POST['birthday'])       ? $_POST['birthday']       : null;
    $gender         = isset($_POST['gender'])         ? $_POST['gender']         : null;
    $address        = isset($_POST['address'])        ? $_POST['address']        : null;
    $tel            = isset($_POST['tel'])            ? $_POST['tel']            : null;
    $department     = isset($_POST['department'])     ? $_POST['department']     : null;

    // 必須項目チェック
    if (
        empty($employee_name) || empty($employee_kana) || empty($birthday) ||
        !isset($gender) || empty($address) || empty($tel) || !isset($department)
    ) {
        echo json_encode(['status' => 'error', 'reason' => '必須項目が不足しています。']);
        exit;
    }

    // DB接続
    $pdo = cdb();

    // SQL作成
    $sql = "INSERT INTO employee (id, name, yomigana, birthday, gender, address, tel, mail, department)
            VALUES (:id, :name, :yomigana, :birthday, :gender, :address, :tel, :mail, :department)";
    $stmt = $pdo->prepare($sql);

    // mailは空文字で登録
    $mail = '';

    // バインド
    $stmt->bindParam(':id', $employee_no);
    $stmt->bindParam(':name', $employee_name);
    $stmt->bindParam(':yomigana', $employee_kana);
    $stmt->bindParam(':birthday', $birthday);
    $stmt->bindParam(':gender', $gender, PDO::PARAM_INT);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':department', $department, PDO::PARAM_INT);

    // 実行
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        $error = $stmt->errorInfo();
        // エラー内容を判定して分かりやすいメッセージに変換
        if (strpos($error[2], 'Duplicate entry') !== false) {
            $msg = '既に同じデータが登録されています。';
        } elseif (strpos($error[2], 'cannot be null') !== false) {
            $msg = '必須項目が入力されていません。';
        } else {
            $msg = '登録に失敗しました。入力内容をご確認ください。';
        }
        echo json_encode(['status' => 'error', 'reason' => $msg]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'reason' => 'システムエラーが発生しました。']);
}