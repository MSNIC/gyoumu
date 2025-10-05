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
            <?php if($_SESSION['permission'] >= 5): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-black">経営管理</div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./management/financials.php', 'financials', 'height=900'); return false;">財務管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./management/performance.php', 'performance', 'height=900'); return false;">業績管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./management/strategy.php', 'strategy', 'height=900'); return false;">戦略管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./management/risk.php', 'risk', 'height=900'); return false;">リスク管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./management/compliance.php', 'compliance', 'height=900'); return false;">コンプライアンス管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./dep/index.php', 'department', 'height=900'); return false;">部署管理</a>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-black">システム管理</div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./system/user.php', 'user', 'height=900'); return false;">ユーザー管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./system/backup.php', 'backup', 'height=900'); return false;">データバックアップ</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./system/logs.php', 'logs', 'height=900'); return false;">システムログ</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./system/settings.php', 'settings', 'height=900'); return false;">システム設定</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <!-- 勤務カード -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    勤務
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="./kinmu/emp.php" class="btn btn-outline-primary" onclick="window.open(this.href, 'emp', 'height=900'); return false;">従業員管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/shigyo.php', 'shigyo', 'height=900'); return false;">仕業管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/waritsuke.php', 'waritsuke', 'height=900'); return false;">勤務割付</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/jikangai.php', 'jikangai', 'height=900'); return false;">時間外報告</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./kinmu/kyuka.php', 'kyuka', 'height=900'); return false;">休暇管理</a>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">営業管理</div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./sales/customer.php', 'customer', 'height=900'); return false;">顧客管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./sales/sales.php', 'sales', 'height=900'); return false;">売上管理</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./sales/report.php', 'report', 'height=900'); return false;">営業報告</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./sales/forecast.php', 'forecast', 'height=900'); return false;">売上予測</a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.open('./sales/budget.php', 'budget', 'height=900'); return false;">予算管理</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>