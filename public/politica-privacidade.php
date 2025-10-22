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
  <title>Política de Privacidade</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body>
  <?php require __DIR__ . '/partials/navbar.php'; ?>

  <main class="page-content">
    <div class="container py-5">
      <h1 class="h3 mb-3">Política de Privacidade</h1>
      <p class="text-muted">Atualizado em <?php echo date('d/m/Y'); ?>.</p>

  <p>Este site é operado por <strong><?php echo esc($company); ?></strong> (“nós”). Esta Política descreve como tratamos seus dados pessoais conforme a Lei Geral de Proteção de Dados (LGPD – Lei 13.709/2018).</p>

      <h2 class="h5 mt-4">1) Controlador e contato</h2>
      <ul>
    <li><strong>Empresa:</strong> <?php echo esc($company); ?></li>
    <li><strong>E-mail:</strong> <a href="mailto:<?php echo esc($email); ?>"><?php echo esc($email); ?></a></li>
    <li><strong>WhatsApp:</strong> <a href="<?php echo esc(wa_link($whatsapp, setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento'))); ?>" target="_blank" rel="noopener"><?php echo esc($whatsapp); ?></a></li>
    <li><strong>Região de atendimento:</strong> <?php echo esc($regions); ?></li>
      </ul>

      <h2 class="h5 mt-4">2) Dados que coletamos</h2>
      <ul>
        <li><strong>Formulário de contato:</strong> nome, sobrenome, e-mail, telefone e mensagem.</li>
  <li><strong>Comunicações:</strong> dados enviados por e-mail/WhatsApp.</li>
        <li><strong>Logs técnicos:</strong> IP, data/hora e páginas acessadas (para segurança e estatísticas essenciais).</li>
        <li><strong>Cookies:</strong> apenas os necessários ao funcionamento. Podemos incluir cookies de medição caso adotemos ferramenta de analytics no futuro.</li>
      </ul>

      <h2 class="h5 mt-4">3) Finalidades e bases legais</h2>
      <ul>
        <li><strong>Atender solicitações e orçamentos</strong> (execução de contrato ou procedimentos preliminares).</li>
        <li><strong>Comunicação</strong> sobre serviços e andamento (execução de contrato/interesse legítimo).</li>
        <li><strong>Segurança e prevenção a fraudes</strong> (interesse legítimo/obrigação legal).</li>
        <li><strong>Marketing opcional</strong> (mediante consentimento quando aplicável).</li>
      </ul>

      <h2 class="h5 mt-4">4) Compartilhamento</h2>
      <p>Podemos compartilhar dados com provedores de hospedagem, e-mail, WhatsApp e ferramentas de suporte, sempre sob contratos e apenas o necessário. Não vendemos seus dados.</p>

      <h2 class="h5 mt-4">5) Retenção</h2>
      <p>Registros de contato são mantidos pelo período necessário à prestação do serviço e obrigações legais (ex.: até 24 meses), salvo solicitação de exclusão quando aplicável.</p>

      <h2 class="h5 mt-4">6) Direitos do titular (LGPD)</h2>
  <p>Você pode solicitar: confirmação do tratamento, acesso, correção, anonimização/exclusão, portabilidade, informação sobre compartilhamentos, revogação de consentimento e oposição, pelo e-mail <a href="mailto:<?php echo esc($email); ?>"><?php echo esc($email); ?></a>. Responderemos em até 15 dias.</p>

      <h2 class="h5 mt-4">7) Segurança</h2>
      <p>Adotamos medidas técnicas e administrativas proporcionais para proteger os dados. Nenhuma transmissão é 100% segura, mas buscamos as melhores práticas.</p>

      <h2 class="h5 mt-4">8) Transferências internacionais</h2>
      <p>Serviços de nuvem e e-mail podem armazenar dados fora do Brasil. Nesses casos, observamos os requisitos da LGPD.</p>

      <h2 class="h5 mt-4">9) Atualizações desta Política</h2>
      <p>Podemos atualizar este documento. A versão vigente estará sempre nesta página, com a data de atualização.</p>

      <h2 class="h5 mt-4">10) Encarregado (DPO)</h2>
  <p><strong>Responsável:</strong> <?php echo esc($company); ?> – <a href="mailto:<?php echo esc($email); ?>"><?php echo esc($email); ?></a></p>
    </div>
  </main>

  <?php require __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/main.js"></script>
</body>
</html>