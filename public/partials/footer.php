<?php
// Footer parcial (ligado a settings + JSON-LD)
$SETTINGS = [];
try {
  $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
  $dbName = getenv('DB_NAME') ?: 'tcc_topografia';
  $dbUser = getenv('DB_USER') ?: 'root';
  $dbPass = getenv('DB_PASS') ?: '';
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $stmt = $pdo->query("SELECT `key`,`value` FROM `settings`");
  foreach ($stmt as $row) {
    $SETTINGS[$row['key']] = $row['value'];
  }
} catch (Throwable $e) {
  // silencioso: usa fallbacks
}

function setting(string $key, string $default = ''): string {
  global $SETTINGS;
  return (isset($SETTINGS[$key]) && $SETTINGS[$key] !== '') ? $SETTINGS[$key] : $default;
}
function esc(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function wa_link(string $number, string $message): string {
  $digits = preg_replace('/\D+/', '', $number);
  if (!$digits) return '#';
  return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message);
}

// Valores vindos do settings (com fallback seguro)
$company      = setting('company_name', 'Topografia');
$trtLabel     = setting('trt_label', 'TRT');
$trtNumber    = setting('trt_number', '000000-1');
$waNumber     = setting('whatsapp_number', '+5515981194365');
$waMsg        = setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento');
$email        = setting('email', 'alfatopst@gmail.com');
$instaUrl     = setting('instagram_url', 'https://instagram.com/alessandro.topografia');
$instaHandle  = setting('instagram_handle', '@alessandro.topografia');
$fbUrl        = setting('facebook_url', 'https://www.facebook.com/share/1BFWR7WdN3/');
$regions      = setting('regions_text', 'Sorocaba e região');
$hours        = setting('hours_text', 'Seg–Sex, 8h–18h');

$waHref = wa_link($waNumber, $waMsg);

// JSON-LD (LocalBusiness/ProfessionalService) no footer
$scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $scheme . '://' . $host;
$logoUrl = $baseUrl . '/img/header-logo.png';

$jsonLd = [
  '@context' => 'https://schema.org',
  '@type' => 'ProfessionalService',
  'name' => $company,
  'url' => $baseUrl,
  'image' => $logoUrl,
  'telephone' => $waNumber,
  'areaServed' => $regions,
  // Mapeado do seu horário (Seg–Sex, 8h–17h)
  'openingHours' => ['Mo-Fr 08:00-17:00'],
  'sameAs' => array_values(array_filter([$fbUrl, $instaUrl])),
  'contactPoint' => [
    '@type' => 'ContactPoint',
    'contactType' => 'customer service',
    'email' => $email,
    'telephone' => $waNumber,
    'availableLanguage' => ['Portuguese'],
  ],
];
?>
<footer class="site-footer mt-5" role="contentinfo">
  <div class="container-fluid">
    <div class="footer-box p-4 p-md-5">
      <div class="row g-4 align-items-start">
        <div class="col-lg-7">
          <h2 class="h5 fw-bold mb-3"><?php echo esc($company); ?></h2>
          <p class="mb-3">
            Levantamentos e medições precisas para projetos, regularizações e obras.
            Atendimento ágil, com responsabilidade técnica (<?php echo esc($trtLabel); ?>) e foco em resultado.
          </p>
          <ul class="mb-0 small">
            <li>Levantamento com equipamentos de última geração, tecnologia e alta precisão</li>
            <li>Entrega de planta e relatório</li>
            <li>Suporte até a aprovação</li>
          </ul>
        </div>

        <div class="col-lg-5">
          <div class="row g-3">
            <div class="col-6">
              <a class="contact-pill" href="<?php echo esc($fbUrl); ?>" target="_blank" rel="noopener" aria-label="Abrir Facebook">
                <span class="icon" aria-hidden="true">F</span>
                <span>ALFA TOP - Serviços topográficos</span>
              </a>
            </div>
            <div class="col-6">
              <a class="contact-pill" href="<?php echo esc($instaUrl); ?>" target="_blank" rel="noopener" aria-label="Abrir Instagram">
                <span class="icon" aria-hidden="true">IG</span>
                <span><?php echo esc($instaHandle); ?></span>
              </a>
            </div>
            <div class="col-6">
              <a class="contact-pill" href="<?php echo esc($waHref); ?>" target="_blank" rel="noopener" aria-label="Abrir WhatsApp">
                <span class="icon" aria-hidden="true">WPP</span>
                <span><?php echo esc($waNumber); ?></span>
              </a>
            </div>
            <div class="col-6">
              <a class="contact-pill" href="mailto:<?php echo esc($email); ?>?subject=<?php echo rawurlencode('Orçamento Topografia'); ?>" aria-label="Enviar e-mail">
                <span class="icon" aria-hidden="true">@</span>
                <span><?php echo esc($email); ?></span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <hr class="my-4 opacity-75">

      <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-start align-items-md-center">
        <div class="small">
          <strong><?php echo esc($trtLabel); ?>:</strong> <?php echo esc($trtNumber ?: '—'); ?> |
          <strong>Atendimento:</strong> <?php echo esc($hours); ?> |
          <strong>Regiões:</strong> <?php echo esc($regions); ?>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-dark btn-sm" href="/contato.php">Pedir orçamento</a>
          <a class="btn btn-outline-dark btn-sm" href="<?php echo esc($waHref); ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
        </div>
      </div>

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-3 small">
        <span>© <span id="year"></span> <?php echo esc($company); ?>. Todos os direitos reservados.</span>
        <div class="d-flex gap-3">
          <a href="/politica-privacidade.php" class="link-dark text-decoration-underline">Política de Privacidade</a>
          <a href="/termos.php" class="link-dark text-decoration-underline">Termos de Uso</a>
          <a href="#" class="link-dark text-decoration-underline" onclick="window.scrollTo({top:0,behavior:'smooth'})">Voltar ao topo</a>
        </div>
      </div>
    </div>
  </div>
</footer>
<script>
  document.getElementById('year')?.append(new Date().getFullYear());
</script>
<script type="application/ld+json">
<?php echo json_encode($jsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
</script>
