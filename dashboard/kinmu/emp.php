<?php
// check.phpでログイン状態を確認
require_once __DIR__ . '/../check.php';

require_once $_SERVER["DOCUMENT_ROOT"]. '/core/php/cdb.php';

try {
    $pdo = cdb();
    $stmt = $pdo->query('SELECT id, name, yomi FROM employee ORDER BY id');
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $employees = [];
    // 本番ではエラー内容を出力しないようにする
}

// ページネーション設定
$perPageOptions = [10, 20, 30, 50, 100];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total = count($employees);
$totalPages = ceil($total / $perPage);
$start = ($page - 1) * $perPage;
$displayEmployees = array_slice($employees, $start, $perPage);

function buildQuery($params) {
    return http_build_query(array_merge($_GET, $params));
}
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>従業員管理 | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h1 class="mb-4">従業員管理</h1>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.close();">閉じる</button>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="get" class="row g-2 align-items-center mb-0">
            <label for="per_page" class="col-auto col-form-label">1ページの表示人数:</label>
            <div class="col-auto">
                <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($perPageOptions as $option): ?>
                        <option value="<?= $option ?>"<?= $perPage === $option ? ' selected' : '' ?>><?= $option ?>人</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        <button type="button" class="btn btn-success" onclick="window.open('e/add.php', 'addEmployee', 'width=400,resizable=yes,scrollbars=yes,location=no,menubar=no,toolbar=no,status=no'); return false;">
            従業員の追加
        </button>
    </div>
    <div class="table-responsive">
        <?php if (empty($displayEmployees)): ?>
            <div class="alert alert-warning text-center mb-0">
            従業員データが存在しません。
            </div>
        <?php else: ?>
            <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                <th>従業員番号</th>
                <th>氏名</th>
                <th>読み方</th>
                <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($displayEmployees as $emp): ?>
                <tr>
                <td><?= htmlspecialchars($emp['id']) ?></td>
                <td><?= htmlspecialchars($emp['name']) ?></td>
                <td><?= htmlspecialchars($emp['yomi']) ?></td>
                <td>
                    <a href="view.php?id=<?= $emp['id'] ?>" class="btn btn-primary btn-sm"
                        onclick="window.open(this.href, 'viewEmployee<?= $emp['id'] ?>', 'width=600,height=600,resizable=yes,scrollbars=yes'); return false;">勤務照会</a>
                    <a href="edit.php?id=<?= $emp['id'] ?>" class="btn btn-warning btn-sm"
                        onclick="window.open(this.href, 'editEmployee<?= $emp['id'] ?>', 'width=500,height=600,resizable=yes,scrollbars=yes'); return false;">編集</a>
                    <a href="contact.php?id=<?= $emp['id'] ?>" class="btn btn-info btn-sm"
                        onclick="window.open(this.href, 'contactEmployee<?= $emp['id'] ?>', 'width=500,height=500,resizable=yes,scrollbars=yes'); return false;">連絡</a>
                    <a href="rest.php?id=<?= $emp['id'] ?>" class="btn btn-secondary btn-sm"
                        onclick="window.open(this.href, 'restEmployee<?= $emp['id'] ?>', 'width=400,height=400,resizable=yes,scrollbars=yes'); return false;">休務</a>
                    <a href="accident.php?id=<?= $emp['id'] ?>" class="btn btn-danger btn-sm"
                        onclick="window.open(this.href, 'accidentEmployee<?= $emp['id'] ?>', 'width=500,height=500,resizable=yes,scrollbars=yes'); return false;">労災</a>
                    <a href="delete.php?id=<?= $emp['id'] ?>" class="btn btn-outline-danger btn-sm"
                        onclick="if(confirm('本当に削除しますか？')){window.open(this.href, 'deleteEmployee<?= $emp['id'] ?>', 'width=400,height=300,resizable=yes,scrollbars=yes');} return false;">削除</a>
                </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        <?php endif; ?>
    </div>
    <!-- ページネーション -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                <a class="page-link" href="?<?= buildQuery(['page' => $page - 1]) ?>">前へ</a>
            </li>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item<?= $p === $page ? ' active' : '' ?>">
                    <a class="page-link" href="?<?= buildQuery(['page' => $p]) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                <a class="page-link" href="?<?= buildQuery(['page' => $page + 1]) ?>">次へ</a>
            </li>
        </ul>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>