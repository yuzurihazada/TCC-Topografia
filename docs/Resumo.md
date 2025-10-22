# Resumão vivo do projeto

## Visão geral
- **Stack**: PHP 8.4 + PDO, SQLite como banco padrão, Bootstrap 5.3 + Bootstrap Icons, JavaScript vanilla e CSS custom (`styles.css` + `admin.css`).
- **Layout**: paleta verde proprietária, fundo suave global (#F4F8F5), botões `.btn-frame` / `.btn-frame-outline`, cartões com sombras leves, ícones oficiais.
- **Estrutura**: `public/` (site e admin), `public/partials/`, `public/css/`, `database/` (SQLite + seeds), `docs/` (documentação).

## Banco e segurança
- **Esquema**: `servicos`, `faqs`, `feedbacks`, `contacts`, `settings`, `users` (mais índices básicos). Tudo criado/populado automaticamente por `database/init_sqlite.php`.
- **Autenticação**: `login.php` usa `password_hash`, `password_verify` e `session_regenerate_id`; áreas protegidas chamam `require_admin()` de `config.php`.
- **Configurações**: valores como nome da empresa, WhatsApp e e-mail vêm de `settings`; fallback atualizado para “Alessandro Silva - Topografia e Projetos”.

## Páginas públicas
- **Home (`index.php`)**: herói com CTAs, diferenciais, processo, vitrine de serviços e carrossel de feedbacks.
- **Serviços (`servicos.php`)**: lista ordenada via `order_index`, preço opcional, descrição rica em tópicos, CTAs para WhatsApp/e-mail.
- **FAQ (`faq.php`)**: acordeão estilizado com chevron rotativo e estados de hover, usando dados da tabela `faqs`.
- **Sobre (`sobre.php`)**: perfil do Alessandro, equipamentos, certificações e CTAs alinhadas.
- **Contato (`contato.php`)**: formulário validado, gravação no SQLite, geração de link de WhatsApp e envio automático por Gmail API (via `gmail_send_message`).
- **Parciais**: barra de navegação uniforme e rodapé com CTA dupla, redes sociais (ícones) e JSON-LD `ProfessionalService`.

## Administração
- **Dashboard**: cartões com contadores dinâmicos (serviços, FAQ, feedbacks) e quick-actions.
- **Serviços/FAQ/Feedbacks**: CRUD completo com busca, ordenação, toggles de status, modais de visualização/edição, botões alinhados ao novo tema.
- **Tema**: `public/css/admin.css` centraliza topbar, tabelas, estatísticas, modais e regras responsivas; páginas herdaram o visual do site público.
- **Gmail integration**: `config.php` agora busca tokens via refresh OAuth, gera cache em `storage/gmail_token.json` e faz logging de diagnóstico em `storage/gmail_debug.log` (diretório já ignorado no Git).

## Convenções de código
- Helpers principais: `db()`, `setting()`, `esc()`, `wa_link()`, `gmail_fetch_access_token()`, `gmail_send_message()`, `require_admin()`.
- CSS global: variáveis `--color-primary`, `--color-accent`, `--gradient-*` e componentes padronizados.
- Responsividade: ajustes recentes para empilhar cards, reorganizar toolbars e melhorar leitura em mobile.

## Estado atual
- PHP lint em todos os arquivos críticos rodou sem erros.
- Formulário de contato envia e-mail via Gmail API (token válido da conta `alessandrosilva.topografia@gmail.com`).
- CRUDs administrativos testados após refatoração visual; buscas e modais operacionais.
- Repositório sincronizado com commit `feat: refresh site styling and Gmail integration`.

## Próximos passos mapeados
1. Introduzir carregamento `.env` (provavelmente com `vlucas/phpdotenv`) para centralizar credenciais e URLs. Criar `.env.example`.
2. (Opcional) Tela administrativa para contatos (`contacts`) e painel de configurações (`settings`).
3. Paginação simples no Admin para listas longas e possíveis filtros adicionais.
4. Refino visual do FAQ público (chevron tamanho, foco/hover em mobile).
5. Revisão SEO/A11Y: metas OG, heading hierarchy, testes Lighthouse.