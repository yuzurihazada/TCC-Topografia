<?php
// Página de contato público: salva lead, envia e-mail e prepara WhatsApp.
require __DIR__ . '/admin/config.php';

// Handler do formulário (no topo para organização)
$errors = [];
$success = false;
$emailWarning = false;
$waLink = null;

// Carrega dados de configuração que preenchem texto do formulário e CTA.
$companyName = setting('company_name', 'Topografia');
$defaultEmail = app_env('GMAIL_SENDER') ?: 'alessandrosilva.topografia@gmail.com';
$primaryEmail = setting('email', $defaultEmail);
$waNumberSetting = setting('whatsapp_number', '+5515981194365');
$waDefaultMessage = setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento');
$hoursText = setting('hours_text', 'Seg–Sex, 8h–17h');
$regionsText = setting('regions_text', 'Tatuí e região');
$waDigits = preg_replace('/\D+/', '', $waNumberSetting);
$waDialLink = $waDigits !== '' ? '+' . $waDigits : '';

// Campos enviados pelo usuário via POST (pré-preenchimento também usa esses valores).
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$email      = trim($_POST['email'] ?? '');
$message    = trim($_POST['message'] ?? '');
$honeypot   = trim($_POST['website'] ?? '');

// Identifica se o usuário veio a partir de um serviço específico (via GET ou POST).
$serviceTopic = trim($_GET['servico'] ?? $_POST['service_topic'] ?? '');
if (function_exists('mb_strlen')) {
  if (mb_strlen($serviceTopic) > 120) {
    $serviceTopic = mb_substr($serviceTopic, 0, 120);
  }
} elseif (strlen($serviceTopic) > 120) {
  $serviceTopic = substr($serviceTopic, 0, 120);
}

// Monta a mensagem padrão enviada para o WhatsApp com os dados do formulário.
if (!function_exists('build_contact_whatsapp_message')) {
  function build_contact_whatsapp_message(string $service, string $firstName, string $lastName, string $phone, string $email, string $message): string {
    $service = trim($service);
    $firstName = trim($firstName);
    $lastName = trim($lastName);
    $phone = trim($phone);
    $email = trim($email);
    $message = trim(preg_replace("/\r\n|\r/", "\n", $message));

    $lines = [];

    if ($service !== '') {
      $lines[] = 'Olá, gostaria de um orçamento para o serviço ' . $service . '.';
    } else {
      $lines[] = 'Olá,';
    }

    $lines[] = '';

    $fullName = trim($firstName . ' ' . $lastName);
    $lines[] = 'Nome: ' . ($fullName !== '' ? $fullName : '-');
    $lines[] = 'Telefone: ' . ($phone !== '' ? $phone : '-');
    $lines[] = 'E-mail: ' . ($email !== '' ? $email : '-');
    $lines[] = 'Mensagem: ' . ($message !== '' ? $message : '-');

    return implode("\n", $lines);
  }
}

