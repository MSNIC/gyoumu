<?php
require_once 'check.php';
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ダッシュボード | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">ダッシュボード</h1>
    <div class="row g-4">
        <!-- 通知カード -->
        <div class="col-md-6">
            <div class="card shadow-sm md-6">
                <div class="card-header bg-primary text-white">
                    通知
                </div>
                <div class="card-body">
                    <p class="card-text">現在通知はありません。</p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">セッション管理</div>
                <div class="card-body">
                    <p>セッションログアウトを行います。</p>
                    <a href="#" class="btn btn-danger" onclick="if(confirm('ログアウトしますか？')){ window.location.href='/logout.php'; } return false;">ログアウト</a>
                </div>
            </div>
        </div>
        <!-- 勤務カード -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    勤務
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="./kinmu/emp.php" class="btn btn-outline-primary" onclick="window.open(this.href, 'emp', 'width=900,height=900'); return false;">従業員管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/shigyo.php', 'shigyo', 'width=900,height=900'); return false;">仕業管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/waritsuke.php', 'waritsuke', 'width=900,height=900'); return false;">勤務割付</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/jikangai.php', 'jikangai', 'width=900,height=900'); return false;">時間外報告</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/kyuka.php', 'kyuka', 'width=900,height=900'); return false;">休暇管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./dep/index.php', 'department', 'width=900,height=900'); return false;">部署管理</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>