<?php
$current = basename($_SERVER['PHP_SELF']);
function active($file) {
    global $current;
    return $current === $file ? ' active' : '';
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="/css/styles.css" rel="stylesheet">

<header class="site-header">
    <nav class="navbar navbar-expand-lg bg-white px-3 py-2">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <img src="/img/header-logo.png" alt="Logomarca" style="height:100px;width:auto;">
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

    <?php if ($current === 'index.php'): ?>
    <section class="hero-visual">
        <img src="/img/hero-image.png" class="hero-img" alt="Equipamento de topografia em campo">
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
    <?php endif; ?>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>