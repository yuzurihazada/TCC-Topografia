<?php
require __DIR__ . '/admin/config.php';

// Carrega feedbacks ativos para o carrossel (limite 10)
$feedbacks = [];
try {
  $stmt = db()->query("SELECT nome, mensagem FROM feedbacks WHERE active=1 ORDER BY id DESC LIMIT 10");
  $feedbacks = $stmt->fetchAll();
} catch (Throwable $e) {
  $feedbacks = [];
}
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topografia | Início</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>

<body>
  <?php require __DIR__ . '/partials/navbar.php'; ?>
  <main class="page-content">
    <div class="container py-5">
      <!-- Diferenciais -->
      <section class="mb-5">
        <div class="section-heading section-heading--center mb-4">
          <span class="title-icon"><i class="bi bi-compass"></i></span>
          <div class="section-heading__body">
            <h2 class="h3 mb-0">Por que escolher a gente</h2>
            <p class="text-muted mb-0">Precisão em campo, entrega confiável e suporte completo.</p>
          </div>
        </div>
        <div class="row g-3 g-md-4">
          <div class="col-md-4">
            <div class="surface-card surface-card--compact tile-card">
              <h3 class="tile-card__title">Precisão e responsabilidade</h3>
              <p class="tile-card__text">Equipamentos de alta precisão, metodologias atualizadas e responsabilidade técnica garantida.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="surface-card surface-card--compact tile-card">
              <h3 class="tile-card__title">Agilidade e acompanhamento</h3>
              <p class="tile-card__text">Fluxo simples de atendimento com orientações em cada etapa até a aprovação do processo.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="surface-card surface-card--compact tile-card">
              <h3 class="tile-card__title">Experiência em campo</h3>
              <p class="tile-card__text">Vivência em levantamentos urbanos e rurais, apoio a obras e regularizações fundiárias.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Processo simples -->
      <section class="mb-5">
        <div class="section-heading section-heading--center mb-4">
          <span class="title-icon"><i class="bi bi-diagram-3"></i></span>
          <div class="section-heading__body">
            <h2 class="h3 mb-0">Como trabalhamos</h2>
            <p class="text-muted mb-0">Processo claro, com acompanhamento em cada fase.</p>
          </div>
        </div>
        <div class="row g-3 g-md-4">
          <div class="col-md-4">
            <div class="surface-card surface-card--compact tile-card process-step">
              <span class="process-step__badge">1</span>
              <h3 class="process-step__title">Briefing e orçamento</h3>
              <p class="process-step__text">Entendemos o cenário do projeto, definimos prioridades e alinhamos investimentos.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="surface-card surface-card--compact tile-card process-step">
              <span class="process-step__badge">2</span>
              <h3 class="process-step__title">Levantamento em campo</h3>
              <p class="process-step__text">Coleta de dados topográficos, georreferenciados e registros fotográficos quando necessário.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="surface-card surface-card--compact tile-card process-step">
              <span class="process-step__badge">3</span>
              <h3 class="process-step__title">Entrega e suporte</h3>
              <p class="process-step__text">Documentos técnicos, plantas e acompanhamento até a aprovação junto aos órgãos.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Teaser de serviços -->
      <section class="mb-5 text-center">
        <div class="section-heading section-heading--center mb-3">
          <span class="title-icon"><i class="bi bi-collection"></i></span>
          <div class="section-heading__body">
            <h2 class="h3 mb-0">Veja os serviços oferecidos</h2>
            <p class="text-muted mb-0">Levantamentos topográficos, georreferenciamento, apoio a obras e muito mais.</p>
          </div>
        </div>
        <a class="btn btn-frame" href="/servicos.php">Ver serviços</a>
      </section>

      <!-- Carrossel de Feedbacks (Bootstrap) -->
      <?php if (!empty($feedbacks)): ?>
      <section class="mb-5">
        <div class="section-heading section-heading--center mb-4">
          <span class="title-icon"><i class="bi bi-chat-quote"></i></span>
          <div class="section-heading__body">
            <h2 class="h3 mb-0">O que nossos clientes dizem</h2>
            <p class="text-muted mb-0">Feedbacks reais sobre entregas, prazos e suporte.</p>
          </div>
        </div>
        <div id="feedbacksCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
          <div class="carousel-inner">
            <?php foreach ($feedbacks as $i => $fb): ?>
              <div class="carousel-item<?php echo $i === 0 ? ' active' : ''; ?>">
                <div class="row justify-content-center">
                  <div class="col-lg-8">
                    <div class="p-4 p-md-5 bg-white border rounded-3 shadow-sm text-center">
                      <div class="mb-2" style="font-size:2rem; line-height:1; color: var(--color-accent);">“</div>
                      <blockquote class="blockquote mb-3" style="font-size:1.05rem;">
                        <p class="mb-0"><?php echo nl2br(esc($fb['mensagem'])); ?></p>
                      </blockquote>
                      <figcaption class="blockquote-footer mb-0">
                        <?php echo esc($fb['nome'] ?: 'Cliente'); ?>
                      </figcaption>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#feedbacksCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#feedbacksCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Próximo</span>
          </button>
        </div>
      </section>
      <?php endif; ?>

      <!-- CTA final -->
      <section class="text-center">
        <div class="section-heading section-heading--center mb-3">
          <span class="title-icon"><i class="bi bi-rocket-takeoff"></i></span>
          <div class="section-heading__body">
            <h2 class="h3 mb-0">Pronto pra começar?</h2>
            <p class="text-muted mb-0">Fale com a gente pelo WhatsApp ou envie sua necessidade pelo formulário.</p>
          </div>
        </div>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
          <a class="btn btn-frame" href="<?php echo esc(wa_link(setting('whatsapp_number', '+5515981194365'), setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento'))); ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
          <a class="btn btn-frame-outline" href="/contato.php">Enviar mensagem</a>
        </div>
      </section>
    </div>
  </main>
  <?php require __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/main.js"></script>
</body>

</html>