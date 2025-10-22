# Resumão vivo do projeto

Visão geral
Stack: PHP 8.x + PDO, SQLite (principal), Bootstrap 5, Bootstrap Icons, JavaScript vanilla, CSS custom (styles.css).
Estrutura: Document root em public; parciais em partials; admin em admin; banco e seeds em database.
Tema visual: Paleta verde própria, com fundo suave global aplicado em todas as páginas (#F4F8F5), botões custom .btn-frame e .btn-frame-outline, cartões com hover, e ícones do Bootstrap.

Banco de dados
Tabelas principais: servicos, faqs, feedbacks, contacts, settings, users.
Inicialização/seed: init_sqlite.php cria e popula configuração básica; usuário admin com senha protegida (password_hash).

Login/admin: login.php usa SQLite e password_verify, com session_regenerate_id, e controle de acesso via require_admin() (em config.php).

Páginas públicas
Home (index.php)
Carrossel de feedbacks com Bootstrap (setas + autoplay).
Seções: diferenciais, processo, teaser de serviços e CTA final.
CTAs: “Chamar no WhatsApp” (btn-frame) e “Enviar mensagem” (btn-frame-outline) com altura/alinhamento iguais.

Serviços (servicos.php)
Lista serviços ativos ordenados por order_index; exibe preço se existir; descrição em bullets; CTA para WhatsApp/e-mail.

FAQ público (faq.php)
Acordeão com cards (estilo v2), cabeçalho com chevron em SVG inline, rotação ao abrir, corpo com transição.
Fundo suave aplicado; ajustes finos em andamento serão retomados depois (ver To‑Dos).

Sobre (sobre.php)
Reformulado para “Sobre mim”: imagem img/sobre.jpg, texto estruturado (experiência, atuação, softwares, equipamentos, formação, certificações), “Como posso ajudar” e “Atendimento”.
CTAs alinhadas (WhatsApp + Enviar mensagem).

Parciais
Navbar/Divider conforme padrão do site.

Footer (footer.php)
Usa helpers setting(), wa_link(), esc(); mantém “Registrado no CRT-SP” e TRT opcional.
Social: trocados quadrados/letras por ícones oficiais (Facebook/Instagram/WhatsApp/E-mail) com Bootstrap Icons.
JSON‑LD de ProfessionalService com telefone, área atendida, horários.

Admin
Dashboard (dashboard.php)
Cartões (Serviços, FAQ, Feedbacks) com ícones e contadores; hover “lift”; fundo suave aplicado.

Feedbacks (feedbacks.php)
CRUD completo: criar/editar/excluir/toggle ativo, “Ver” completo em modal, busca por nome/e‑mail/mensagem.
Ações rápidas: “Popular exemplos” e “Excluir todos”.

Serviços (servicos.php)
CRUD completo: criar/editar/excluir/toggle ativo, ordem (order_index), preço opcional, “Ver” completo, busca por título/descrição.

FAQ (faq.php)
CRUD completo: criar/editar/excluir/toggle ativo, ordem, “Ver” completo, busca por pergunta/resposta.

UX padrão admin:
Tabelas em cards, botões “Voltar”, badges de ativo/inativo, modais fora da tabela (corrige fechamento/stacking), .btn-primary com accent verde + hover.

Decisões de arquitetura
Banco: SQLite como padrão para dev; MySQL opcional (não prioritário).
Segurança: password_hash/password_verify, prepared statements, require_admin nos módulos administrativos.
Design: paleta verde; cards com sombras, hover leves, ícones de Bootstrap; CTAs padronizadas; fundo global suave para conforto visual.

Itens pendentes e próximos passos
FAQ público (refino visual)
Ajustar detalhes do cabeçalho: remover linha tracejada no corpo, polir sombras e “estado aberto”, testar tamanhos do chevron e alvo de clique em mobile, garantir centralização perfeita em todos os breakpoints.

Paginação no Admin
Adicionar paginação simples (page/perPage) nas listas de Feedbacks, Serviços e FAQ mantendo a busca (q). Hoje já temos busca; falta a paginação.

CSS do Admin
Centralizar estilos inline (fundo, botões, cards) em um CSS dedicado (ex.: public/admin/admin.css) para reduzir duplicação.

Contatos no Admin (opcional)
Criar uma tela de listagem/visualização para contacts (já existe a tabela e a página pública).

Configurações (opcional)
Página no admin para editar settings (nome, WhatsApp, horários, redes, TRT etc.), hoje lidos via helper.

SEO/A11Y (quando oportuno)
Metadados, titles/OG, headings consistentes, contrastes, foco/aria refinados (boa parte já iniciada).

Convenções úteis
Helpers (em config.php):
db(), setting($key, $default), esc($text), wa_link($number, $message), require_admin().
Botões e estilos:
Principal: .btn-frame; Outline: .btn-frame-outline; Admin .btn-primary custom com hover.
Paleta (em styles.css):
--color-primary/--color-primary-dark/--color-primary-light; --color-accent/--color-accent-hover.
Fundo global: #F4F8F5 (agora aplicado em html/body).

Estado atual confiável
Build/lint: sem erros de sintaxe PHP nos arquivos alterados.
Login: OK com SQLite + hash.
CRUDs: Feedbacks, Serviços e FAQ completos; busca funcionando; “Ver completo” em modais.