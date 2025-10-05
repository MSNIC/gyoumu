<?php
// check.phpでログイン状態を確認
require_once __DIR__ . '/../check.php';

require_once $_SERVER["DOCUMENT_ROOT"]. '/core/php/cdb.php';

try {
    $pdo = cdb();
    // 従業員データ取得
    $stmt = $pdo->query('SELECT * FROM employee ORDER BY id');
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 面接予定者データ取得
    $stmtRecruit = $pdo->query('SELECT * FROM recruit_dis_yoyaku ORDER BY id');
    $recruits = $stmtRecruit->fetchAll(PDO::FETCH_ASSOC);

    // 部署情報を取得し、IDをキーにマッピング
    $depMap = [];
    try {
        $stmtDep = $pdo->query('SELECT id, name, parent FROM dep');
        foreach ($stmtDep->fetchAll(PDO::FETCH_ASSOC) as $dep) {
            $depMap[$dep['id']] = $dep;
        }
    } catch (Exception $e) {
        $depMap = [];
    }
} catch (Exception $e) {
    $employees = [];
    $recruits = [];
    $depMap = [];
    // 本番ではエラー内容を出力しないようにする
}

// ページネーション設定（従業員＋面接予定者の合計でページング）
$perPageOptions = [10, 20, 30, 50, 100];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// リストを結合（面接予定者→従業員の順で表示）
$allList = [];
foreach ($recruits as $r) {
    $r['__type'] = 'recruit';
    $allList[] = $r;
}
foreach ($employees as $e) {
    $e['__type'] = 'employee';
    $allList[] = $e;
}

$total = count($allList);
$totalPages = ceil($total / $perPage);
$start = ($page - 1) * $perPage;
$displayList = array_slice($allList, $start, $perPage);

function buildQuery($params) {
    return http_build_query(array_merge($_GET, $params));
}

// 部署IDから階層付き部署名を取得する関数
function getDepartmentFullName($depId, $depMap) {
    $names = [];
    while ($depId && isset($depMap[$depId])) {
        array_unshift($names, $depMap[$depId]['name']);
        $depId = $depMap[$depId]['parent'];
    }
    return implode('/', $names);
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
        <?php
        // ユーザー権限を取得（例: $_SESSION['auth'] に格納されていると仮定）
        $auth = isset($_SESSION['permission']) ? (int)$_SESSION['permission'] : 0;
        ?>
        <div class="d-flex gap-2">
            <?php if ($auth >= 4): ?>
            <button type="button" class="btn btn-primary" onclick="window.open('/interview/index.php', 'newHire', 'width=400,resizable=yes,scrollbars=yes,location=no,menubar=no,toolbar=no,status=no'); return false;">
                面接を開始する
            </button>
            <button type="button" class="btn btn-primary" onclick="window.open('e/new_recruit.php', 'newHire', 'width=400,resizable=yes,scrollbars=yes,location=no,menubar=no,toolbar=no,status=no'); return false;">
                新規採用
            </button>
            <?php endif; ?>
            <?php if ($auth >= 6): ?>
            <button type="button" class="btn btn-success" onclick="window.open('e/add.php', 'addEmployee', 'width=400,resizable=yes,scrollbars=yes,location=no,menubar=no,toolbar=no,status=no'); return false;">
                従業員の追加
            </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-responsive">
        <?php if (empty($displayList)): ?>
            <div class="alert alert-warning text-center mb-0">
            データが存在しません。
            </div>
        <?php else: ?>
            <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                <th>従業員番号</th>
                <th>氏名</th>
                <th>読み方</th>
                <th>所属部署</th>
                <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($displayList as $item): ?>
                <tr>
                <?php if ($item['__type'] === 'employee'): ?>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['yomigana']) ?></td>
                    <td><?= htmlspecialchars(getDepartmentFullName($item['department'] ?? null, $depMap)) ?></td>
                    <td>
                        <a href="e/view.php?id=<?= $item['id'] ?>" class="btn btn-primary btn-sm"
                            onclick="window.open(this.href, 'viewEmployee<?= $item['id'] ?>', 'width=600,height=600,resizable=yes,scrollbars=yes'); return false;">勤務照会</a>
                        <a href="e/edit.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm"
                            onclick="window.open(this.href, 'editEmployee<?= $item['id'] ?>', 'width=500,height=600,resizable=yes,scrollbars=yes'); return false;">編集</a>
                        <a href="e/contact.php?id=<?= $item['id'] ?>" class="btn btn-info btn-sm"
                            onclick="window.open(this.href, 'contactEmployee<?= $item['id'] ?>', 'width=500,height=500,resizable=yes,scrollbars=yes'); return false;">連絡</a>
                        <a href="e/rest.php?id=<?= $item['id'] ?>" class="btn btn-secondary btn-sm"
                            onclick="window.open(this.href, 'restEmployee<?= $item['id'] ?>', 'width=400,height=400,resizable=yes,scrollbars=yes'); return false;">休務</a>
                        <a href="e/accident.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="window.open(this.href, 'accidentEmployee<?= $item['id'] ?>', 'width=500,height=500,resizable=yes,scrollbars=yes'); return false;">労災</a>
                        <a href="e/delete.php?id=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm"
                            onclick="if(confirm('本当に削除しますか？')){window.open(this.href, 'deleteEmployee<?= $item['id'] ?>', 'width=400,height=300,resizable=yes,scrollbars=yes');} return false;">削除</a>
                    </td>
                <?php else: // recruit_dis_yoyaku ?>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['interview_date'] ?? '') ?></td>
                    <td>面接予定者</td>
                    <td>
                        <a href="e/QR/qrcode_<?= hash('sha3-512',$item['name'].$item['birth'].$item['interview_date']) ?>.png" class="btn btn-success btn-sm"
                            onclick="window.open(this.href, 'qrRecruit', 'width=400,height=400,resizable=yes,scrollbars=yes'); return false;">QR表示</a>
                    </td>
                <?php endif; ?>
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