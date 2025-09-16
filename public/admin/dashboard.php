<?php
require __DIR__ . '/config.php';
require_admin();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Dashboard</title>
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
  <h1 class="mb-4">Painel</h1>
  <div class="list-group">
    <a href="/admin/servicos.php" class="list-group-item list-group-item-action">Gerenciar Serviços</a>
    <a href="/admin/faq.php" class="list-group-item list-group-item-action">Gerenciar FAQ</a>
    <a href="/admin/feedbacks.php" class="list-group-item list-group-item-action">Gerenciar Feedbacks</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
