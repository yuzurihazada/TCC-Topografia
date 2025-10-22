<?php
require __DIR__ . '/admin/config.php';

// Busca todos os serviços ativos para montar a vitrine da página pública.
$servicos = [];
try {
  $stmt = db()->query('SELECT id, titulo, descricao, preco FROM servicos WHERE active = 1 ORDER BY order_index ASC, id DESC');
  $servicos = $stmt->fetchAll();
} catch (Throwable $e) {
  // Evita quebrar a página caso o banco não responda.
  $servicos = [];
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topografia | Serviços</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body>
  <?php require __DIR__ . '/partials/navbar.php'; ?>
  <main class="page-content services-page">
    <div class="container py-5">
        <div class="section-heading mb-4">
          <span class="title-icon"><i class="bi bi-rulers"></i></span>
          <div class="section-heading__body">
            <h1 class="h3 mb-0">Serviços</h1>
            <p class="text-muted mb-0">Soluções completas em topografia, georreferenciamento e apoio a obras.</p>
          </div>
        </div>

      <?php if (!$servicos): ?>
        <!-- Fallback quando não há serviços cadastrados -->
        <div class="surface-card surface-card--compact text-center text-muted">Em breve publicaremos nossos serviços.</div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($servicos as $s): ?>
            <!-- Card individual com dados vindos do banco -->
            <div class="col-md-6 col-lg-4">
                <article class="surface-card surface-card--compact service-card">
                  <h2 class="service-card__title"><?= esc($s['titulo']) ?></h2>
                  <p class="service-card__description"><?= esc((string)$s['descricao']) ?></p>
                  <div class="service-card__footer">
                    <?php if (!is_null($s['preco'])): ?>
                      <span class="badge text-uppercase service-card__badge">A partir de R$ <?= number_format((float)$s['preco'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                    <a href="/contato.php?servico=<?= urlencode($s['titulo']) ?>" class="btn btn-frame">Contatar</a>
                  </div>
                </article>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
  <?php require __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/main.js"></script>
</body>
</html>
