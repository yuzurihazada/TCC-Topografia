<?php require __DIR__ . '/admin/config.php'; ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topografia | Sobre</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body>
  <?php require __DIR__ . '/partials/navbar.php'; ?>
  <main class="page-content">
    <div class="container py-5">
      <div class="row g-4 align-items-center mb-5">
        <div class="col-md-5">
          <img src="/img/about.jpg" alt="Foto de perfil/atuação em topografia" class="img-fluid rounded-4 shadow-sm">
        </div>
        <div class="col-md-7">
          <div class="section-heading mb-3">
            <span class="title-icon"><i class="bi bi-person-vcard"></i></span>
            <div class="section-heading__body">
              <h1 class="h3 mb-0">Sobre mim</h1>
              <p class="text-muted mb-0">Experiência técnica em topografia, engenharia e gestão de projetos.</p>
            </div>
          </div>
          <p class="text-muted mb-3">
            Tenho mais de 20 anos de experiência na área de topografia, engenharia e infraestrutura, atuando como
            desenhista projetista e em trabalhos de campo, sempre alinhado às normas vigentes para obter os melhores
            resultados.
          </p>
          <p class="text-muted mb-3">
            Realizo parcelamento de glebas e estudos geométricos utilizando ferramentas tecnológicas de precisão e processos
            preparados para a metodologia BIM. Minha formação superior em Gestão da Tecnologia da Informação ajuda a
            aprofundar, customizar e trabalhar dados da melhor forma com ferramentas BIM e outras auxiliares na área de
            infraestrutura. Tenho ampla vivência em trabalhos de infraestrutura rodoviária.
          </p>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="surface-card surface-card--compact about-card">
                <h2 class="about-card__title">Atuação e softwares</h2>
                <ul class="about-card__list">
                  <li>Topograph, AutoCAD e Civil 3D</li>
                  <li>Estudos em maquete eletrônica e BIM (Infraworks)</li>
                  <li>Gestão de projetos e gestão estratégica (BSC)</li>
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="surface-card surface-card--compact about-card">
                <h2 class="about-card__title">Equipamentos e campo</h2>
                <ul class="about-card__list">
                  <li>Estações totais</li>
                  <li>GPS pós-processado e RTK</li>
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="surface-card surface-card--compact about-card">
                <h2 class="about-card__title">Formação</h2>
                <ul class="about-card__list">
                  <li>Graduação em Gestão de TI</li>
                  <li>Bacharelado em Engenharia de Produção</li>
                  <li>Pós-graduação Lato Sensu em BIM</li>
                </ul>
              </div>
            </div>
            <div class="col-md-6">
              <div class="surface-card surface-card--compact about-card">
                <h2 class="about-card__title">Certificações</h2>
                <ul class="about-card__list">
                  <li>Especialista em Georreferenciamento</li>
                  <li>Gestão de Projetos (Especialização)</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="surface-card surface-card--compact mt-4 d-flex flex-column gap-3">
            <p class="mb-0"><strong>Registrado no CRT-SP</strong> <?php if (setting('trt_number', '') !== ''): ?>| <strong><?php echo esc(setting('trt_label','TRT')); ?>:</strong> <?php echo esc(setting('trt_number','')); ?><?php endif; ?></p>
            <div class="d-flex gap-2 flex-wrap">
              <a class="btn btn-frame" target="_blank" rel="noopener" href="<?php echo esc(wa_link(setting('whatsapp_number','+5515981194365'), setting('cta_whatsapp_message','Olá, vim pelo site e gostaria de um orçamento'))); ?>">Chamar no WhatsApp</a>
              <a class="btn btn-frame-outline" href="/contato.php">Enviar mensagem</a>
            </div>
          </div>
        </div>
      </div>

      <hr class="my-5 section-divider">

      <div class="row g-4">
        <div class="col-md-6">
          <div class="surface-card surface-card--compact about-card">
            <h2 class="about-card__title">Como posso ajudar</h2>
            <p class="tile-card__text">De estudos geométricos a apoio em campo, integro tecnologia, normas e experiência para entregar precisão e clareza.</p>
            <ul class="about-card__list">
              <li>Projetos com foco em infraestrutura e rodovias</li>
              <li>Modelagem e dados orientados a BIM</li>
              <li>Relatórios e plantas para aprovação</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="surface-card surface-card--compact about-card">
            <h2 class="about-card__title">Atendimento</h2>
            <p class="tile-card__text mb-0">Atendo a aproximadamente um raio de até 100 km de Tatuí.</p>
            <span class="contact-meta">Seg–Sex, 8h–17h</span>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php require __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/main.js"></script>
</body>
</html>
