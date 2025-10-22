<?php
// Configuração central do projeto (PDO + helpers compartilhados)

declare(strict_types=1);

// Inicializa a sessão com parâmetros seguros sempre que o arquivo for carregado.
if (session_status() === PHP_SESSION_NONE) {
  $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  $cookieDomain = $_SERVER['HTTP_HOST'] ?? '';
  if (strpos($cookieDomain, ':') !== false) {
    $cookieDomain = explode(':', $cookieDomain)[0];
  }
  if ($cookieDomain === 'localhost' || filter_var($cookieDomain, FILTER_VALIDATE_IP)) {
    $cookieDomain = '';
  }

  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $cookieDomain,
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);

  session_start();
}

// Carrega rotina que garante a existência do banco SQLite e seeds iniciais.
require_once __DIR__ . '/../../database/init_sqlite.php';

// Helpers utilitários compartilhados entre todo o site público/admin.
if (!function_exists('esc')) {
  function esc(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('wa_link')) {
  // Normaliza o número e gera o link oficial da API do WhatsApp com texto pré-preenchido.
  function wa_link(string $number, string $message): string {
    $digits = preg_replace('/\D+/', '', $number);
    if ($digits === '') {
      return '#';
    }

    $encoded = rawurlencode($message);
    return 'https://api.whatsapp.com/send?phone=' . $digits . '&text=' . $encoded;
  }
}

if (!function_exists('app_env')) {
  // Acessa variáveis de ambiente com fallback amigável.
  function app_env(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    return $value === false ? $default : $value;
  }
}

// Funções ligadas à integração com a API do Gmail (cache, logs e refresh de token).
if (!function_exists('gmail_token_cache_path')) {
  // Define onde o token OAuth fica armazenado no disco.
  function gmail_token_cache_path(): string {
    $base = app_env('GMAIL_TOKEN_CACHE') ?: (__DIR__ . '/../../storage/gmail_token.json');
    $directory = dirname($base);
    if (!is_dir($directory)) {
      mkdir($directory, 0775, true);
    }
    return $base;
  }
}

if (!function_exists('gmail_log_message')) {
  // Escreve logs da integração Gmail para facilitar depuração.
  function gmail_log_message(string $message): void {
    $logFile = app_env('GMAIL_LOG_FILE') ?: (__DIR__ . '/../../storage/gmail_debug.log');
    $directory = dirname($logFile);
    if (!is_dir($directory)) {
      mkdir($directory, 0775, true);
    }
    $line = '[' . date('c') . '] ' . $message . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND);
  }
}

if (!function_exists('gmail_fetch_access_token')) {
  // Obtém e reutiliza o token de acesso da API Gmail com cache em disco.
  function gmail_fetch_access_token(): ?string {
    static $token = null;
    static $expiresAt = 0;

    // Reaproveita token ainda válido em memória para evitar roundtrips.
    if ($token && $expiresAt > (time() + 60)) {
      return $token;
    }

    $cachePath = gmail_token_cache_path();
    // Prioriza o token salvo em arquivo, evitando chamadas repetidas ao Google.
    if (is_file($cachePath)) {
      $cached = json_decode((string)file_get_contents($cachePath), true);
      if (is_array($cached) && !empty($cached['access_token']) && !empty($cached['expires_at'])) {
        if ((int)$cached['expires_at'] > (time() + 60)) {
          $token = (string)$cached['access_token'];
          $expiresAt = (int)$cached['expires_at'];
          return $token;
        }
      }
    }

    $clientId = app_env('GMAIL_CLIENT_ID');
    $clientSecret = app_env('GMAIL_CLIENT_SECRET');
    $refreshToken = app_env('GMAIL_REFRESH_TOKEN');

    // Caso todas as credenciais estejam presentes, solicita um novo token.
    if ($clientId && $clientSecret && $refreshToken) {
      $postFields = http_build_query([
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token',
      ], '', '&', PHP_QUERY_RFC3986);

      $ch = curl_init('https://oauth2.googleapis.com/token');
      curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 15,
      ]);

      $response = curl_exec($ch);
      $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        error_log('Gmail token refresh failed: HTTP ' . $httpCode . ' Response: ' . ($response ?: curl_error($ch)));
        gmail_log_message('Token refresh failed: HTTP ' . $httpCode . ' Response: ' . ($response ?: curl_error($ch)));
        curl_close($ch);
      } else {
        curl_close($ch);
        $data = json_decode($response, true);
        if (is_array($data) && !empty($data['access_token']) && !empty($data['expires_in'])) {
          $token = (string)$data['access_token'];
          $expiresAt = time() + (int)$data['expires_in'];
          file_put_contents($cachePath, json_encode([
            'access_token' => $token,
            'expires_at' => $expiresAt,
          ], JSON_PRETTY_PRINT));
          gmail_log_message('Token refresh ok. Expires at ' . date('c', $expiresAt));
          return $token;
        }
        gmail_log_message('Token refresh unexpected response: ' . $response);
      }
    }

    $envToken = app_env('GMAIL_API_TOKEN');
    if ($envToken) {
      $token = (string)$envToken;
      $expiresAt = time() + 300;
      gmail_log_message('Using GMAIL_API_TOKEN fallback (expires simulated in 5 min).');
      return $token;
    }

    return null;
  }
}

