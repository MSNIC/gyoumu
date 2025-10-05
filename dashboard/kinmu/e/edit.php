<?php
// ログインチェック
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";

// DB接続
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/php/cdb.php";
$db = cdb();

// 従業員ID取得
$employee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($employee_id <= 0) {
    die("不正なアクセスです。");
}

// 従業員情報取得
$stmt = $db->prepare("SELECT * FROM employee WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
    die("従業員が見つかりません。");
}

// データベースから部署一覧を取得
        $departments = [];
        try {
            $stmt = $db->query("SELECT * FROM dep ORDER BY id");
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $departments = [];
        }
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>従業員編集 | 業務管理</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>従業員編集</h2>
    <form id="editForm">
        <input type="hidden" name="id" value="<?= htmlspecialchars($employee['id']) ?>">
        <div class="mb-3">
            <label class="form-label">名前</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($employee['name']) ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">読み仮名</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-type"></i></span>
                <input type="text" class="form-control" name="kana" value="<?= htmlspecialchars($employee['yomigana']) ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">住所</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($employee['address']) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">電話番号</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="tel" class="form-control" name="tel" value="<?= htmlspecialchars($employee['tel']) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">メールアドレス</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="mail" value="<?= htmlspecialchars($employee['mail']) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="department" class="form-label">所属部署</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-building"></i></span>
                <select class="form-select" id="department" name="department" required>
                    <option value="">選択してください</option>
                    <?php
                    // 部署を親部署ごとにグループ化
                    $parents = [];
                    $children = [];
                    foreach ($departments as $dept) {
                        if ($dept['is_parent']) {
                            $parents[$dept['id']] = $dept['name'];
                        } else {
                            $children[$dept['parent']][] = $dept;
                        }
                    }
                    foreach ($parents as $parent_id => $parent_name):
                    ?>
                        <optgroup label="<?= htmlspecialchars($parent_name) ?>">
                            <?php if (!empty($children[$parent_id])): ?>
                                <?php foreach ($children[$parent_id] as $child): ?>
                                    <option value="<?= htmlspecialchars($child['id']) ?>"<?= $employee['department'] == $child['id'] ? ' selected' : '' ?>>
                                        <?= htmlspecialchars($child['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
        <button type="button" class="btn btn-secondary" onclick="window.close();">閉じる</button>
    </form>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$('#editForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'edit_ajax.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json'
    }).done(function(res) {
        if (res.result === 'success') {
            if (window.opener && typeof window.opener.location.reload === 'function') {
                window.opener.location.reload();
            }
            window.close();
        } else {
            alert(res.message || '更新に失敗しました。');
        }
    }).fail(function() {
        alert('通信エラーが発生しました。');
    });
});
</script>
</body>
</html>