// Processa o formulário quando o usuário submete a mensagem.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($honeypot !== '') {
    // Campo invisível preenchido indica possível bot.
    $errors[] = 'Não foi possível enviar o formulário. Tente novamente.';
  }
  // Validações básicas para garantir contato mínimo e formato correto.
  if ($first_name === '') $errors[] = 'Informe o nome.';
  if ($last_name === '')  $errors[] = 'Informe o sobrenome.';
  if ($phone === '' || strlen(preg_replace('/\D+/', '', $phone)) < 10) $errors[] = 'Informe um telefone válido.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Informe um e-mail válido.';
  if ($message === '') $errors[] = 'Descreva sua necessidade.';
  if (mb_strlen($message) > 2000) $errors[] = 'Mensagem muito longa. Utilize até 2000 caracteres.';

  if (!$errors) {
    try {
      $pdo = db();
      $stmt = $pdo->prepare('INSERT INTO contacts (first_name, last_name, phone, email, message, source, status)
        VALUES (?, ?, ?, ?, ?, \'site\', \'new\')');
      $messageForStore = $message;
      if ($serviceTopic !== '') {
        $messageForStore = 'Serviço de interesse: ' . $serviceTopic . "\n\n" . $messageForStore;
      }

    $stmt->execute([$first_name, $last_name, $phone, $email, $messageForStore]);

    // Mensagem personalizada de WhatsApp e link pronto para continuar o atendimento.
    $waMsg = build_contact_whatsapp_message($serviceTopic, $first_name, $last_name, $phone, $email, $message);
      $waLink = wa_link($waNumberSetting, $waMsg);

    // Corpo do e-mail enviado via Gmail API para a caixa principal.
    $emailBody = '<h2>Novo contato pelo site</h2>'
        . '<p><strong>Nome:</strong> ' . esc($first_name . ' ' . $last_name) . '</p>'
        . '<p><strong>Telefone:</strong> ' . esc($phone) . '</p>'
        . '<p><strong>E-mail:</strong> ' . esc($email) . '</p>';

      if ($serviceTopic !== '') {
        $emailBody .= '<p><strong>Serviço de interesse:</strong> ' . esc($serviceTopic) . '</p>';
      }

      $emailBody .= '<p><strong>Mensagem:</strong><br>' . nl2br(esc($message)) . '</p>';

      $mailSent = gmail_send_message($primaryEmail, 'Novo contato - ' . $first_name . ' ' . $last_name, $emailBody, $companyName);
      if (!$mailSent) {
        $emailWarning = true;
      }

      $success = true;
      $first_name = $last_name = $phone = $email = $message = '';
    } catch (Throwable $e) {
      // Captura qualquer falha (conexão/banco) e apresenta mensagem genérica.
      $errors[] = 'Erro ao salvar seu contato. Tente novamente.';
    }
  }
}

// Mensagem que alimenta o botão verde antes do envio (mantém dados digitados).
$waPrefillMessage = build_contact_whatsapp_message($serviceTopic, $first_name, $last_name, $phone, $email, $message);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topografia | Contato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body>
  <?php include __DIR__ . '/partials/navbar.php'; ?>
