<?php
include_once('../core/php/cdb.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username !== '' && $password !== '') {
        $db = cdb();
        $stmt = $db->prepare('SELECT pwd FROM users WHERE name = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && hash('sha3-512', $password) === $row['pwd']) {
            include_once('../core/php/encdec.php');
            $encrypted_username = encrypt($username); // encrypt()はencdec.php内の関数
            setcookie('session', $encrypted_username, 0, '/', '', false, true);
            // ログイン成功時の処理（例: リダイレクト）
            header('Location: /dashboard/');
            exit();
        } else {
            // ログイン失敗時の処理
            // POSTで/index.phpにfail=yesを送信してリダイレクトする
            echo '<form id="failForm" action="/index.php" method="post" style="display:none;">';
            echo '<input type="hidden" name="fail" value="yes">';
            echo '</form>';
            echo '<script>document.getElementById("failForm").submit();</script>';
            exit;
        }
    } else {
        // 入力値不足時の処理
            echo '<form id="failForm" action="/index.php" method="post" style="display:none;">';
            echo '<input type="hidden" name="fail" value="low">';
            echo '</form>';
            echo '<script>document.getElementById("failForm").submit();</script>';
            exit;
    }
}
?>