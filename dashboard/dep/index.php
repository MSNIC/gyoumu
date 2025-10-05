<?php
// ログイン状態の確認
require_once $_SERVER["DOCUMENT_ROOT"] . "/dashboard/check.php";

include_once $_SERVER["DOCUMENT_ROOT"] . '/core/php/cdb.php';

try {
    $pdo = cdb();
    $stmt = $pdo->query("SELECT id, name, parent, is_parent FROM dep ORDER BY id ASC");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $departments = [];
}
?>
<!DOCTYPE html>
<html lang="ja-jp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>部署管理 | 業務管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">部署管理</h1>
    <div class="mb-3 d-flex justify-content-end align-items-center">
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="window.close();">閉じる</button>
    </div>
    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDepModal">部署を追加</a>

    <!-- 部署追加モーダル -->
    <div class="modal fade" id="addDepModal" tabindex="-1" aria-labelledby="addDepModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form class="modal-content" id="addDepForm" method="post" action="add_dep.php">
          <div class="modal-header">
            <h5 class="modal-title" id="addDepModalLabel">部署を追加</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
          </div>
          <div class="modal-body">
            <div id="addDepAlert" class="alert alert-danger d-none" role="alert"></div>
            <div class="mb-3">
              <label for="depName" class="form-label">部署名</label>
              <input type="text" class="form-control" id="depName" name="name" required>
            </div>
            <div class="mb-3">
              <label for="parentDep" class="form-label">親部署</label>
              <select class="form-select" id="parentDep" name="parent">
            <option value="">なし</option>
            <?php foreach ($departments as $dep): ?>
              <?php if ($dep['is_parent']): ?>
                <option value="<?= htmlspecialchars($dep['id']) ?>"><?= htmlspecialchars($dep['name']) ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
              </select>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="isParent" name="is_parent">
              <label class="form-check-label" for="isParent">
            親部署として設定
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-primary">追加</button>
          </div>
        </form>
        </div>
    </div>

    <!-- 部署リスト -->
    <?php
    // 部署IDをキーに部署情報をマップ
    $depMap = [];
    $childrenMap = [];
    foreach ($departments as $dep) {
        $depMap[$dep['id']] = $dep;
        $parentId = $dep['parent'] ?: 0;
        $childrenMap[$parentId][] = $dep['id'];
    }

    // 階層構造で部署を表示する再帰関数
    function renderDepAccordion($parentId, $childrenMap, $depMap, $level = 0, &$accordionId = 0) {
        if (empty($childrenMap[$parentId])) return;
        echo '<ul class="list-group ms-' . ($level * 3) . '">';
        foreach ($childrenMap[$parentId] as $depId) {
            $dep = $depMap[$depId];
            $hasChildren = !empty($childrenMap[$depId]);
            $currentAccordionId = ++$accordionId;
            echo '<li class="list-group-item d-flex align-items-center justify-content-between">';
            echo '<div class="dep-clickable flex-grow-1" data-depid="' . htmlspecialchars($dep['id']) . '" data-depname="' . htmlspecialchars($dep['name']) . '" data-parent="' . htmlspecialchars($dep['parent']) . '" data-isparent="' . htmlspecialchars($dep['is_parent']) . '" style="cursor:pointer;">';
            if ($hasChildren) {
                // アコーディオンヘッダー
                ?>
                <div class="accordion" id="accordion-<?= $currentAccordionId ?>">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="heading-<?= $currentAccordionId ?>">
                            <button class="accordion-button collapsed py-1 px-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $currentAccordionId ?>" aria-expanded="false" aria-controls="collapse-<?= $currentAccordionId ?>">
                                <?= htmlspecialchars($dep['name']) ?>
                                <?php if ($dep['is_parent']): ?>
                                    <span class="badge bg-info ms-2">親部署</span>
                                <?php endif; ?>
                            </button>
                        </h2>
                        <div id="collapse-<?= $currentAccordionId ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $currentAccordionId ?>" data-bs-parent="#accordion-<?= $currentAccordionId ?>">
                            <div class="accordion-body py-1 px-2">
                                <?php renderDepAccordion($depId, $childrenMap, $depMap, $level + 1, $accordionId); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                // 子なし部署
                echo htmlspecialchars($dep['name']);
                if ($dep['is_parent']) {
                    echo '<span class="badge bg-info ms-2">親部署</span>';
                }
            }
            echo '</div>';
            // アクションメニュー
            ?>
            <div class="dropdown ms-2">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    ⋮
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item dep-edit-btn" href="#" data-depid="<?= htmlspecialchars($dep['id']) ?>" data-depname="<?= htmlspecialchars($dep['name']) ?>" data-parent="<?= htmlspecialchars($dep['parent']) ?>" data-isparent="<?= htmlspecialchars($dep['is_parent']) ?>">編集</a></li>
                    <li><a class="dropdown-item dep-emp-btn" href="#" data-depid="<?= htmlspecialchars($dep['id']) ?>">従業員参照</a></li>
                    <li><a class="dropdown-item dep-delete-btn text-danger" href="#" data-depid="<?= htmlspecialchars($dep['id']) ?>" data-depname="<?= htmlspecialchars($dep['name']) ?>">削除</a></li>
                </ul>
            </div>
            <?php
            echo '</li>';
        }
        echo '</ul>';
    }
    ?>

    <?php if (empty($departments)): ?>
        <ul class="list-group"><li class="list-group-item">部署がありません。</li></ul>
    <?php else: ?>
        <?php
        $accordionId = 0;
        renderDepAccordion(0, $childrenMap, $depMap, 0, $accordionId);
        ?>
    <?php endif; ?>

    <!-- 編集モーダル -->
    <div class="modal fade" id="editDepModal" tabindex="-1" aria-labelledby="editDepModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form class="modal-content" id="editDepForm" method="post" action="edit_dep.php">
          <div class="modal-header">
            <h5 class="modal-title" id="editDepModalLabel">部署を編集</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
          </div>
          <div class="modal-body">
            <div id="editDepAlert" class="alert alert-danger d-none" role="alert"></div>
            <input type="hidden" id="editDepId" name="id">
            <div class="mb-3">
              <label for="editDepName" class="form-label">部署名</label>
              <input type="text" class="form-control" id="editDepName" name="name" required>
            </div>
            <div class="mb-3">
              <label for="editParentDep" class="form-label">親部署</label>
              <select class="form-select" id="editParentDep" name="parent">
                <option value="">なし</option>
                <?php foreach ($departments as $dep): ?>
                  <?php if ($dep['is_parent']): ?>
                    <option value="<?= htmlspecialchars($dep['id']) ?>"><?= htmlspecialchars($dep['name']) ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="editIsParent" name="is_parent">
              <label class="form-check-label" for="editIsParent">
                親部署として設定
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-primary">保存</button>
          </div>
        </form>
      </div>
    </div>

    <!-- 削除確認モーダル -->
    <div class="modal fade" id="deleteDepModal" tabindex="-1" aria-labelledby="deleteDepModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form class="modal-content" id="deleteDepForm" method="post" action="delete_dep.php">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteDepModalLabel">部署の削除確認</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="deleteDepId" name="id">
            <p><span id="deleteDepName"></span> を削除しますか？</p>
            <div id="deleteDepAlert" class="alert alert-danger d-none" role="alert"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
            <button type="submit" class="btn btn-danger">削除</button>
          </div>
        </form>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
        $(function() {
          $('#addDepForm').on('submit', function(e) {
            e.preventDefault();
            $('#addDepAlert').addClass('d-none').text('');
            $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: $(this).serialize(),
              dataType: 'json'
            })
            .done(function(res) {
              if (res.status === 'success') {
            location.reload();
              } else {
            $('#addDepAlert').removeClass('d-none').text(res.message || '追加に失敗しました。');
              }
            })
            .fail(function(xhr) {
              let msg = '通信エラーが発生しました。';
              if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
              }
              $('#addDepAlert').removeClass('d-none').text(msg);
            });
          });
        });
        $(function() {
      // 編集ボタン
      $(document).on('click', '.dep-edit-btn', function(e) {
        e.preventDefault();
        $('#editDepAlert').addClass('d-none').text('');
        $('#editDepId').val($(this).data('depid'));
        $('#editDepName').val($(this).data('depname'));
        $('#editParentDep').val($(this).data('parent'));
        $('#editIsParent').prop('checked', $(this).data('isparent') == 1);
        $('#editDepModal').modal('show');
      });

      // 編集フォーム送信
      $('#editDepForm').on('submit', function(e) {
        e.preventDefault();
        $('#editDepAlert').addClass('d-none').text('');
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json'
        })
        .done(function(res) {
          if (res.status === 'success') {
            location.reload();
          } else {
            $('#editDepAlert').removeClass('d-none').text(res.message || '編集に失敗しました。');
          }
        })
        .fail(function(xhr) {
          let msg = '通信エラーが発生しました。';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          $('#editDepAlert').removeClass('d-none').text(msg);
        });
      });

      // 削除ボタン
      $(document).on('click', '.dep-delete-btn', function(e) {
        e.preventDefault();
        $('#deleteDepAlert').addClass('d-none').text('');
        $('#deleteDepId').val($(this).data('depid'));
        $('#deleteDepName').text($(this).data('depname'));
        $('#deleteDepModal').modal('show');
      });

      // 削除フォーム送信
      $('#deleteDepForm').on('submit', function(e) {
        e.preventDefault();
        $('#deleteDepAlert').addClass('d-none').text('');
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json'
        })
        .done(function(res) {
          if (res.status === 'success') {
            location.reload();
          } else {
            $('#deleteDepAlert').removeClass('d-none').text(res.message || '削除に失敗しました。');
          }
        })
        .fail(function(xhr) {
          let msg = '通信エラーが発生しました。';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          $('#deleteDepAlert').removeClass('d-none').text(msg);
        });
      });

      // 従業員参照ボタン
      $(document).on('click', '.dep-emp-btn', function(e) {
        e.preventDefault();
        var depid = $(this).data('depid');
        window.location.href = '/dashboard/kinmu/emp.php?dep_id=' + encodeURIComponent(depid);
      });
    });
        </script>
</body>
</html>