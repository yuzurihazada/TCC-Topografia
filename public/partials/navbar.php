<?php
$current = basename($_SERVER['PHP_SELF']);
function active($file) {
    global $current;
    return $current === $file ? ' active' : '';
}
?>

<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-light site-nav px-3 py-2">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <img src="/img/header-logo.png" alt="Logomarca" style="height:110px;width:auto;">
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false"
                aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link<?php echo active('index.php'); ?>" href="/index.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo active('servicos.php'); ?>" href="/servicos.php">Serviços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo active('faq.php'); ?>" href="/faq.php">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo active('sobre.php'); ?>" href="/sobre.php">Sobre Mim</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-frame btn-sm fw-semibold px-3" href="/contato.php">Contatar Serviço</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<?php if ($current !== 'index.php'): ?>
  <!-- Linha aparece logo após o header nas páginas internas -->
  <div class="header-divider"></div>
<?php endif; ?>

<?php if ($current === 'index.php'): ?>
<section class="hero-visual">
    <img src="/img/hero-image.jpg" class="hero-img" alt="Equipamento de topografia em campo">
    <div class="hero-overlay">
        <div class="container d-flex align-items-center mt-5">
            <div class="col-12 col-lg-6">
                <h1 class="hero-title mb-3">Precisão e confiança em Topografia</h1>
                <p class="hero-subtitle mb-4">
                    Levantamentos, projetos e soluções técnicas para seu empreendimento.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="/contato.php" class="btn btn-frame">Solicitar Orçamento</a>
                    <a href="/servicos.php" class="btn btn-outline-light d-flex align-items-center">Ver Serviços</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Linha separando HERO do conteúdo principal (somente na Home) -->
<div class="header-divider after-hero"></div>
<?php endif; ?>