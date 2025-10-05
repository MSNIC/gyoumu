<?php
// ログイン状態の確認
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";

// DB接続
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/php/cdb.php";
$pdo = cdb();

// 従業員ID取得
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 従業員情報の取得（1人のみ）
$sql = "SELECT id, name, mail, tel FROM employee WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$emp = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>従業員連絡 | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">従業員連絡</h1>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.close();">閉じる</button>
    </div>
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>従業員番号</th>
                <th>従業員名</th>
                <th>メールアドレス</th>
                <th>電話番号</th>
                <th>連絡方法</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($emp): ?>
            <tr>
                <td><?= htmlspecialchars($emp['id']) ?></td>
                <td><?= htmlspecialchars($emp['name']) ?></td>
                <td><?= htmlspecialchars($emp['mail']) ?></td>
                <td><?= htmlspecialchars($emp['tel']) ?></td>
                <td>
                    <?php if (!empty($emp['mail'])): ?>
                        <a href="mailto:<?= htmlspecialchars($emp['mail']) ?>" class="btn btn-primary btn-sm me-2">メール</a>
                    <?php endif; ?>
                    <?php if (!empty($emp['tel'])): ?>
                        <a href="tel:<?= htmlspecialchars($emp['tel']) ?>" class="btn btn-success btn-sm">電話</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">該当する従業員が見つかりません。</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>