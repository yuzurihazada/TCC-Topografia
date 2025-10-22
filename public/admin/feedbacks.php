<?php
require __DIR__ . '/config.php';
require_admin();

// Conexão PDO
$pdo = db();

$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare('INSERT INTO feedbacks (nome, email, mensagem) VALUES (?, ?, ?)');
  $stmt->execute([$_POST['nome'], $_POST['email'] ?: null, $_POST['mensagem']]);
  header('Location: /admin/feedbacks.php');
  exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('DELETE FROM feedbacks WHERE id = ?');
  $stmt->execute([$_GET['id']]);
  header('Location: /admin/feedbacks.php');
  exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('UPDATE feedbacks SET nome=?, email=?, mensagem=? WHERE id=?');
  $stmt->execute([$_POST['nome'], $_POST['email'] ?: null, $_POST['mensagem'], $_GET['id']]);
  header('Location: /admin/feedbacks.php');
  exit;
}

if ($action === 'toggle' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('UPDATE feedbacks SET active = CASE WHEN active = 1 THEN 0 ELSE 1 END WHERE id = ?');
  $stmt->execute([$_GET['id']]);
  header('Location: /admin/feedbacks.php');
  exit;
}

if ($action === 'delete_all') {
  $pdo->exec('DELETE FROM feedbacks');
  header('Location: /admin/feedbacks.php');
  exit;
}

if ($action === 'seed_examples') {
  $examples = [
    ['Lucas Almeida', 'lucas.almeida@example.com', 'Serviço muito bem executado, prazos cumpridos e atendimento excelente. Recomendo!'],
    ['Mariana Silva', null, 'Equipe atenciosa e precisa nas medições. O relatório facilitou a aprovação do projeto.'],
    ['João Pedro', 'joaopedro@example.com', 'Ótima comunicação e seriedade. O levantamento ficou bem completo.'],
    ['Ana Carolina', null, 'Muito satisfeita com o resultado. Ajudou demais na regularização do imóvel.'],
    ['Ricardo Santos', 'ricardo.s@example.com', 'Profissionais competentes, preço justo e entrega rápida.'],
    ['Patrícia Souza', null, 'Atendimento rápido e com responsabilidade técnica. Voltarei a contratar.'],
  ];
  $stmt = $pdo->prepare('INSERT INTO feedbacks (nome, email, mensagem, active) VALUES (?, ?, ?, 1)');
  foreach ($examples as $ex) { $stmt->execute($ex); }
  header('Location: /admin/feedbacks.php');
  exit;
}

