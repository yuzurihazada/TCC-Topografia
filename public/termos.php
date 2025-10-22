<?php
require __DIR__ . '/admin/config.php';

$company  = setting('company_name', 'Topografia');
$email    = setting('email', 'alfatopst@gmail.com');
$whatsapp = setting('whatsapp_number', '+5515981194365');
$regions  = setting('regions_text', 'Sorocaba e região');
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Termos de Uso</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body>
  <?php require __DIR__ . '/partials/navbar.php'; ?>
<main class="page-content">
    <div class="container py-5">
        <h1 class="h3 mb-3">Termos de Uso</h1>
        <p class="text-muted">Atualizado em <?php echo date('d/m/Y'); ?>.</p>

        <h2 class="h5 mt-4">1) Aceitação</h2>
        <p>Ao acessar este site, você concorda com estes Termos e com a nossa Política de Privacidade.</p>

        <h2 class="h5 mt-4">2) Objetivo do site</h2>
  <p>Este site divulga serviços de topografia e recebe solicitações de contato/orçamento. Informações publicadas podem ser ilustrativas e não constituem proposta comercial.</p>

        <h2 class="h5 mt-4">3) Orçamentos e serviços</h2>
        <ul>
          <li>Preços e prazos dependem de avaliação técnica e podem variar.</li>
          <li>Serviços são formalizados por proposta/contrato próprios e emissão de TRT quando aplicável.</li>
          <li>Respostas a contatos serão feitas pelos canais informados pelo usuário.</li>
        </ul>

        <h2 class="h5 mt-4">4) Uso permitido e conduta</h2>
        <ul>
          <li>É proibido enviar conteúdo ilegal, ofensivo, difamatório, spam ou com dados de terceiros sem autorização.</li>
          <li>É vedada tentativa de invasão, engenharia reversa ou uso automatizado que prejudique o site.</li>
          <li>Links externos podem levar a sites de terceiros, sob responsabilidade de seus respectivos operadores.</li>
        </ul>

        <h2 class="h5 mt-4">5) Propriedade intelectual</h2>
  <p>Marcas, textos, imagens e layout pertencem a <strong><?php echo esc($company); ?></strong> ou licenciantes. É proibida a reprodução sem autorização.</p>

        <h2 class="h5 mt-4">6) Limitação de responsabilidade</h2>
        <p>Não nos responsabilizamos por indisponibilidades, erros de conteúdo ou danos decorrentes do uso do site, na máxima extensão permitida pela lei.</p>

        <h2 class="h5 mt-4">7) Privacidade</h2>
        <p>O tratamento de dados pessoais é regido pela <a href="/politica-privacidade.php">Política de Privacidade</a>.</p>

        <h2 class="h5 mt-4">8) Alterações</h2>
        <p>Podemos modificar estes Termos a qualquer momento. A versão vigente é a publicada nesta página, com a data de atualização.</p>

        <h2 class="h5 mt-4">9) Contato e foro</h2>
  <p>Dúvidas: <a href="mailto:<?php echo esc($email); ?>"><?php echo esc($email); ?></a> | WhatsApp <a href="<?php echo esc(wa_link($whatsapp, setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento'))); ?>" target="_blank" rel="noopener"><?php echo esc($whatsapp); ?></a>.<br>
  Lei aplicável: Brasil. Foro: Comarca de Sorocaba/SP (ou região de atendimento: <?php echo esc($regions); ?>).</p>
    </div>
</main>
  <?php require __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/main.js"></script>
</body>
</html>