<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>従業員追加 | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">従業員追加</h1>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.close();">閉じる</button>
    </div>
    <form id="employeeForm" method="post" action="insert.php">
        <div class="mb-3">
            <label for="employee_no" class="form-label">社員番号</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="employee_no" name="employee_no" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="employee_name" class="form-label">社員名</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="employee_name" name="employee_name" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="employee_kana" class="form-label">社員名の読み方</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-type"></i></span>
                <input type="text" class="form-control" id="employee_kana" name="employee_kana" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="birthday" class="form-label">生年月日</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                <input type="date" class="form-control" id="birthday" name="birthday" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">性別</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                <select class="form-select" name="gender" required>
                    <option value="">選択してください</option>
                    <option value="male">男</option>
                    <option value="female">女</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="tel" class="form-label">電話番号</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="tel" class="form-control" id="tel" name="tel" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス（あれば）</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email">
            </div>
        </div>
        <div class="mb-3">
            <label for="department" class="form-label">所属部署</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-building"></i></span>
                <input type="text" class="form-control" id="department" name="department" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">追加</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(function() {
        $('#employeeForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json'
            }).done(function(response) {
                if (response.status === 'success') {
                    if (window.opener) {
                        window.opener.location.reload();
                    }
                    window.close();
                } else {
                    alert('追加に失敗しました。');
                }
            }).fail(function() {
                alert('通信エラーが発生しました。');
            });
        });
    });
    </script>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>