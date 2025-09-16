# TCC - Topografia

Projeto simples em PHP + MySQL com Bootstrap.

## Estrutura
- public/ páginas do site (index, serviços, sobre, faq, contato, login/logout)
- admin/ dashboard e CRUD de Serviços, FAQ e Feedbacks
- database/site.sql script para criar o banco e tabelas

## Pré-requisitos
- PHP 8.x
- MySQL 5.7+ ou 8.x

## Instalação
1) Importe o script SQL:
- Abra o MySQL e execute o arquivo `database/site.sql`.

2) Configure a conexão no `admin/config.php` via variáveis de ambiente (opcional):
- DB_HOST, DB_NAME, DB_USER, DB_PASS.
- Sem variáveis, usa: host=127.0.0.1, db=tcc_topografia, user=root, senha vazia.

## Executar localmente
Sirva a pasta `public` como raiz (document root) do servidor.

No Windows (PowerShell), com PHP instalado:

```powershell
php -S localhost:8000 -t "C:\Users\Mazzi\Documents\TCC-Topografia\public"
```

Acesse:
- Site: http://localhost:8000/
- Admin: http://localhost:8000/login.php (padrão: admin/admin)

## Observações
- Login é apenas demonstrativo; adicione hashing e usuários reais depois.
- Ajuste caminhos se publicar em hospedagem compartilhada.
