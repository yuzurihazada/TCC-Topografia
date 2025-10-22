<?php
// Inicializa schema e dados seed para SQLite (rodado automaticamente no primeiro uso)
function init_sqlite_schema(PDO $pdo): void {
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS servicos (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      titulo TEXT NOT NULL,
      descricao TEXT NOT NULL,
      preco REAL NULL,
      order_index INTEGER NOT NULL DEFAULT 0,
      active INTEGER NOT NULL DEFAULT 1,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS faqs (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      pergunta TEXT NOT NULL,
      resposta TEXT NOT NULL,
      order_index INTEGER NOT NULL DEFAULT 0,
      active INTEGER NOT NULL DEFAULT 1,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS feedbacks (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL,
      email TEXT NULL,
      mensagem TEXT NULL,
      active INTEGER NOT NULL DEFAULT 1,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS contacts (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      first_name TEXT NOT NULL,
      last_name  TEXT NOT NULL,
      phone      TEXT NOT NULL,
      email      TEXT NOT NULL,
      message    TEXT NOT NULL,
      source     TEXT NULL,
      status     TEXT NOT NULL DEFAULT 'new',
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS settings (
      key TEXT PRIMARY KEY,
      value TEXT
    );
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT NOT NULL UNIQUE,
      password_hash TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );
    CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contacts (created_at);
  ");

  $hasServ = (int)$pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
  $hasFaq  = (int)$pdo->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
  $hasSet  = (int)$pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
  $hasUser = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

  $pdo->beginTransaction();
  try {
    if ($hasServ === 0) {
      $services = [
        ['Levantamentos Topográficos', implode("\n", [
          'Levantamento planialtimétrico (plano + relevo)',
          'Levantamento planimétrico (somente planta)',
          'Levantamento altimétrico (somente relevo)',
          'Levantamento cadastral urbano ou rural',
          'Levantamento para cálculo de área',
          'Levantamento para retificação de área',
          'Levantamento para desmembramento ou remembramento',
          'Levantamento para regularização fundiária (urbana ou rural)',
        ]), NULL, 1, 1],
        ['Georreferenciamento e Precisão', implode("\n", [
          'Georreferenciamento de imóveis rurais (INCRA - SIGEF)',
          'Georreferenciamento de imóveis urbanos',
          'Posicionamento com GNSS RTK/PPK',
          'Apoio topográfico para imagens de drone',
          'Implantação de marcos e vértices georreferenciados',
          'Apoio à certificação cartorial e registros públicos',
        ]), NULL, 2, 1],
        ['Infraestrutura e Projetos', implode("\n", [
          'Apoio topográfico para obras de terraplenagem',
          'Locação de rodovias, ruas e avenidas',
          'Locação de loteamentos e condomínios',
          'Locação de galerias, redes de água, esgoto, drenagem',
          'Implantação de platôs, cortes e aterros',
          'Cálculo de volumes de corte/aterro (cubagem) - Monitoramento de taludes e recalques',
        ]), NULL, 3, 1],
        ['Construção Civil', implode("\n", [
          'Locação de fundações, pilares e estruturas',
          'Controle geométrico de obras (as built)',
          'Nivelamento e conferência de prumo/alinhamento',
          'Levantamento de fachadas e elementos arquitetônicos',
        ]), NULL, 4, 1],
      ];
      $stmt = $pdo->prepare("INSERT INTO servicos (titulo, descricao, preco, order_index, active) VALUES (:t, :d, :p, :o, :a)");
      foreach ($services as [$t, $d, $p, $o, $a]) {
        $stmt->execute([':t'=>$t, ':d'=>$d, ':p'=>$p, ':o'=>$o, ':a'=>$a]);
      }
    }

    if ($hasFaq === 0) {
      $pdo->exec("INSERT INTO faqs (pergunta, resposta, order_index, active) VALUES
        ('O que é topografia?', 'É a ciência que descreve e representa as características do terreno...', 1, 1),
        ('Para que serve a topografia?', 'Auxilia no planejamento de obras, projetos agrícolas, loteamentos...', 2, 1),
        ('Qual a diferença entre topografia e geodésia?', 'Geodésia estuda a forma e dimensões da Terra; topografia foca em áreas menores...', 3, 1),
        ('Quais são os principais tipos de levantamento?', 'Planialtimétrico, cadastral, georreferenciamento, locação de obra...', 4, 1),
        ('Topografia é obrigatória para construir?', 'Em muitos casos sim: define limites, cotas, volumes e reduz erros executivos.', 5, 1)
      ");
    }

    if ($hasSet === 0) {
      $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
      foreach ([
  ['company_name', 'Alessandro Silva - Topografia e Projetos'],
        ['trt_label', 'TRT'],
        ['trt_number', ''],
        ['whatsapp_number', '+5515981194365'],
        ['email', 'alessandrosilva.topografia@gmail.com'],
        ['instagram_handle', '@alessandro.topografia'],
        ['instagram_url', 'https://instagram.com/alessandro.topografia'],
        ['facebook_url', 'https://www.facebook.com/share/1BFWR7WdN3/'],
        ['regions_text', 'Atende a aproximadamente um raio de até 100 km de Tatuí'],
        ['hours_text', 'Seg–Sex, 8h–17h'],
        ['cta_whatsapp_message', 'Olá, vim pelo site e gostaria de um orçamento'],
      ] as $kv) { $stmt->execute($kv); }
    }

    if ($hasUser === 0) {
      // Usuário admin padrão com senha hash (senha: admin)
      $hash = password_hash('admin', PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
      $stmt->execute(['admin', $hash]);
    }

    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
}