/**
 * Retorna instância PDO compartilhada.
 * Usa SQLite por padrão e MySQL quando indicado via variáveis de ambiente.
 */
if (!function_exists('db')) {
  function db(): PDO {
    static $pdo = null;

    // Retorna a mesma conexão durante toda a requisição para economizar recursos.
    if ($pdo instanceof PDO) {
      return $pdo;
    }

    $driver = strtolower((string) (app_env('DB_DRIVER') ?: 'sqlite'));

    if ($driver === 'mysql') {
      // Configuração prioritária: conecta no MySQL quando informado via ambiente.
      $host = app_env('DB_HOST', '127.0.0.1') ?: '127.0.0.1';
      $name = app_env('DB_NAME', 'tcc_topografia') ?: 'tcc_topografia';
      $user = app_env('DB_USER', 'root') ?: 'root';
      $pass = app_env('DB_PASS', '');
      $dsn  = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);

      try {
        $pdo = new PDO($dsn, $user, $pass, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
      } catch (Throwable $mysqlError) {
        // Fallback automático para SQLite caso a conexão MySQL falhe
      }
    }

    // Caminho do banco SQLite que funciona por padrão em qualquer ambiente.
  $sqlitePath = app_env('SQLITE_PATH');
    if (!$sqlitePath) {
      $sqlitePath = realpath(__DIR__ . '/../../database') ?: (__DIR__ . '/../../database');
      $sqlitePath .= DIRECTORY_SEPARATOR . 'site.sqlite';
    }

    $directory = dirname($sqlitePath);
    if (!is_dir($directory)) {
      mkdir($directory, 0775, true);
    }

    $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Garante que as tabelas existam e estejam na versão atual.
    init_sqlite_schema($pdo);

    return $pdo;
  }
}

// Recupera configurações armazenadas no banco com cache simples em memória.
if (!function_exists('setting')) {
  function setting(string $key, string $default = ''): string {
    static $cache = null;

    if ($cache === null) {
      $cache = [];
      try {
        $stmt = db()->query('SELECT key, value FROM settings');
        foreach ($stmt as $row) {
          $cache[$row['key']] = (string) $row['value'];
        }
      } catch (Throwable $e) {
        // Mantém cache vazio e usa default
      }
    }

    return $cache[$key] ?? $default;
  }
}

// Auxiliares de autenticação usados pelo painel administrativo.
if (!function_exists('is_admin_authenticated')) {
  function is_admin_authenticated(): bool {
    return !empty($_SESSION['admin']);
  }
}

if (!function_exists('require_admin')) {
  function require_admin(): void {
    if (!is_admin_authenticated()) {
      header('Location: /login.php');
      exit;
    }
  }
}

if (!function_exists('logout_admin')) {
  // Limpa sessão e cookies para encerrar o login administrativo.
  function logout_admin(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
  }
}

// Envia e-mails usando a API Gmail autenticada com OAuth.
if (!function_exists('gmail_send_message')) {
  function gmail_send_message(string $toEmail, string $subject, string $htmlBody, string $fromName = 'Site Topografia'): bool {
    $accessToken = gmail_fetch_access_token();
    $senderEmail = app_env('GMAIL_SENDER') ?: $toEmail;

    // Sem token válido ou remetente configurado não é possível disparar o e-mail.
    if (!$accessToken || !$senderEmail) {
      return false;
    }

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    // Monta o e-mail completo conforme RFC822 para o endpoint do Gmail.
    $rawMessage = implode("\r\n", [
      'From: ' . $fromName . ' <' . $senderEmail . '>',
      'To: ' . $toEmail,
      'Subject: ' . $encodedSubject,
      'MIME-Version: 1.0',
      'Content-Type: text/html; charset=UTF-8',
      '',
      $htmlBody,
    ]);

    // Gmail exige base64 URL-safe para aceitar o payload.
    $base64 = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');

    $payload = json_encode(['raw' => $base64]);
    if ($payload === false) {
      return false;
    }

    $endpointUser = app_env('GMAIL_USER', 'me') ?: 'me';
    $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/' . $endpointUser . '/messages/send');
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
      ],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $payload,
      CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
      error_log('Gmail API send failed: HTTP ' . $httpCode . ' Response: ' . ($response ?: curl_error($ch)));
      gmail_log_message('Send failed for ' . $toEmail . ' | HTTP ' . $httpCode . ' | Response: ' . ($response ?: curl_error($ch)));
      curl_close($ch);
      return false;
    }

    curl_close($ch);
    $decoded = json_decode((string)$response, true);
    // Registra em log o ID retornado pelo Gmail para rastreabilidade.
    if (is_array($decoded) && !empty($decoded['id'])) {
      gmail_log_message('Send ok for ' . $toEmail . ' | Gmail ID ' . $decoded['id']);
    } else {
      gmail_log_message('Send ok for ' . $toEmail . ' | Unexpected response: ' . $response);
    }
    return true;
  }
}