<?php
// ログイン状態の確認
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";

// DB接続
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/php/cdb.php";
$db = cdb();

$interview_ok = false;
$error = '';
$recruit_data = null;

// GETでコードが来ているか確認
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // recruit_dis_yoyakuテーブルから全件取得
    $stmt = $db->prepare("SELECT name, birth, interview_date FROM recruit_dis_yoyaku");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $concat = $row['name'] . $row['birth'] . $row['interview_date'];
        $hash = hash('sha3-512', $concat);
        if ($code === $hash) {
            $interview_ok = true;
            $recruit_data = $row;
            break;
        }
    }
    if (!$interview_ok) {
        $error = '認証に失敗しました。QRコードを再度読み込んでください。';
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        #qr-reader { width: 100%; max-width: 400px; margin: 0 auto; }
    </style>
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">新規採用面接ページ</h1>
    <?php if ($interview_ok): ?>
        <div class="alert alert-success">面接を開始します。</div>
        <div id="interview-steps">
            <!-- Step 1: 名前・生年月日確認 -->
            <div class="card step" id="step-1">
                <div class="card-body">
                    <h5 class="card-title">氏名・生年月日確認</h5>
                    <p>氏名: <strong><?= htmlspecialchars($recruit_data['name']) ?></strong></p>
                    <p>生年月日: <strong><?= htmlspecialchars($recruit_data['birth']) ?></strong></p>
                    <p>上記に誤りがないことを確認してください。</p>
                    <button class="btn btn-primary next-btn">次へ</button>
                </div>
            </div>
            <!-- Step 2: 名前の読み仮名入力 -->
            <div class="card step d-none" id="step-2">
                <div class="card-body">
                    <h5 class="card-title">名前の読み仮名</h5>
                    <div class="mb-3">
                        <label for="kana" class="form-label">名前の読み仮名（カタカナ）</label>
                        <input type="text" class="form-control" id="kana" name="kana" required>
                    </div>
                    <button class="btn btn-primary next-btn">次へ</button>
                </div>
            </div>
            <!-- Step 3: 生年月日確認 -->
            <div class="card step d-none" id="step-3">
                <div class="card-body">
                    <h5 class="card-title">生年月日確認</h5>
                    <p>あなたの生年月日は <strong><?= htmlspecialchars($recruit_data['birth']) ?></strong> で間違いありませんか？</p>
                    <button class="btn btn-primary next-btn">はい、間違いありません</button>
                </div>
            </div>
            <!-- Step 4: 性別入力 -->
            <div class="card step d-none" id="step-4">
                <div class="card-body">
                    <h5 class="card-title">性別</h5>
                    <div class="mb-3">
                        <label class="form-label">性別を選択してください</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">選択してください</option>
                            <option value="male">男性</option>
                            <option value="female">女性</option>
                            <option value="other">その他</option>
                        </select>
                    </div>
                    <button class="btn btn-primary next-btn">次へ</button>
                </div>
            </div>
            <!-- Step 5: 住所入力 -->
            <div class="card step d-none" id="step-5">
                <div class="card-body">
                    <h5 class="card-title">住所</h5>
                    <div class="mb-3">
                        <label for="address" class="form-label">住所</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <button class="btn btn-primary next-btn">次へ</button>
                </div>
            </div>
            <!-- Step 6: 電話番号・メールアドレス入力 -->
            <div class="card step d-none" id="step-6">
                <div class="card-body">
                    <h5 class="card-title">連絡先</h5>
                    <div class="mb-3">
                        <label for="tel" class="form-label">電話番号（必須）</label>
                        <input type="tel" class="form-control" id="tel" name="tel" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">メールアドレス（任意）</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <button class="btn btn-primary next-btn">次へ</button>
                </div>
            </div>
            <!-- Step 7: 面接担当者認証（入力確認） -->
            <div class="card step d-none" id="step-7">
                <div class="card-body">
                    <h5 class="card-title">面接担当者認証</h5>
                    <p>タブレットを面接担当者に渡してください。</p>
                    <!-- 入力内容の確認表示 -->
                    <div class="mb-3">
                        <h6>入力内容確認</h6>
                        <ul id="input-summary" class="list-group mb-3">
                            <li class="list-group-item">氏名: <span id="summary-name"><?= htmlspecialchars($recruit_data['name']) ?></span></li>
                            <li class="list-group-item">生年月日: <span id="summary-birth"><?= htmlspecialchars($recruit_data['birth']) ?></span></li>
                            <li class="list-group-item">名前の読み仮名: <span id="summary-kana"></span></li>
                            <li class="list-group-item">性別: <span id="summary-gender"></span></li>
                            <li class="list-group-item">住所: <span id="summary-address"></span></li>
                            <li class="list-group-item">電話番号: <span id="summary-tel"></span></li>
                            <li class="list-group-item">メールアドレス: <span id="summary-email"></span></li>
                        </ul>
                    </div>
                    <form id="auth-form-1">
                        <div class="mb-3">
                            <label for="staff_id_1" class="form-label">担当者ID</label>
                            <input type="text" class="form-control" id="staff_id_1" name="staff_id_1" required>
                        </div>
                        <div class="mb-3">
                            <label for="staff_pw_1" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="staff_pw_1" name="staff_pw_1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">認証して次へ</button>
                    </form>
                </div>
            </div>
            <script src="/core/js/jq.js"></script>
            <script>
                // 入力内容の確認表示
                function updateInputSummary() {
                    document.getElementById('summary-kana').textContent = document.getElementById('kana').value;
                    const genderMap = { male: '男性', female: '女性', other: 'その他' };
                    document.getElementById('summary-gender').textContent = genderMap[document.getElementById('gender').value] || '';
                    document.getElementById('summary-address').textContent = document.getElementById('address').value;
                    document.getElementById('summary-tel').textContent = document.getElementById('tel').value;
                    document.getElementById('summary-email').textContent = document.getElementById('email').value;
                }
                // ステップ7表示時に内容を更新
                const observer = new MutationObserver(function() {
                    const step7 = document.getElementById('step-7');
                    if (step7 && !step7.classList.contains('d-none')) {
                        updateInputSummary();
                    }
                });
                observer.observe(document.getElementById('interview-steps'), { childList: false, subtree: true, attributes: true, attributeFilter: ['class'] });
            </script>
            <!-- Step 8: 就業規則同意 -->
            <div class="card step d-none" id="step-8">
                <div class="card-body">
                    <h5 class="card-title">就業規則同意</h5>
                    <p>就業規則を熟読し、同意してください。</p>
                    <div class="mb-3">
                        <textarea class="form-control" rows="6" readonly>
        【就業規則の例】
        ここに就業規則の内容を記載してください。
                        </textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agree" required>
                        <label class="form-check-label" for="agree">
                            就業規則を熟読し、同意します
                        </label>
                    </div>
                    <button class="btn btn-primary next-btn" id="agree-btn" disabled>次へ</button>
                </div>
            </div>
            <!-- Step 9: 面接担当者認証（同意確認） -->
            <div class="card step d-none" id="step-9">
                <div class="card-body">
                    <h5 class="card-title">面接担当者認証</h5>
                    <p>タブレットを面接担当者に渡してください。</p>
                    <form id="auth-form-2">
                        <div class="mb-3">
                            <label for="staff_id_2" class="form-label">担当者ID</label>
                            <input type="text" class="form-control" id="staff_id_2" name="staff_id_2" required>
                        </div>
                        <div class="mb-3">
                            <label for="staff_pw_2" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="staff_pw_2" name="staff_pw_2" required>
                        </div>
                        <button type="submit" class="btn btn-primary">認証して次へ</button>
                    </form>
                </div>
            </div>
            <!-- Step 10: 面接終了 -->
            <div class="card step d-none" id="step-10">
                <div class="card-body">
                    <h5 class="card-title">面接終了</h5>
                    <p>面接が完了しました。入力内容を送信して終了します。</p>
                    <form id="finish-form">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($recruit_data['name']) ?>">
                        <input type="hidden" name="birth" value="<?= htmlspecialchars($recruit_data['birth']) ?>">
                        <input type="hidden" name="kana" id="finish-kana">
                        <input type="hidden" name="gender" id="finish-gender">
                        <input type="hidden" name="address" id="finish-address">
                        <input type="hidden" name="tel" id="finish-tel">
                        <input type="hidden" name="email" id="finish-email">
                        <input type="hidden" name="agree" value="1">
                        <button type="submit" class="btn btn-success">面接終了・送信</button>
                    </form>
                </div>
            </div>
            <script>
                // Step10表示時にhiddenに値をセット
                function setFinishFormValues() {
                    document.getElementById('finish-kana').value = document.getElementById('kana').value;
                    document.getElementById('finish-gender').value = document.getElementById('gender').value;
                    document.getElementById('finish-address').value = document.getElementById('address').value;
                    document.getElementById('finish-tel').value = document.getElementById('tel').value;
                    document.getElementById('finish-email').value = document.getElementById('email').value;
                }
                // ステップ10表示時にセット
                const finishObserver = new MutationObserver(function() {
                    const step10 = document.getElementById('step-10');
                    if (step10 && !step10.classList.contains('d-none')) {
                        setFinishFormValues();
                    }
                });
                finishObserver.observe(document.getElementById('interview-steps'), { childList: false, subtree: true, attributes: true, attributeFilter: ['class'] });

                // 送信処理
                document.getElementById('finish-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    setFinishFormValues();
                    const formData = $(this).serialize();
                    $.post('/process/interview_finish.php', formData, function(data) {
                        if (data.result === 'success') {
                            alert('面接内容を送信しました。画面を閉じてください。');
                            window.close();
                        } else {
                            alert('送信に失敗しました。再度お試しください。');
                        }
                    }, 'json').fail(function() {
                        alert('送信処理中にエラーが発生しました。');
                    });
                });
            </script>
        </div>
        <script>
            // ステップ管理
            let currentStep = 1;
            const totalSteps = 10;

            function showStep(step) {
                document.querySelectorAll('.step').forEach((el, idx) => {
                    el.classList.toggle('d-none', idx !== (step - 1));
                });
                currentStep = step;
            }

            // 次へボタン
            document.querySelectorAll('.next-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // 入力チェック
                    if (currentStep === 2 && !document.getElementById('kana').value.trim()) {
                        alert('名前の読み仮名を入力してください');
                        return;
                    }
                    if (currentStep === 4 && !document.getElementById('gender').value) {
                        alert('性別を選択してください');
                        return;
                    }
                    if (currentStep === 5 && !document.getElementById('address').value.trim()) {
                        alert('住所を入力してください');
                        return;
                    }
                    if (currentStep === 6 && !document.getElementById('tel').value.trim()) {
                        alert('電話番号を入力してください');
                        return;
                    }
                    if (currentStep === 8 && !document.getElementById('agree').checked) {
                        alert('同意が必要です');
                        return;
                    }
                    showStep(currentStep + 1);
                });
            });

            // 就業規則同意チェック
            const agreeCheckbox = document.getElementById('agree');
            if (agreeCheckbox) {
                agreeCheckbox.addEventListener('change', function() {
                    document.getElementById('agree-btn').disabled = !this.checked;
                });
            }

            // 面接担当者認証（入力確認）
            document.getElementById('auth-form-1').addEventListener('submit', function(e) {
                e.preventDefault();
                // ここでID/PW認証処理を実装（Ajaxでサーバーに送信して認証）
                const staffId = document.getElementById('staff_id_1').value;
                const staffPw = document.getElementById('staff_pw_1').value;
                $.post('/process/verify.php', { "username": staffId, "password": staffPw }, function(data) {
                    if (data.result === 'success') {
                        showStep(8);
                    } else {
                        alert('認証に失敗しました。IDまたはパスワードが正しくありません。');
                        showStep(7);
                    }
                }, 'json').fail(function() {
                    alert('認証処理中にエラーが発生しました。');
                });
            });

            // 面接担当者認証（同意確認）
            document.getElementById('auth-form-2').addEventListener('submit', function(e) {
                e.preventDefault();
                const staffId = document.getElementById('staff_id_2').value;
                const staffPw = document.getElementById('staff_pw_2').value;
                // Ajaxで認証（jQuery使用）
                $.post('/process/verify.php', { "username": staffId, "password": staffPw }, function(data) {
                    if (data.result === 'success') {
                        showStep(10);
                    } else {
                        alert('認証に失敗しました。IDまたはパスワードが正しくありません。');
                        showStep(9);
                    }
                }, 'json').fail(function() {
                    alert('認証処理中にエラーが発生しました。');
                });
            });
            // 初期表示
            showStep(1);
        </script>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="alert alert-info">面接用QRコードをカメラで読み取ってください。</div>
        <div id="qr-reader"></div>
        <form id="codeForm" method="get" style="display:none;">
            <input type="hidden" name="code" id="codeInput">
        </form>
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            function onScanSuccess(decodedText, decodedResult) {
                try {
                    // QRコードのデータをBase64デコード
                    let decoded = atob(decodedText);
                    // そのままGETで送信
                    document.getElementById('codeInput').value = decoded;
                    document.getElementById('codeForm').submit();
                } catch (e) {
                    alert('QRコードのデータが不正です');
                }
            }
            // Html5QrcodeScannerの代わりにHtml5Qrcodeを直接使う
            const qrCodeRegionId = "qr-reader";
            const html5QrCode = new Html5Qrcode(qrCodeRegionId);
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    html5QrCode.start(
                        cameras[1].id,
                        {
                            fps: 10,
                            qrbox: 250
                        },
                        onScanSuccess
                    ).catch(err => {
                        alert("カメラの起動に失敗しました: " + err);
                    });
                } else {
                    alert("カメラが見つかりませんでした");
                }
            }).catch(err => {
                alert("カメラの取得に失敗しました: " + err);
            });
        </script>
    <?php endif; ?>
</div>
</body>
</html>