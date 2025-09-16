<?php
require __DIR__ . '/config.php';
require_admin();

$action = $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare('INSERT INTO servicos (titulo, descricao, preco) VALUES (?, ?, ?)');
  $stmt->execute([$_POST['titulo'], $_POST['descricao'], $_POST['preco'] ?: null]);
  header('Location: /admin/servicos.php');
  exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('DELETE FROM servicos WHERE id = ?');
  $stmt->execute([$_GET['id']]);
  header('Location: /admin/servicos.php');
  exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
  $stmt = $pdo->prepare('UPDATE servicos SET titulo=?, descricao=?, preco=? WHERE id=?');
  $stmt->execute([$_POST['titulo'], $_POST['descricao'], $_POST['preco'] ?: null, $_GET['id']]);
  header('Location: /admin/servicos.php');
  exit;
}

$servicos = $pdo->query('SELECT * FROM servicos ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Serviços</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
  <a class="navbar-brand" href="./dashboard.php">Admin</a>
  <a class="btn btn-outline-light" href="../public/logout.php">Sair</a>
  </div>
</nav>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Serviços</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novo">Novo</button>
  </div>
  <p class="text-muted">Lista de serviços em construção.</p>
</div>

<!-- Modal Novo -->
<div class="modal fade" id="novo" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="?action=create">
      <div class="modal-header">
        <h5 class="modal-title">Novo Serviço</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Preço (opcional)</label>
          <input type="number" step="0.01" name="preco" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
