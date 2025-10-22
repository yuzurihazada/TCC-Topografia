<?php
require __DIR__ . '/admin/config.php';

// Handler do formulário (no topo para organização)
$errors = [];
$success = false;
$emailWarning = false;
$waLink = null;

$companyName = setting('company_name', 'Topografia');
$defaultEmail = app_env('GMAIL_SENDER') ?: 'alessandrosilva.topografia@gmail.com';
$primaryEmail = setting('email', $defaultEmail);
$waNumberSetting = setting('whatsapp_number', '+5515981194365');
$waDefaultMessage = setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento');
$hoursText = setting('hours_text', 'Seg–Sex, 8h–17h');
$regionsText = setting('regions_text', 'Tatuí e região');
$waDialLink = '+' . preg_replace('/\D+/', '', $waNumberSetting);

$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$email      = trim($_POST['email'] ?? '');
$message    = trim($_POST['message'] ?? '');
$honeypot   = trim($_POST['website'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($honeypot !== '') {
    $errors[] = 'Não foi possível enviar o formulário. Tente novamente.';
  }
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
      $stmt->execute([$first_name, $last_name, $phone, $email, $message]);

      $waMsg = $waDefaultMessage
        . "\n\nNome: {$first_name} {$last_name}"
        . "\nTelefone: {$phone}"
        . "\nE-mail: {$email}"
        . "\nDescrição: {$message}";
      $waLink = wa_link($waNumberSetting, $waMsg);

      $emailBody = '<h2>Novo contato pelo site</h2>'
        . '<p><strong>Nome:</strong> ' . esc($first_name . ' ' . $last_name) . '</p>'
        . '<p><strong>Telefone:</strong> ' . esc($phone) . '</p>'
        . '<p><strong>E-mail:</strong> ' . esc($email) . '</p>'
        . '<p><strong>Mensagem:</strong><br>' . nl2br(esc($message)) . '</p>';

      $mailSent = gmail_send_message($primaryEmail, 'Novo contato - ' . $first_name . ' ' . $last_name, $emailBody, $companyName);
      if (!$mailSent) {
        $emailWarning = true;
      }

      $success = true;
      $first_name = $last_name = $phone = $email = $message = '';
    } catch (Throwable $e) {
      $errors[] = 'Erro ao salvar seu contato. Tente novamente.';
    }
  }
}
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
            <div class="contact-pane contact-pane--info">
              <span class="badge rounded-pill text-bg-success mb-3">Fale conosco</span>
              <h1 class="display-6 fw-semibold mb-3">Vamos conversar sobre seu projeto?</h1>
              <p class="text-muted mb-4">Envie seus dados e conte um pouco da sua necessidade. Respondemos rápido e, se preferir, você pode continuar o atendimento diretamente no WhatsApp.</p>

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

              <div class="contact-highlight">
                <span class="fw-semibold">Preferência?</span>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <a class="btn btn-frame" target="_blank" rel="noopener" href="<?= esc(wa_link($waNumberSetting, $waDefaultMessage)) ?>">Chamar no WhatsApp</a>
                  <a class="btn btn-frame-outline" href="tel:<?= esc($waDialLink) ?>">Ligar agora</a>
                </div>
              </div>
            </div>

            <div class="contact-pane contact-pane--form">
              <h2 class="h4 mb-3 text-center text-md-start">Envie uma mensagem</h2>

              <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Corrija os campos:</strong>
                  <ul class="mb-0">
                    <?php foreach ($errors as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <?php if (!empty($success)): ?>
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
                <input type="text" name="website" class="contact-hp" tabindex="-1" autocomplete="off">
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
                    <button type="submit" class="btn btn-primary">Enviar mensagem</button>
                    <a class="btn btn-outline-success" target="_blank" rel="noopener" href="<?= esc(($waLink ?? '') ?: wa_link($waNumberSetting, $waDefaultMessage)) ?>">Falar no WhatsApp</a>
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
