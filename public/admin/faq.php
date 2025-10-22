<?php
require __DIR__ . '/config.php';
require_admin();

// Conexão PDO
$pdo = db();

$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare('INSERT INTO faqs (pergunta, resposta, order_index, active) VALUES (?, ?, ?, ?)');
  $stmt->execute([
    $_POST['pergunta'],
    $_POST['resposta'],
    isset($_POST['order_index']) ? (int)$_POST['order_index'] : 0,
    isset($_POST['active']) ? 1 : 0
  ]);
  header('Location: /admin/faq.php');
  exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('DELETE FROM faqs WHERE id = ?');
  $stmt->execute([$_GET['id']]);
  header('Location: /admin/faq.php');
  exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('UPDATE faqs SET pergunta=?, resposta=?, order_index=?, active=? WHERE id=?');
  $stmt->execute([
    $_POST['pergunta'],
    $_POST['resposta'],
    isset($_POST['order_index']) ? (int)$_POST['order_index'] : 0,
    isset($_POST['active']) ? 1 : 0,
    $_GET['id']
  ]);
  header('Location: /admin/faq.php');
  exit;
}

$actionToggle = $_GET['action'] ?? '';
if ($actionToggle === 'toggle' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('UPDATE faqs SET active = CASE WHEN active = 1 THEN 0 ELSE 1 END WHERE id = ?');
  $stmt->execute([$_GET['id']]);
  header('Location: /admin/faq.php');
  exit;
}