<main class="page-content">
  <section class="contact-section py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-xxl-10">
          <div class="contact-shell">
            <!-- Coluna com informações de contato e CTA rápidas -->
            <div class="contact-pane contact-pane--info">
              <span class="badge rounded-pill text-bg-success mb-3">Fale conosco</span>
              <h1 class="display-6 fw-semibold mb-3">Vamos conversar sobre seu projeto?</h1>
              <p class="text-muted mb-4">Envie seus dados e conte um pouco da sua necessidade. Respondemos rápido e, se preferir, você pode continuar o atendimento diretamente no WhatsApp.</p>

              <!-- Lista com canais oficiais de atendimento -->
              <ul class="list-unstyled contact-quick mb-4">
                <li>
                  <span class="contact-icon"><i class="bi bi-whatsapp"></i></span>
                  <div>
                    <strong>WhatsApp</strong>
                    <a class="d-block" target="_blank" rel="noopener" href="<?= esc(wa_link($waNumberSetting, $waDefaultMessage)) ?>"><?= esc($waNumberSetting) ?></a>
                  </div>
                <li>
                  <span class="contact-icon"><i class="bi bi-envelope"></i></span>
                  <div>
                    <strong>E-mail</strong>
                    <a class="d-block" href="mailto:<?= esc($primaryEmail) ?>"><?= esc($primaryEmail) ?></a>
                  </div>
                </li>
                <li>
                  <span class="contact-icon"><i class="bi bi-geo-alt"></i></span>
                  <div>
                    <strong>Atendimento</strong>
                    <span><?= esc($regionsText) ?></span>
                    <span class="small opacity-75"><?= esc($hoursText) ?></span>
                  </div>
                </li>
              </ul>

              <!-- Destaque final com atalhos para WhatsApp e ligação -->
              <div class="contact-highlight">
                <span class="fw-semibold">Preferência?</span>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <a class="btn btn-frame" target="_blank" rel="noopener" href="<?= esc(wa_link($waNumberSetting, $waDefaultMessage)) ?>">Chamar no WhatsApp</a>
                  <a class="btn btn-frame-outline" href="tel:<?= esc($waDialLink) ?>">Ligar agora</a>
                </div>
              </div>
            </div>
            <!-- Coluna com o formulário que alimenta Gmail e WhatsApp -->
            <div class="contact-pane contact-pane--form">
              <h2 class="h4 mb-3 text-center text-md-start">Envie uma mensagem</h2>

              <?php if ($serviceTopic !== ''): ?>
                <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3 mb-md-4">
                  <i class="bi bi-bookmark-star-fill fs-5"></i>
                  <span><span class="fw-semibold">Serviço selecionado:</span> <?= esc($serviceTopic) ?></span>
                </div>
              <?php endif; ?>

              <?php if (!empty($errors)): ?>
                <!-- Exibe lista de validações que falharam -->
                <div class="alert alert-danger" role="alert">
                  <strong>Corrija os campos:</strong>
                  <ul class="mb-0">
                    <?php foreach ($errors as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <?php if (!empty($success)): ?>
                <!-- Confirma que o contato foi armazenado e enviado -->
                <div class="alert alert-success" role="alert">
                  Recebemos seu contato! Em breve retornaremos com a resposta.
                </div>
                <?php if ($emailWarning): ?>
                  <div class="alert alert-warning" role="alert">
                    Houve um problema ao enviar a notificação por e-mail. Entraremos em contato assim mesmo.
                  </div>
                <?php endif; ?>
                <?php if (!empty($waLink)): ?>
                  <p><a class="btn btn-success" target="_blank" rel="noopener" href="<?= esc($waLink) ?>">Continuar no WhatsApp</a></p>
                <?php endif; ?>
              <?php endif; ?>

              <form method="post" action="/contato.php" class="contact-form needs-validation" novalidate>
                <!-- Honeypot para bots + serviço selecionado preservado nos envios -->
                <input type="text" name="website" class="contact-hp" tabindex="-1" autocomplete="off">
                <input type="hidden" name="service_topic" value="<?= esc($serviceTopic) ?>">
                <!-- Campos que alimentam o banco, e-mail e mensagem automática -->
                <div class="row g-3">
                  <div class="col-sm-6">
                    <label for="first_name" class="form-label">Primeiro nome</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required value="<?= esc($first_name ?? '') ?>">
                  </div>
                  <div class="col-sm-6">
                    <label for="last_name" class="form-label">Sobrenome</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required value="<?= esc($last_name ?? '') ?>">
                  </div>
                  <div class="col-sm-6">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="(15) 9 9999-9999" required value="<?= esc($phone ?? '') ?>">
                  </div>
                  <div class="col-sm-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?= esc($email ?? '') ?>">
                  </div>
                  <div class="col-12">
                    <label for="message" class="form-label">Conte sobre sua necessidade</label>
                    <textarea class="form-control" id="message" name="message" rows="5" maxlength="2000" required><?= esc($message ?? '') ?></textarea>
                    <div class="form-text">Até 2000 caracteres.</div>
                  </div>
                  <div class="col-12 d-flex flex-wrap gap-2">
                    <!-- Ações principais: envio por e-mail (Gmail) ou via WhatsApp -->
                    <button type="submit" class="btn btn-gmail d-inline-flex align-items-center">
                      Enviar <i class="bi bi-envelope-fill ms-2"></i>
                    </button>
                    <a
                      href="<?= esc(wa_link($waNumberSetting, $waPrefillMessage)) ?>"
                      class="btn btn-whatsapp-submit d-inline-flex align-items-center"
                      data-wa-button
                      data-wa-number="<?= esc($waDigits) ?>"
                      data-service-topic="<?= esc($serviceTopic) ?>"
                      target="_blank"
                      rel="noopener"
                    >
                      Enviar <i class="bi bi-whatsapp ms-2"></i>
                    </a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
  <?php require __DIR__ . '/partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/main.js"></script>
</body>
</html>
