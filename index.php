<!--
MSNIC業務管理システム v1.0.0
Copyright (c) 2025 MSNIC. All rights reserved.
-->
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>業務管理</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm" style="min-width: 350px;">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">ログイン | 業務管理</h4>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fail']) && $_POST['fail'] === 'yes') {
                    echo '<div class="alert alert-danger" role="alert">ユーザー名またはパスワードが違います</div>';
                }else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fail']) && $_POST['fail'] === 'low') {
                    echo '<div class="alert alert-warning" role="alert">入力値が不足しています</div>';
                }
                ?>
                <form action="process/login.php" method="post">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="ログインID" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" placeholder="パスワード" name="password" required>
                        </div>
                    </div>
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary">ログイン</button>
                    </div>
                </form>
                <div class="text-center">
                    <a href="gpthry.php" class="small">ID・パスワードを忘れた</a>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>