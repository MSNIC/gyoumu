<?php
// ログインチェック
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";

// 日数選択
$view_modes = [
    '7' => '7日',
    '14' => '14日',
    'month' => '月毎'
];
$mode = $_GET['mode'] ?? '7';
$offset = (int)($_GET['offset'] ?? 0);

// 週・月の切り替え用URL生成
function build_url($params) {
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    $query = array_merge($_GET, $params);
    return $base . '?' . http_build_query($query);
}

// 表示開始日を決定（7日・14日は直近の月曜日から）
$today = new DateTime();
if ($mode === 'month') {
    $start = (clone $today)->modify('first day of this month')->modify("{$offset} month");
    $days = (int)$start->format('t');
} else {
    $w = (int)$today->format('w');
    $monday = (clone $today)->modify('-' . (($w + 6) % 7) . ' days')->modify("+" . ($offset * $mode) . " days");
    $start = $monday;
    $days = (int)$mode;
}

// 日付配列作成
$dates = [];
for ($i = 0; $i < $days; $i++) {
    $dates[] = (clone $start)->modify("+$i days");
}
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>仕業管理 | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .calendar-cell {
            min-width: 40px;
            min-height: 32px;
            border: 1px solid #dee2e6;
            cursor: pointer;
        }
        .calendar-header {
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .calendar-row-date {
            width: 120px;
            text-align: center;
            vertical-align: middle;
            background: #e9ecef;
        }
        .calendar-table {
            font-size: 0.85rem;
        }
        .calendar-controls {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- ローディングオーバーレイ -->
    <div id="loadingOverlay" style="position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.8);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="fs-5 text-white">読み込み中...</div>
        </div>
    </div>
<div class="container-fluid py-3">
    <h1 class="mb-4 fs-3">仕業管理</h1>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.close();">閉じる</button>
    </div>
    <form class="calendar-controls row g-2 align-items-center" method="get">
        <div class="col-auto">
            <label for="mode" class="form-label mb-0">表示日数:</label>
        </div>
        <div class="col-auto">
            <select name="mode" id="mode" class="form-select" onchange="this.form.submit()">
                <?php foreach ($view_modes as $k => $v): ?>
                    <option value="<?= htmlspecialchars($k) ?>" <?= $mode === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary" id="rearrangeBtn">配置変更</button>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-primary ms-2" id="rangeBtn">時間選択</button>
        </div>
    </form>
    <div id="alertContainer"></div>
    <script>
    window.addEventListener('DOMContentLoaded', function () {
        // 赤セル・緑セル・未割付セルがあるか監視してアラート表示
        setTimeout(function() {
            let hasRed = false;
            let hasGreen = false;
            let hasAny = false;
            let allZero = true;
            document.querySelectorAll('.calendar-cell').forEach(function(cell) {
                const bg = cell.style.backgroundColor;
                if (bg === 'rgb(255, 0, 0)' || bg === '#ff0000') {
                    hasRed = true;
                    hasAny = true;
                    allZero = false;
                } else if (bg === 'rgb(0, 255, 0)' || bg === '#00ff00') {
                    hasGreen = true;
                    hasAny = true;
                    allZero = false;
                } else if (cell.textContent.trim() !== '') {
                    hasAny = true;
                    allZero = false;
                }
            });
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = '';

            // アイコン定義
            const warningIcon = '<span style="color:#dc3545;font-size:1.3em;vertical-align:middle;margin-right:0.5em;">&#9888;&#xfe0f;</span>';
            const infoIcon = '<span style="color:#0dcaf0;font-size:1.3em;vertical-align:middle;margin-right:0.5em;">&#8505;&#xfe0f;</span>';

            if (hasRed) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger d-flex align-items-center';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = warningIcon + '欠員が発生しています';
                alertContainer.appendChild(alertDiv);
            }
            if (hasGreen) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success d-flex align-items-center';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = infoIcon + '余剰割付が発生しています';
                alertContainer.appendChild(alertDiv);
            }
            if (!hasAny || allZero) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-primary d-flex align-items-center';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = infoIcon + '勤務割付・仕業作成が行われていません';
                alertContainer.appendChild(alertDiv);
            }
        }, 1200);
    });
    </script>
    <div class="calendar-controls">
    <form id="columnSelectForm">
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-bordered calendar-table" style="min-width: 1800px;">
            <thead>
            <tr>
            <th class="calendar-header">日付</th>
            <?php for ($h = 0; $h < 24; $h++): ?>
                <th class="calendar-header" colspan="2"><?= sprintf('%02d', $h) ?>時</th>
            <?php endfor; ?>
            </tr>
            <tr>
            <th></th>
            <?php for ($h = 0; $h < 24; $h++): ?>
                <?php for ($m = 0; $m < 60; $m += 30): ?>
                <th class="calendar-header">
                    <input type="checkbox" class="column-select-checkbox" data-time="<?= sprintf('%02d:%02d', $h, $m) ?>">
                    <?= sprintf('%02d', $m) ?>
                </th>
                <?php endfor; ?>
            <?php endfor; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dates as $date): ?>
            <tr>
                <td class="calendar-row-date"<?= $date->format('Y-m-d') === $today->format('Y-m-d') ? ' style="background-color: #d1e7dd;"' : '' ?>>
                    <?= $date->format('Y-m-d (D)') ?>
                </td>
                <?php for ($h = 0; $h < 24; $h++): ?>
                <?php for ($m = 0; $m < 60; $m += 30): ?>
                    <td class="calendar-cell" data-date="<?= $date->format('Y-m-d') ?>" data-time="<?= sprintf('%02d:%02d', $h, $m) ?>" id="<?= $date->format('Y-m-d') . ' ' . sprintf('%02d:%02d', $h, $m) ?>"></td>
                <?php endfor; ?>
                <?php endfor; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        </form>
        <div class="d-flex justify-content-center my-3">
            <a href="<?= htmlspecialchars(build_url(['offset' => $offset - 1])) ?>" class="btn btn-outline-secondary me-2">&lt; 前へ</a>
            <span class="align-self-center">
                <?php
                if ($mode === 'month') {
                    echo $start->format('Y年n月');
                } else {
                    $end = (clone $start)->modify('+' . ($days - 1) . ' days');
                    echo $start->format('Y年n月j日') . ' ～ ' . $end->format('Y年n月j日');
                }
                ?>
            </span>
            <a href="<?= htmlspecialchars(build_url(['offset' => $offset + 1])) ?>" class="btn btn-outline-secondary ms-2">次へ &gt;</a>
        </div>
    </div>
