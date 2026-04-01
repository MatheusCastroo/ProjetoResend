# Documentação — ProjetoResend (SaaS multi-tenant de e-mails transacionais)

Este documento descreve o **processo completo** do sistema: desde a requisição HTTP até o envio pelo Resend, passando por isolamento por cliente, templates e armazenamento no banco.

---

## 1. Objetivo do sistema

O ProjetoResend é um **MVP de painel web** em PHP que permite:

- Cadastrar **clientes** (tenants), cada um com identidade visual própria.
- Criar **templates de e-mail** com HTML e variáveis dinâmicas (`{{chave}}`).
- Opcionalmente usar **templates base** e **layout padrão** no cliente para reaproveitar estrutura HTML.
- Fazer **upload de imagens** via API externa e registrar URLs por cliente.
- **Enviar e-mails** pela API do **Resend** e **registrar histórico** (destinatário, assunto, conteúdo, status, resposta da API).

Não há framework pesado: há um **roteador simples**, **controllers**, **services**, **repositories** e **views** em PHP.

---

## 2. Requisitos de ambiente

| Item | Observação |
|------|------------|
| PHP | 8.x recomendado (tipagem e `::class` no roteador) |
| MySQL / MariaDB | Para importar `database/schema.sql` |
| Apache | Com `mod_rewrite` (para URLs amigáveis em `public/`) |
| Extensões | `pdo_mysql`, `curl`, `json`, `session` |
| Contas / chaves | Chave **Resend**, chave **API de upload** (Caw Agency), domínio/remetente válidos na Resend |

---

## 3. Instalação resumida

1. **Coloque o projeto** em um diretório servido pelo Apache (ex.: `htdocs/ProjetoResend`).
2. **Crie o banco** importando `database/schema.sql` (phpMyAdmin ou `mysql < schema.sql`).
3. **Configure** `config/config.php` (ou copie de `config/config.example.php`):
   - `db.*` — host, nome do banco, usuário e senha.
   - `app.base_url` — caminho público até a pasta `public` (ex.: `/ProjetoResend/public`). Deve bater com a URL que o navegador usa, para links e redirecionamentos.
   - `app.default_test_email` — e-mail sugerido no formulário de envio (padrão do MVP).
   - `resend.api_key` e `resend.from` — autenticação e remetente do Resend (`from` deve ser um remetente permitido na conta Resend).
   - `caw_upload.api_key` — Bearer para `https://cloud.caw.agency/api/upload`.
4. **Acesse** a aplicação pela pasta `public`:
   - Exemplo: `http://localhost/ProjetoResend/public/`

### 3.1. Se o `mod_rewrite` não estiver ativo

O arquivo `public/.htaccess` redireciona tudo para `index.php`. Sem rewrite, você pode acessar rotas com o parâmetro **`r`**:

- Exemplo: `http://localhost/ProjetoResend/public/index.php?r=/clientes`

A função `request_path()` em `helpers.php` lê `$_GET['r']` quando presente.

---

## 4. Fluxo de uma requisição HTTP

Ordem típica:

1. O navegador chama uma URL sob `public/` (ex.: `/ProjetoResend/public/clientes`).
2. O Apache entrega para **`public/index.php`** (via rewrite ou `index.php` direto).
3. `index.php` carrega **`bootstrap.php`**, que:
   - inicia sessão;
   - carrega **`helpers.php`** (config, URL base, CSRF, flash, `view()`, `request_path()`);
   - registra **autoload** para classes em `core/`, `controllers/`, `services/`, `repositories/`.
4. Instancia **`Router`**, registra rotas **GET/POST** → `[NomeController::class, 'metodo']`.
5. Chama `$router->dispatch($method, request_path())`.
6. O **`Router`** localiza o handler e executa `new Controller()` → `metodo()`.
7. O **controller** usa **repositories** (PDO), **services** (regra de negócio) e chama **`view('pasta/arquivo', $dados)`**, que renderiza a view e envolve em **`views/layout.php`**.

Nada de front controller fora do `public/` — pastas internas não devem ser expostas diretamente pelo Apache se o DocumentRoot apontar só para `public/`.

---

## 5. Multi-tenant (isolamento por cliente)

- **Conceito:** cada **cliente** é um tenant. Tabelas como `templates`, `template_bases`, `midias` e `emails` possuem **`cliente_id`** e chaves estrangeiras com `ON DELETE CASCADE` onde aplicável.
- **Dados do cliente** usados na identidade visual:
  - `nome`
  - `logo_url`
  - `cor_primaria`, `cor_secundaria`
  - `layout_padrao` (HTML opcional com placeholder `{{conteudo}}`)

