<?php
// ログインチェック
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";

// DB接続設定（例: PDO使用、適宜修正）
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/php/cdb.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/php/encdec.php";
include_once($_SERVER["DOCUMENT_ROOT"] . '/core/php/phpqrcode/qrlib.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $birth = trim($_POST['birth'] ?? '');
    $interview = trim($_POST['interview'] ?? '');

    if ($name && $birth && $interview) {
        try {
            $pdo = cdb();
            // 予約情報をDBに保存
            $stmt = $pdo->prepare("INSERT INTO recruit_dis_yoyaku (name, birth, interview_date) VALUES (?, ?, ?)");
            $stmt->execute([$name, $birth, $interview]);

            // SHA3-512ハッシュ生成
            $hash = hash('sha3-512', $name . $birth . $interview);

            $encryptedBase64 = base64_encode($hash);
            // QRコード生成
            $qrPath = 'qrcode_'.time().'.png';
            QRcode::png($encryptedBase64, $qrPath, QR_ECLEVEL_L, 10);
        } catch (Exception $e) {
            $error = '登録に失敗しました: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = '全ての項目を入力してください。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>新規採用 | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">新規採用ポータル誘導ページ</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($qrPath)): ?>
        <div class="alert alert-success">予約が完了しました。下記QRコードを保存してください。</div>
        <div class="mb-4">
            <img src="<?= $qrPath ?>" alt="QRコード">
        </div>
        <a href="" class="btn btn-primary">もう一度予約する</a>
    <?php else: ?>
        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="name" class="form-label">氏名</label>
                <input type="text" class="form-control" id="name" name="name" required maxlength="64">
            </div>
            <div class="mb-3">
                <label for="birth" class="form-label">生年月日</label>
                <input type="date" class="form-control" id="birth" name="birth" required>
            </div>
            <div class="mb-3">
                <label for="interview" class="form-label">面接予定日</label>
                <input type="date" class="form-control" id="interview" name="interview" required>
            </div>
            <button type="submit" class="btn btn-success">予約する</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>