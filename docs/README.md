# TCC - Topografia

Projeto acadêmico/profissional em PHP 8 + Bootstrap para divulgar serviços de topografia e administrar conteúdo.

## Stack e recursos
- **Back-end**: PHP 8.4 + PDO com SQLite (padrão) e fallback opcional para MySQL via variáveis de ambiente.
- **Front-end**: Bootstrap 5.3, Bootstrap Icons, CSS custom (`public/css/styles.css`) e tema administrativo dedicado (`public/css/admin.css`).
- **Banco**: `database/init_sqlite.php` cria/atualiza tabelas e popula dados iniciais (serviços, FAQ, feedbacks, usuário admin).
- **Integrações**: envio automático de contatos pela Gmail API (`gmail_send_message`) com tokens OAuth 2.0.

## Estrutura de pastas
- `public/` – site público, formulário de contato, páginas institucionais e login.
- `public/admin/` – dashboard e CRUD de Serviços, FAQ e Feedbacks (proteção por sessão).
- `public/partials/` – navbar, footer e componentes compartilhados.
- `public/css/` – estilos globais e tema administrativo.
- `database/` – banco SQLite (`site.sqlite`) e rotina de seed (`init_sqlite.php`).
- `docs/` – documentação auxiliar (`README.md`, `Resumo.md`).
- `storage/` – cache de tokens e logs da Gmail API (já ignorado no Git).

## Pré-requisitos
- PHP 8.1 ou superior com extensões `pdo_sqlite`, `pdo_mysql`, `curl`, `mbstring`.
- (Opcional) MySQL 5.7+ caso deseje trocar o banco padrão.

## Instalação rápida
1. Clone o repositório e garanta permissões de escrita em `database/` e `storage/`.
2. Inicie o servidor embutido apontando para `public/`:

	```powershell
	php -S localhost:8000 -t "C:\Users\Mazzi\Documents\TCC-Topografia\public"
	```

3. Acesse o site em http://localhost:8000/.
4. Entre no painel: http://localhost:8000/login.php (usuário padrão `admin`, senha gerada via `password_hash` em `init_sqlite.php`).

> **Nota:** ao primeiro acesso, `init_sqlite.php` cria `database/site.sqlite` com os dados iniciais. Apague o arquivo para regenerar o seed do zero.

## Gmail API (contato)
1. Crie um projeto no [Google Cloud Console](https://console.cloud.google.com/), ative a Gmail API e configure a tela de consentimento.
2. Gere credenciais OAuth (tipo aplicativo desktop) e obtenha **Client ID**, **Client Secret** e **Refresh Token** com o escopo `https://www.googleapis.com/auth/gmail.send`.
3. Defina as variáveis de ambiente antes de iniciar o servidor PHP:

	```powershell
	setx GMAIL_CLIENT_ID "seu-client-id"
	setx GMAIL_CLIENT_SECRET "seu-client-secret"
	setx GMAIL_REFRESH_TOKEN "seu-refresh-token"
	setx GMAIL_SENDER "seu.email@gmail.com"
	setx GMAIL_USER "seu.email@gmail.com"
	```

	Depois abra um novo terminal (ou use comandos `Set-Item Env:` na sessão atual) e reinicie o servidor.

4. Ao enviar uma mensagem em `/contato.php`, o site grava o lead na tabela `contacts` e dispara o e-mail via Gmail API. Tokens são armazenados em `storage/gmail_token.json` com logs em `storage/gmail_debug.log`.

## Deploy
- Certifique-se de definir corretamente as variáveis de ambiente (DB, Gmail, etc.) no provedor.
- Aponte o document root para `public/`.
- Proteja `database/` e `storage/` contra acesso direto.
- Se utilizar SQLite em produção, mantenha backup agendado do arquivo `database/site.sqlite`.
