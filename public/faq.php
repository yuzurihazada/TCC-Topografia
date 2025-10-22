<?php
require __DIR__ . '/admin/config.php';

$faqs = [];
try {
  $stmt = db()->query('SELECT pergunta, resposta FROM faqs WHERE active = 1 ORDER BY order_index ASC, id DESC');
  $faqs = $stmt->fetchAll();
} catch (Throwable $e) {
  $faqs = [];
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topografia | FAQ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="./css/styles.css?v=20251006" rel="stylesheet">
</head>
<body class="faq-page">
<?php require __DIR__ . '/partials/navbar.php'; ?>

<main class="page-content faq-page">
  <div class="container py-5">
    <div class="text-center mb-4">
      <h1 class="display-6 section-title mb-3">Perguntas frequentes</h1>
      <hr class="section-divider mx-auto" style="max-width: 180px;">
      <p class="text-muted mb-0">Estilo “Pessoas também perguntam”: clique para ver a resposta.</p>
    </div>

    <div id="faqPpa" class="faq-ppa">
      <?php if (!$faqs): ?>
        <div class="p-4 text-center text-muted">Estamos preparando as perguntas frequentes. Volte em breve.</div>
      <?php else: ?>
        <?php foreach ($faqs as $index => $faq):
          $collapseId = 'faq-item-' . ($index + 1);
          $faqNumber = sprintf('%02d', $index + 1);
        ?>
          <div class="faq-qa">
            <button class="faq-q collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc($collapseId); ?>" aria-expanded="false" aria-controls="<?php echo esc($collapseId); ?>">
              <span class="faq-label">
                <span class="faq-number"><?php echo esc($faqNumber); ?></span>
                <span class="q-text"><?php echo esc((string) $faq['pergunta']); ?></span>
              </span>
              <span class="chev" aria-hidden="true"><i class="bi bi-chevron-down chev-icon"></i></span>
            </button>
            <div id="<?php echo esc($collapseId); ?>" class="collapse faq-collapse">
              <div class="faq-a"><?php echo nl2br(esc((string) $faq['resposta'])); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/main.js"></script>
</body>
</html>
