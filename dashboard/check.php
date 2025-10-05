<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/core/php/cdb.php';
include_once $_SERVER["DOCUMENT_ROOT"] . '/core/php/encdec.php';
session_start();

// Cookie[session]の存在確認
if (!isset($_COOKIE['session'])) {
    header('Location: /index.php');
    exit;
}

if(!isset($_SESSION['cn']) || !isset($_COOKIE['SIDK']) || $_SESSION['cn'] !== $_COOKIE['SIDK']){
    // セッションが無い、またはセッションとCookieの値が異なる場合、再度認証
    check();
}

function check(){

    // Cookieデコード
    $session_name = decrypt($_COOKIE['session']);

    try {
        $pdo = cdb();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE name = ?');
        $stmt->execute([$session_name]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            // Cookie削除
            setcookie('session', '', time() - 3600, '/');
            header('Location: /index.php');
            exit;
        }

        //sessionのキャッシュを作成する
        $cn = md5(uniqid(mt_rand(), true).session_id().time());
        $_SESSION['cn'] = $cn;
        setcookie('SIDK', $cn, 0, '/');
    } catch (PDOException $e) {
        // エラー時はリダイレクト
        setcookie('session', '', time() - 3600, '/');
        header('Location: /index.php');
        exit;
    }
}
?>