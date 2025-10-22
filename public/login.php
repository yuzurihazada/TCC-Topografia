<?php
// Login com SQLite e password_hash
putenv('DB_DRIVER=sqlite');
require __DIR__ . '/admin/config.php';

if (!empty($_SESSION['admin'])) {
  header('Location: /admin/dashboard.php');
  exit;
}

$companyName = setting('company_name', 'Topografia');
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = trim($_POST['usuario'] ?? '');
  $pass = $_POST['senha'] ?? '';
  try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$user]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && password_verify($pass, $row['password_hash'])) {
      if (function_exists('session_regenerate_id')) session_regenerate_id(true);
      $_SESSION['admin'] = (int)$row['id'];
      header('Location: /admin/dashboard.php');
      exit;
    }
    $erro = 'Usuário ou senha inválidos.';
  } catch (Throwable $e) {
    $erro = 'Erro de autenticação. Tente novamente.';
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body class="auth-page">
  <div class="auth-shell">
    <div class="auth-panel auth-panel--brand">
      <div class="auth-panel__content">
        <div>
          <span class="badge rounded-pill text-bg-success">Área administrativa</span>
          <h1 class="display-6 fw-semibold mt-3 mb-2"><?= esc($companyName) ?></h1>
          <p class="text-light opacity-75 mb-4">Painel para gerenciar serviços, perguntas frequentes, feedbacks e contatos recebidos. Acesso restrito a administradores.</p>
        </div>
        <ul class="auth-highlights">
          <li><i class="bi bi-lock-fill"></i> Sessões protegidas e renovadas após login.</li>
          <li><i class="bi bi-shield-check"></i> Senhas armazenadas com criptografia forte.</li>
          <li><i class="bi bi-speedometer"></i> Conteúdo atualizado em poucos cliques.</li>
        </ul>
        <div class="auth-panel__footer">
          <a class="btn btn-outline-light" href="/" rel="noopener">Voltar ao site</a>
        </div>
      </div>
    </div>

    <div class="auth-panel auth-panel--form">
      <div class="auth-panel__content">
        <div class="auth-card shadow-lg">
          <h2 class="h4 mb-3 text-center text-md-start">Entrar no painel</h2>
          <?php if ($erro): ?>
            <div class="alert alert-danger" role="alert"><?= esc($erro) ?></div>
          <?php endif; ?>
          <form method="post" novalidate class="auth-form">
            <div class="mb-3">
              <label class="form-label" for="usuario">Usuário</label>
              <input type="text" id="usuario" name="usuario" class="form-control" required autocomplete="username" autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label" for="senha">Senha</label>
              <input type="password" id="senha" name="senha" class="form-control" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
            <p class="text-muted small mt-3 mb-0 text-center">Dica inicial: admin / admin</p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
