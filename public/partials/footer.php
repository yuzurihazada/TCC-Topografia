<?php
// Footer parcial (usa helpers/setting() se já existirem, sem abrir conexão própria)
// Se setting() não existir (página estática), define fallbacks simples.
if (!function_exists('setting')) {
  $FOOTER_DEFAULTS = [
    'company_name' => 'Topografia',
    'whatsapp_number' => '+5515981194365',
    'cta_whatsapp_message' => 'Olá, vim pelo site e gostaria de um orçamento',
    'email' => 'alfatopst@gmail.com',
    'instagram_url' => 'https://instagram.com/alessandro.topografia',
    'instagram_handle' => '@alessandro.topografia',
    'facebook_url' => 'https://www.facebook.com/share/1BFWR7WdN3/',
    'regions_text' => 'Atendimento em Tatuí e região',
    'hours_text' => 'Seg–Sex, 8h–17h',
    'trt_label' => 'TRT',
    'trt_number' => '',
  ];
  function setting(string $key, string $default = ''): string {
    global $FOOTER_DEFAULTS;
    return $FOOTER_DEFAULTS[$key] ?? $default;
  }
}

if (!function_exists('esc')) {
  function esc(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('wa_link')) {
  function wa_link(string $number, string $message): string {
    $digits = preg_replace('/\D+/', '', $number);
    if ($digits === '') {
      return '#';
    }

    $encoded = rawurlencode($message);
    return 'https://api.whatsapp.com/send?phone=' . $digits . '&text=' . $encoded;
  }
}

// Valores vindos do settings (com fallback seguro)
$company      = setting('company_name', 'Topografia');
$waNumber     = setting('whatsapp_number', '+5515981194365');
$waMsg        = setting('cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento');
$email        = setting('email', 'alfatopst@gmail.com');
$instaUrl     = setting('instagram_url', 'https://instagram.com/alessandro.topografia');
$instaHandle  = setting('instagram_handle', '@alessandro.topografia');
$fbUrl        = setting('facebook_url', 'https://www.facebook.com/share/1BFWR7WdN3/');
$regions      = setting('regions_text', 'Sorocaba e região');
$hours        = setting('hours_text', 'Seg–Sex, 8h–17h');
$trtLabel     = setting('trt_label', 'TRT');
$trtNumber    = setting('trt_number', '');

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
<!-- Ícones (Bootstrap Icons) para redes sociais no footer -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<footer class="site-footer" role="contentinfo">
  <!-- Linha superior do footer (agora dentro do footer para padronizar) -->
  <div class="footer-divider"></div>

  <!-- Largura total sem container lateral “sobrando” -->
  <div class="footer-box footer-full p-4 p-md-5">
    <div class="row g-4 align-items-start">
      <div class="col-lg-7">
        <h2 class="h5 fw-bold mb-3 text-light"><?php echo esc($company); ?></h2>
        <p class="mb-3 text-light">
          Levantamentos e medições precisas para projetos, regularizações e obras.
          Atendimento ágil, com responsabilidade técnica e foco em resultado.
        </p>
        <ul class="mb-0 small text-light">
          <li>Levantamento com equipamentos de última geração, tecnologia e alta precisão</li>
          <li>Entrega de planta e relatório</li>
          <li>Suporte até a aprovação</li>
        </ul>
      </div>

      <div class="col-lg-5">
        <div class="row g-3">
          <div class="col-6">
            <a class="contact-pill" href="<?php echo esc($fbUrl); ?>" target="_blank" rel="noopener" aria-label="Abrir Facebook">
              <span class="icon" aria-hidden="true"><i class="bi bi-facebook"></i></span>
              <span>Alessandro Silva - Topografia e Projetos</span>
            </a>
          </div>
          <div class="col-6">
            <a class="contact-pill" href="<?php echo esc($instaUrl); ?>" target="_blank" rel="noopener" aria-label="Abrir Instagram">
              <span class="icon" aria-hidden="true"><i class="bi bi-instagram"></i></span>
              <span><?php echo esc($instaHandle); ?></span>
            </a>
          </div>
          <div class="col-6">
            <a class="contact-pill" href="<?php echo esc($waHref); ?>" target="_blank" rel="noopener" aria-label="Abrir WhatsApp">
              <span class="icon" aria-hidden="true"><i class="bi bi-whatsapp"></i></span>
              <span><?php echo esc($waNumber); ?></span>
            </a>
          </div>
          <div class="col-6">
            <a class="contact-pill" href="mailto:<?php echo esc($email); ?>?subject=<?php echo rawurlencode('Orçamento Topografia'); ?>" aria-label="Enviar e-mail">
              <span class="icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
              <span><?php echo esc($email); ?></span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <hr class="my-4 opacity-75">

    <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-start align-items-md-center">
      <div class="small text-light">
        <strong>Registrado no CRT-SP</strong>
        <?php if (!empty($trtNumber)): ?>
          | <strong><?php echo esc($trtLabel); ?>:</strong> <?php echo esc($trtNumber); ?>
        <?php endif; ?>
        | <strong>Atendimento:</strong> <?php echo esc($hours); ?>
        | <strong>Regiões:</strong> <?php echo esc($regions); ?>
      </div>
      <div class="d-flex gap-2">
  <a class="btn btn-outline-light btn-sm footer-cta" href="/contato.php">Pedir orçamento</a>
  <a class="btn btn-frame btn-sm footer-cta" href="<?php echo esc($waHref); ?>" target="_blank" rel="noopener">Chamar no WhatsApp</a>
      </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-3 small text-light">
      <span>© <span id="year"></span> <?php echo esc($company); ?>. Todos os direitos reservados.</span>
      <div class="d-flex gap-3">
        <a href="/politica-privacidade.php" class="link-light text-decoration-underline">Política de Privacidade</a>
        <a href="/termos.php" class="link-light text-decoration-underline">Termos de Uso</a>
        <a href="#" class="link-light text-decoration-underline" onclick="window.scrollTo({top:0,behavior:'smooth'})">Voltar ao topo</a>
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