Toda listagem ou edição de templates/mídias no código é feita **filtrando por `cliente_id`**, para não misturar dados entre tenants.

---

## 6. Modelo de dados (resumo)

| Tabela | Função |
|--------|--------|
| **clientes** | Tenant: nome, logo, cores, layout opcional. |
| **template_bases** | HTML “casca” reutilizável; deve conter `{{conteudo}}` onde o corpo do template entra. |
| **templates** | Nome, assunto, HTML, JSON de variáveis, vínculo opcional com `template_base_id`. |
| **midias** | Registro de uploads: nome amigável, URL retornada pela API, `provider` (ex.: cloudinary). |
| **emails** | Log de envios: destinatário, assunto, HTML final, `status` (`PENDENTE` / `ENVIADO` / `ERRO`), JSON/texto da resposta da API, `data_envio`. |

O campo **`emails.status`** inclui `PENDENTE` para evoluções futuras (fila assíncrona); no fluxo atual de envio direto o sistema grava normalmente `ENVIADO` ou `ERRO`.

---

## 7. Templates e variáveis dinâmicas

### 7.1. Formato dos placeholders

No **assunto** e no **HTML**, use placeholders no formato:

```text
{{nome_cliente}}  {{logo}}  {{cor_primaria}}  {{nome}}  {{link}}
```

A classe **`TemplateEngine`** (`services/TemplateEngine.php`) substitui cada `{{chave}}` pelos valores do array `$data`. Valores escalares são **escapados para HTML** (proteção XSS em conteúdo inserido no template).

### 7.2. JSON de variáveis no template

O campo **`templates.variaveis`** guarda um JSON que define quais chaves **extras** aparecem na tela de envio (além do branding automático). Formatos suportados pela leitura:

- Lista de strings: `["nome","empresa","link"]`
- Lista de objetos com `key`: `[{"key":"nome","label":"Nome"}]` — usa-se a **`key`** para o placeholder e para o nome do campo no formulário.

O método estático **`TemplateEngine::variaveisFromJson()`** extrai os nomes das chaves para montar os inputs em **`views/envio/index.php`**.

### 7.3. Variáveis injetadas automaticamente no envio

No fluxo de envio, o sistema **mescla** sempre (via **`MailComposerService`**):

| Chave | Origem |
|--------|---------|
| `nome_cliente` | `clientes.nome` |
| `logo` | `clientes.logo_url` |
| `cor_primaria` | `clientes.cor_primaria` |
| `cor_secundaria` | `clientes.cor_secundaria` |

Depois disso vêm as variáveis preenchidas pelo usuário no formulário, conforme o JSON do template.

---

## 8. Processo de montagem do HTML do e-mail (`MailComposerService`)

Ordem lógica:

1. **Corpo inicial** = HTML do **template** (`templates.html`).
2. Se existir **`template_base_id`** válido para o mesmo cliente:
   - Carrega o HTML de **`template_bases`**.
   - Substitui **`{{conteudo}}`** nesse HTML pelo corpo do template (passo 1).
3. **Senão**, se o cliente tiver **`layout_padrao`**:
   - Substitui **`{{conteudo}}`** no layout pelo corpo do template.
4. **Senão**, usa só o corpo do template.
5. Aplica **`TemplateEngine::render()`** no **assunto** e no **HTML** final com o array de dados mesclado (branding + variáveis do formulário).

Assim, **primeiro** define-se a estrutura (base ou layout), **depois** aplica-se o motor de variáveis em todo o texto.

---

## 9. Processo de envio de e-mail (passo a passo)

1. **Tela `/envio`:** o usuário escolhe **cliente** e **template** (query string `cliente_id` e `template_id`).
2. O sistema carrega variáveis do JSON e exibe campos `var_*` para cada chave.
3. **Destinatário** — padrão de testes vem de `app.default_test_email` no `config.php`.
4. Ao submeter **Enviar** (`POST /envio/enviar`):
   - Valida CSRF (`csrf_verify()`).
   - Carrega cliente e template; monta dados com **`MailComposerService::compose()`**.
   - Chama **`EmailService::send()`**, que faz **HTTP POST** em `https://api.resend.com/emails` com JSON (`from`, `to`, `subject`, `html`) e header `Authorization: Bearer <RESEND_API_KEY>`.
   - Insere linha em **`emails`** com status **`ENVIADO`** ou **`ERRO`**, e grava corpo da resposta da API em **`resposta_api`** (JSON serializado).
