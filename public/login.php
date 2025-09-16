<?php // Esqueleto de Login (funcional simples para acessar admin) 
session_start();
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['usuario'] ?? '';
  $pass = $_POST['senha'] ?? '';
  if ($user === 'admin' && $pass === 'admin') {
    $_SESSION['admin'] = true;
    header('Location: ../admin/dashboard.php');
    exit;
  } else {
    $erro = 'Usuário ou senha inválidos.';
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="./css/styles.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h1 class="h4 mb-3 text-center">Admin</h1>
            <?php if ($erro): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>
            <form method="post">
              <div class="mb-3">
                <label class="form-label">Usuário</label>
                <input type="text" name="usuario" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
              </div>
              <button class="btn btn-primary w-100">Entrar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
