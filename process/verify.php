<?php
session_start();
include_once('../core/php/cdb.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : $_GET['username'];
    $password = isset($_POST['password']) ? $_POST['password'] : $_GET['password'];

    if ($username === '' || $password === '') {
        echo json_encode(['result' => 'fail', 'reason' => 'input_missing']);
        exit;
    }

    $db = cdb();
    $stmt = $db->prepare('SELECT pwd, permission FROM users WHERE name = ?');
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && hash('sha3-512', $password) === $row['pwd']) {
        echo json_encode(['result' => 'success']);
        exit;
    } else {
        echo json_encode(['result' => 'fail', 'reason' => 'invalid_username_or_credentials']);
        exit;
    }
}
?>