5. **Pré-visualização** (`POST /envio/preview`): mesmo pipeline de composição, mas a resposta é só o **HTML** no navegador (nova aba), sem chamar a Resend.

---

## 10. Upload de imagens e logo

### 10.1. Mídias (`/midias`)

- Formulário envia arquivo via **`POST /midias/upload`**.
- **`MediaService`** (`services/MediaService.php`) envia multipart com **cURL** para a URL configurada (`caw_upload.url`), com header **`Authorization: Bearer`**, e campo **`provider`** (ex.: `cloudinary`).
- A resposta esperada é JSON contendo URL em chaves comuns (`url`, `data.url`, `secure_url`); se a API mudar, ajuste a extração em **`MediaService::uploadFile()`**.
- **`MediaRepository`** persiste `cliente_id`, `nome`, `url`, `provider`.

### 10.2. Logo no cadastro do cliente

- No **salvar cliente**, se houver arquivo em **`logo_file`**, o mesmo **`MediaService`** é usado e o retorno preenche **`logo_url`** (substituindo ou complementando a URL digitada manualmente).

---

## 11. Camadas do código

| Camada | Papel |
|--------|--------|
| **Controllers** | Recebem HTTP, validam entrada básica, chamam services/repositories, redirecionam ou renderizam views. |
| **Repositories** | SQL com **PDO** e **prepared statements**; um repositório por agregado principal. |
| **Services** | Regras: renderização de template, composição do e-mail, chamada Resend, upload HTTP. |
| **core/Database** | Singleton PDO. |
| **helpers.php** | Config, URLs, sessão flash, CSRF, helper `view()`. |

Evitar lógica pesada nas views; mantê-la em services/controllers.

---

## 12. Segurança (práticas adotadas)

- **PDO prepared statements** nos repositories (mitigação a SQL injection).
- **CSRF** em formulários POST (`csrf_token()` / `csrf_verify()`).
- **Escape de saída** nas views com `e()` (`htmlspecialchars`).
- **`TemplateEngine`** escapa valores substituídos em `{{...}}` por padrão.

Para produção, recomenda-se HTTPS, política de senha se houver login futuro, e não versionar `config.php` com chaves reais (usar variáveis de ambiente ou `config.local.php` fora do repositório).

---

## 13. Mapa de rotas (referência)

Rotas registradas em **`public/index.php`**:

| Método | Caminho | Ação |
|--------|---------|------|
| GET | `/` | Dashboard |
| GET | `/clientes` | Lista clientes |
| GET | `/clientes/novo` | Form novo |
| GET | `/clientes/editar?id=` | Form edição |
| POST | `/clientes/salvar` | Criar/atualizar |
| POST | `/clientes/excluir` | Excluir |
| GET | `/templates?cliente_id=` | Lista templates |
| GET/POST | `/templates/novo`, `/templates/editar`, `/templates/salvar`, `/templates/excluir` | CRUD templates |
| GET/POST | `/template-bases/...` | CRUD templates base |
| GET/POST | `/midias/...` | Lista, upload, exclusão de mídia |
| GET | `/envio` | Tela de envio |
| POST | `/envio/preview` | Preview HTML |
| POST | `/envio/enviar` | Enviar via Resend |
| GET | `/historico` | Histórico (filtro opcional `cliente_id`) |
| GET | `/historico/ver?id=` | HTML bruto do log |

---

## 14. Solução de problemas comuns

| Sintoma | O que verificar |
|---------|-----------------|
| Links quebrados ou 404 nas rotas | `app.base_url` no `config.php`; `mod_rewrite` e `RewriteBase` em `public/.htaccess`. |
| Erro ao enviar e-mail | Chave Resend, remetente `from` autorizado, resposta em `emails.resposta_api` no banco. |
| Upload falha | Chave da API Caw, formato da resposta JSON, firewall permitindo `curl` para o host externo. |
| Variáveis vazias no e-mail | JSON de variáveis no template; placeholders com o **mesmo nome** de chave; branding (`nome_cliente`, etc.) só existe após escolher cliente. |

---

## 15. Extensões possíveis (não implementadas no MVP)

- Autenticação de usuários e permissões por cliente.
- Worker/cron que lê `emails` com `status = PENDENTE` e envia em fila.
- Webhooks da Resend para atualizar eventos (entrega, bounce).

---

*Documento alinhado à estrutura atual do repositório. Em caso de alteração de rotas ou serviços, atualize esta documentação junto com o código.*