$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
  $like = '%' . $q . '%';
  $stmt = $pdo->prepare('SELECT * FROM feedbacks WHERE nome LIKE ? OR email LIKE ? OR mensagem LIKE ? ORDER BY id DESC');
  $stmt->execute([$like, $like, $like]);
  $items = $stmt->fetchAll();
} else {
  $items = $pdo->query('SELECT * FROM feedbacks ORDER BY id DESC')->fetchAll();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Feedbacks</title>
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
        <span class="admin-breadcrumb">Relacionamento</span>
        <h1 class="admin-title">Feedbacks</h1>
        <p class="admin-subtitle">Centralize depoimentos e mensagens para exibir no site ou manter como histórico de atendimento.</p>
      </div>
    </section>

    <section class="surface-card admin-toolbar">
      <div class="admin-toolbar__primary">
        <span class="admin-breadcrumb mb-0">Total: <?php echo count($items); ?> registros</span>
        <a class="btn btn-outline-secondary btn-sm" href="./dashboard.php"><i class="bi bi-arrow-left me-2"></i>Voltar</a>
        <a class="btn btn-outline-secondary btn-sm" href="?action=seed_examples" onclick="return confirm('Popular exemplos de feedback?');"><i class="bi bi-stars me-2"></i>Popular exemplos</a>
        <a class="btn btn-outline-danger btn-sm" href="?action=delete_all" onclick="return confirm('Excluir TODOS os feedbacks? Esta ação não pode ser desfeita.');"><i class="bi bi-trash me-2"></i>Excluir todos</a>
        <button class="btn btn-frame btn-sm" data-bs-toggle="modal" data-bs-target="#novo"><i class="bi bi-plus-circle me-2"></i>Novo feedback</button>
      </div>
      <form class="admin-search" method="get" action="">
        <input type="text" class="form-control form-control-sm" placeholder="Buscar..." name="q" value="<?php echo esc($q); ?>">
        <button class="btn btn-frame-outline btn-sm" type="submit"><i class="bi bi-search me-1"></i>Buscar</button>
        <?php if ($q !== ''): ?>
          <a class="btn btn-outline-secondary btn-sm" href="/admin/feedbacks.php">Limpar</a>
        <?php endif; ?>
      </form>
    </section>

    <?php if (!$items): ?>
      <div class="admin-empty">
        <strong>Nenhum feedback cadastrado.</strong>
        <p class="mb-0">Quando receber retornos dos clientes, registre aqui para manter o histórico.</p>
      </div>
    <?php else: ?>
      <div class="surface-card admin-table">
        <div class="table-responsive">
          <table class="table align-middle table-hover table-striped mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th style="max-width:420px">Mensagem</th>
                <th>Ativo</th>
                <th>Data</th>
                <th style="width:220px">Ações</th>
              </tr>
            </thead>
            <tbody>
            <?php $modals = ''; foreach ($items as $row): ?>
              <tr>
                <td><?php echo (int)$row['id']; ?></td>
                <td><?php echo esc((string)$row['nome']); ?></td>
                <td class="text-muted small"><?php echo esc((string)($row['email'] ?? '')); ?></td>
                <td class="small" style="max-width:420px;">
                  <?php $msg=(string)$row['mensagem']; $short=mb_strlen($msg)>140?mb_substr($msg,0,140).'…':$msg; echo esc($short); ?>
                </td>
                <td>
                  <?php if (!empty($row['active'])): ?>
                    <span class="badge bg-success">Ativo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Inativo</span>
                  <?php endif; ?>
                </td>
                <td class="text-muted small"><?php echo esc((string)($row['created_at'] ?? '')); ?></td>
                <td>
                  <div class="admin-actions-grid">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#view-<?php echo (int)$row['id']; ?>">Ver</button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#edit-<?php echo (int)$row['id']; ?>">Editar</button>
                    <a class="btn btn-outline-warning btn-sm" href="?action=toggle&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Deseja <?php echo !empty($row['active']) ? 'desativar' : 'ativar'; ?> este feedback?');"><?php echo !empty($row['active']) ? 'Desativar' : 'Ativar'; ?></a>
                    <a class="btn btn-outline-danger btn-sm" href="?action=delete&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Excluir este feedback?');">Excluir</a>
                  </div>
                </td>
              </tr>
            <?php ob_start(); ?>
              <div class="modal fade" id="view-<?php echo (int)$row['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Feedback de <?php echo esc((string)$row['nome']); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                      <dl class="row mb-0">
                        <dt class="col-sm-3">Nome</dt>
                        <dd class="col-sm-9"><?php echo esc((string)$row['nome']); ?></dd>
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><?php echo esc((string)($row['email'] ?? '')); ?></dd>
                        <dt class="col-sm-3">Data</dt>
                        <dd class="col-sm-9"><?php echo esc((string)($row['created_at'] ?? '')); ?></dd>
                        <dt class="col-sm-3">Mensagem</dt>
                        <dd class="col-sm-9"><div class="admin-note"><?php echo esc((string)$row['mensagem']); ?></div></dd>
                      </dl>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="edit-<?php echo (int)$row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                  <form class="modal-content" method="post" action="?action=update&id=<?php echo (int)$row['id']; ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Editar Feedback #<?php echo (int)$row['id']; ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required value="<?php echo esc((string)$row['nome']); ?>">
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Email (opcional)</label>
                        <input type="email" name="email" class="form-control" value="<?php echo esc((string)($row['email'] ?? '')); ?>">
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Mensagem</label>
                        <textarea name="mensagem" class="form-control" rows="4" required><?php echo esc((string)$row['mensagem']); ?></textarea>
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
        <h5 class="modal-title">Novo Feedback</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Mensagem</label>
          <textarea name="mensagem" class="form-control" rows="4" required></textarea>
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