</div>
<script>
    // 配置変更ボタンの例
    // 配置人数を保持するマップ
    const cellAssignments = {};

    document.getElementById('rearrangeBtn').addEventListener('click', function () {
        // 選択されたセルがなければ警告
        if (selectedCells.size === 0) {
            alert('配置を変更する時間帯を選択してください。');
            return;
        }
        // 配置人数を入力
        let num = prompt('配置する人数を入力してください（0以上の整数）', '0');
        if (num === null) return;
        num = parseInt(num, 10);
        if (isNaN(num) || num < 0) {
            alert('0以上の整数を入力してください。');
            return;
        }
        // 選択セルに人数を表示し、配列に保持
        document.querySelectorAll('.calendar-cell').forEach(function(cell) {
            const key = cell.dataset.date + ' ' + cell.dataset.time;
            if (selectedCells.has(key)) {
                cell.textContent = num;
                cell.style.backgroundColor = num > 0 ? '#ffc107' : 'orange';
                cellAssignments[key] = num;
            }
        });
        // Bootstrap Icons, Material Icons, 記号の順で警告アイコンを利用
        // Bootstrap Icons
        const bsIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.707c.89 0 1.438-.99.982-1.767L8.982 1.566zm-.982 4.434a.905.905 0 1 1 1.81 0l-.35 3.507a.552.552 0 0 1-1.11 0L8 6zm.002 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>';
        // Material Icons
        const materialIcon = '<span class="material-icons" style="color:red;vertical-align:middle;" title="保存失敗">warning</span>';
        // 記号
        const fallbackIcon = '<span style="color:red;font-weight:bold;" title="保存失敗">&#9888;</span>';

        // 利用可能なアイコンを判定
        function getWarningIcon() {
            // Bootstrap Icons
            if (document.querySelector('link[href*="bootstrap"]')) return bsIcon;
            // Material Icons
            if (document.querySelector('link[href*="fonts.googleapis.com"][href*="Material+Icons"]')) return materialIcon;
            // fallback
            return fallbackIcon;
        }

        // 配置変更をサーバーに送信
        // 選択セルごとにfetchし、すべて終わったらリロード
        const fetches = [];
        selectedCells.forEach(function(key) {
            const [date, time] = key.split(' ');
            const fetchPromise = fetch('s/post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'datetime=' + encodeURIComponent(key) + '&haichi=' + encodeURIComponent(num)
            })
            .then(response => response.json())
            .then(data => {
            if (data.result !== 'success') {
                // 失敗したセルに警告アイコンを表示
                document.querySelectorAll('.calendar-cell').forEach(function(cell) {
                if (cell.dataset.date === date && cell.dataset.time === time) {
                    cell.innerHTML = getWarningIcon();
                }
                });
            }
            })
            .catch(e => {
            // エラー時も警告アイコン
            document.querySelectorAll('.calendar-cell').forEach(function(cell) {
                if (cell.dataset.date === date && cell.dataset.time === time) {
                cell.innerHTML = getWarningIcon();
                }
            });
            });
            fetches.push(fetchPromise);
        });
        Promise.all(fetches).then(() => {
            location.reload();
        });

        // 選択解除
        selectedCells.clear();
        
    });
    // セルクリック例
    // 選択されたセルを保持する配列
    const selectedCells = new Set();

    document.querySelectorAll('.calendar-cell').forEach(function(cell) {
        cell.addEventListener('click', function() {
            const key = cell.dataset.date + ' ' + cell.dataset.time;
            if (selectedCells.has(key)) {
                // すでに選択されていれば解除
                selectedCells.delete(key);
                cell.style.backgroundColor = '';
            } else {
                // 選択
                selectedCells.add(key);
                cell.style.backgroundColor = 'orange';
            }
            // 必要なら選択中セル一覧を表示
            // console.log(Array.from(selectedCells));
        });
    });
    // 「〇時から〇時まで選択」ボタンを追加

    // 日付と時間範囲選択
    const rangeBtn = document.getElementById('rangeBtn');
    rangeBtn.addEventListener('click', function () {
        // 日付選択
        const dateStr = prompt('日付を入力してください（例: 2024-06-01）', (new Date()).toISOString().slice(0, 10));
        if (!dateStr) return;
        // 日付フォーマット簡易チェック
        if (!/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            alert('日付の形式が正しくありません');
            return;
        }
        const startHour = prompt('開始時刻（0-23 時）を入力してください', '9');
        const endHour = prompt('終了時刻（1-24 時）を入力してください', '18');
        if (startHour === null || endHour === null) return;
        const s = parseInt(startHour, 10);
        const e = parseInt(endHour, 10);
        if (isNaN(s) || isNaN(e) || s < 0 || s > 23 || e < 1 || e > 24 || s >= e) {
            alert('正しい時刻を入力してください');
            return;
        }
        // 対象セルを選択
        document.querySelectorAll(`.calendar-cell[data-date="${dateStr}"]`).forEach(function(cell) {
            const [h, m] = cell.dataset.time.split(':').map(Number);
            if (h >= s && h < e) {
                const key = cell.dataset.date + ' ' + cell.dataset.time;
                selectedCells.add(key);
                cell.style.backgroundColor = 'orange';
            }
        });
    });
    // 列ごとのチェックボックスで全日付の該当時刻セルを選択/解除
        document.querySelectorAll('.column-select-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
            const time = checkbox.dataset.time;
            document.querySelectorAll('.calendar-cell[data-time="' + time + '"]').forEach(function(cell) {
                const key = cell.dataset.date + ' ' + cell.dataset.time;
                if (checkbox.checked) {
                selectedCells.add(key);
                cell.style.backgroundColor = 'orange';
                } else {
                selectedCells.delete(key);
                cell.style.backgroundColor = '';
                }
            });
            });
        });
        // ページ読み込み時に配置情報を取得して反映
        window.addEventListener('DOMContentLoaded', function () {
            // 各行ごとに赤セルがあるかを記録するマップ
            const redRows = {};

            // すべてのセルの日付・時刻を収集
            document.querySelectorAll('.calendar-cell').forEach(function(cell) {
            const key = cell.dataset.date + ' ' + cell.dataset.time;
            // 1セルずつ個別にリクエスト
            fetch('s/get.php?dt=' + encodeURIComponent(key))
            .then(response => response.json())
            .then(async(data) => {
            // dataは { dt: "yyyy-mm-dd HH:mm",haichi:数値, j_haichi:数値} の形式を想定
            info = data;
            if (!info) return;
            const haichi = parseInt(info.haichi, 10);
            const j_haichi = parseInt(info.j_haichi, 10);
            if (haichi === 0 && j_haichi === 0) return;
            await (cell.textContent = haichi + ' / ' + j_haichi);
            await (cell.style.textAlign = 'center');
            await (cell.style.verticalAlign = 'middle');
            await (cell.style.color = '#fff');
            await (cell.style.whiteSpace = 'nowrap');
            if (haichi > j_haichi) {
                await (cell.style.backgroundColor = '#ff0000');
                // 赤セルがある行を記録
                redRows[cell.dataset.date] = true;
            } else if (haichi < j_haichi) {
                await (cell.style.backgroundColor = '#00ff00');
            } else {
                await (cell.style.backgroundColor = '#ffc107');
                await (cell.style.color = '#212529');
            }
            })
            .catch(e => {
            // エラー時は何もしない
            });
            });

            // すべてのfetchが終わった後に日付セルを赤枠で囲む
            // 多少遅延させて実行（fetch完了後に実行するには本来Promise.all等が必要だが、ここでは簡易的にsetTimeoutで対応）
            setTimeout(function() {
            Object.keys(redRows).forEach(function(dateStr) {
            // 対象の日付セルを取得
            document.querySelectorAll('.calendar-row-date').forEach(function(dateCell) {
            if (dateCell.textContent.trim().startsWith(dateStr)) {
                dateCell.style.border = '2px solid #ff0000';
            }
            });
            });
            // ローディングオーバーレイを非表示にする
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'none';
            }, 1000);
        });
</script>
</body>
</html>