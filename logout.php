<?php
// セッションを開始
session_start();

// 全てのクッキーを削除
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 3600, '/');
        setcookie($name, '', time() - 3600, '/', '', true, true);
    }
}

//確実に破壊する
setcookie('session', '', time() - 3600, '/');
setcookie('SIDK', '', time() - 3600, '/');

// セッション変数を全て解除
$_SESSION = [];

// セッションを破壊
if (session_id() !== '' || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();

// ログアウト後のリダイレクト（例: ログインページへ）
header('Location: /index.php');
exit;
?>