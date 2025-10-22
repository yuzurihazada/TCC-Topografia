<?php
require __DIR__ . '/config.php';
require_admin();
$pdo = db();

// Contadores simples
$countServ = (int)$pdo->query('SELECT COUNT(*) FROM servicos')->fetchColumn();
$countFaq = (int)$pdo->query('SELECT COUNT(*) FROM faqs')->fetchColumn();
$countFb = (int)$pdo->query('SELECT COUNT(*) FROM feedbacks')->fetchColumn();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Dashboard</title>
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
        <span class="admin-breadcrumb">Painel</span>
        <h1 class="admin-title">Painel de Controle</h1>
        <p class="admin-subtitle">Acompanhe os conteúdos publicados e acesse rapidamente as principais áreas.</p>
      </div>
      <div class="admin-toolbar__secondary">
        <a class="btn btn-frame-outline btn-sm" href="/admin/servicos.php"><i class="bi bi-tools me-2"></i>Serviços</a>
        <a class="btn btn-frame-outline btn-sm" href="/admin/faq.php"><i class="bi bi-question-circle me-2"></i>FAQ</a>
        <a class="btn btn-frame-outline btn-sm" href="/admin/feedbacks.php"><i class="bi bi-chat-dots me-2"></i>Feedbacks</a>
      </div>
    </section>

    <div class="row g-4 admin-stats">
      <div class="col-sm-6 col-lg-4">
        <a class="admin-stat" href="/admin/servicos.php">
          <span class="admin-stat__icon"><i class="bi bi-tools"></i></span>
          <span class="admin-stat__meta">Serviços cadastrados</span>
          <span class="admin-stat__value"><?php echo $countServ; ?></span>
        </a>
      </div>
      <div class="col-sm-6 col-lg-4">
        <a class="admin-stat" href="/admin/faq.php">
          <span class="admin-stat__icon"><i class="bi bi-question-circle"></i></span>
          <span class="admin-stat__meta">Perguntas frequentes</span>
          <span class="admin-stat__value"><?php echo $countFaq; ?></span>
        </a>
      </div>
      <div class="col-sm-6 col-lg-4">
        <a class="admin-stat" href="/admin/feedbacks.php">
          <span class="admin-stat__icon"><i class="bi bi-chat-dots"></i></span>
          <span class="admin-stat__meta">Feedbacks recebidos</span>
          <span class="admin-stat__value"><?php echo $countFb; ?></span>
        </a>
      </div>
    </div>
  </div>
</main>

<div class="admin-footer-space"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