// Busca
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
  $like = '%' . $q . '%';
  $stmt = $pdo->prepare('SELECT * FROM faqs WHERE pergunta LIKE ? OR resposta LIKE ? ORDER BY order_index ASC, id DESC');
  $stmt->execute([$like, $like]);
  $faqs = $stmt->fetchAll();
} else {
  $faqs = $pdo->query('SELECT * FROM faqs ORDER BY order_index ASC, id DESC')->fetchAll();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | FAQ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css?v=20251006">
  <link rel="stylesheet" href="/css/admin.css?v=20251021">
</head>
<body class="admin-body">
<header class="admin-topbar">
  <div class="admin-topbar__inner">
    <a class="admin-brand" href="/admin/dashboard.php">
      <span class="brand-mark">AS</span>
      <span class="brand-text">
        <strong>Área Administrativa</strong>
        <span>Alessandro Silva</span>
      </span>
    </a>
    <div class="admin-actions">
      <a class="btn btn-outline-light btn-sm" href="/" target="_blank" rel="noopener">Ver site</a>
      <a class="btn btn-frame btn-sm" href="/logout.php">Sair</a>
    </div>
  </div>
</header>

<main class="admin-shell">
  <div class="container-xxl">
    <section class="surface-card admin-page-header">
      <div class="admin-meta">
        <span class="admin-breadcrumb">Conteúdo</span>
        <h1 class="admin-title">Perguntas frequentes</h1>
        <p class="admin-subtitle">Organize as dúvidas dos clientes e defina a ordem em que aparecem no site.</p>
      </div>
      <div class="admin-toolbar__secondary">
        <a class="btn btn-outline-secondary btn-sm" href="./dashboard.php"><i class="bi bi-arrow-left me-2"></i>Voltar</a>
        <button class="btn btn-frame btn-sm" data-bs-toggle="modal" data-bs-target="#novo"><i class="bi bi-plus-circle me-2"></i>Novo FAQ</button>
      </div>
    </section>

    <section class="surface-card admin-toolbar">
      <div class="admin-toolbar__primary">
        <span class="admin-breadcrumb mb-0">Total: <?php echo count($faqs); ?> itens</span>
      </div>
      <form class="admin-search" method="get" action="">
        <input type="text" class="form-control form-control-sm" placeholder="Buscar..." name="q" value="<?php echo esc($q); ?>">
        <button class="btn btn-frame-outline btn-sm" type="submit"><i class="bi bi-search me-1"></i>Buscar</button>
        <?php if ($q !== ''): ?>
          <a class="btn btn-outline-secondary btn-sm" href="/admin/faq.php">Limpar</a>
        <?php endif; ?>
      </form>
    </section>

    <?php if (!$faqs): ?>
      <div class="admin-empty">
        <strong>Nenhuma pergunta cadastrada.</strong>
        <p class="mb-0">Utilize o botão “Novo FAQ” para registrar as principais dúvidas.</p>
      </div>
    <?php else: ?>
      <div class="surface-card admin-table">
        <div class="table-responsive">
          <table class="table align-middle table-hover table-striped mb-0">
            <thead>
              <tr>
                <th>Ordem</th>
                <th>Pergunta</th>
                <th>Ativo</th>
                <th style="max-width:420px">Resposta</th>
                <th style="width:200px">Ações</th>
              </tr>
            </thead>
            <tbody>
            <?php $modals = ''; foreach ($faqs as $row): ?>
              <tr>
                <td><?php echo (int)$row['order_index']; ?></td>
                <td><?php echo esc((string)$row['pergunta']); ?></td>
                <td>
                  <?php if (!empty($row['active'])): ?>
                    <span class="badge bg-success">Ativo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Inativo</span>
                  <?php endif; ?>
                </td>
                <td class="small" style="max-width:420px;">
                  <?php $txt=(string)$row['resposta']; $short=mb_strlen($txt)>140?mb_substr($txt,0,140).'…':$txt; echo esc($short); ?>
                </td>
                <td>
                  <div class="admin-actions-grid">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#view-<?php echo (int)$row['id']; ?>">Ver</button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#edit-<?php echo (int)$row['id']; ?>">Editar</button>
                    <a class="btn btn-outline-warning btn-sm" href="?action=toggle&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Deseja <?php echo !empty($row['active']) ? 'desativar' : 'ativar'; ?> este item?');"><?php echo !empty($row['active']) ? 'Desativar' : 'Ativar'; ?></a>
                    <a class="btn btn-outline-danger btn-sm" href="?action=delete&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Excluir esta pergunta?');">Excluir</a>
                  </div>
                </td>
              </tr>
            <?php ob_start(); ?>
              <div class="modal fade" id="view-<?php echo (int)$row['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Pergunta: <?php echo esc((string)$row['pergunta']); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                      <dl class="row mb-0">
                        <dt class="col-sm-3">Pergunta</dt>
                        <dd class="col-sm-9"><?php echo esc((string)$row['pergunta']); ?></dd>
                        <dt class="col-sm-3">Resposta</dt>
                        <dd class="col-sm-9"><div class="admin-note"><?php echo esc((string)$row['resposta']); ?></div></dd>
                      </dl>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="edit-<?php echo (int)$row['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <form class="modal-content" method="post" action="?action=update&id=<?php echo (int)$row['id']; ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Editar FAQ #<?php echo (int)$row['id']; ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-8">
                          <label class="form-label">Pergunta</label>
                          <input type="text" name="pergunta" class="form-control" required value="<?php echo esc((string)$row['pergunta']); ?>">
                        </div>
                        <div class="col-md-2">
                          <label class="form-label">Ordem</label>
                          <input type="number" name="order_index" class="form-control" value="<?php echo (int)$row['order_index']; ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="active" id="act-<?php echo (int)$row['id']; ?>" <?php echo !empty($row['active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="act-<?php echo (int)$row['id']; ?>">Ativo</label>
                          </div>
                        </div>
                        <div class="col-12">
                          <label class="form-label">Resposta</label>
                          <textarea name="resposta" class="form-control" rows="6" required><?php echo esc((string)$row['resposta']); ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                      <button class="btn btn-frame">Salvar</button>
                    </div>
                  </form>
                </div>
              </div>
            <?php $modals .= ob_get_clean(); endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php echo $modals; ?>
    <?php endif; ?>
  </div>
</main>

<div class="admin-footer-space"></div>

<div class="modal fade" id="novo" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="?action=create">
      <div class="modal-header">
        <h5 class="modal-title">Novo FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Pergunta</label>
            <input type="text" name="pergunta" class="form-control" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Ordem</label>
            <input type="number" name="order_index" class="form-control" value="0">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="active" id="act-new" checked>
              <label class="form-check-label" for="act-new">Ativo</label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label">Resposta</label>
            <textarea name="resposta" class="form-control" rows="5" required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-frame">Salvar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
