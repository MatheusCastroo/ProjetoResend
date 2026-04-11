# Documentação — ProjetoResend (Applications + API + Resend)

## Visão geral

Sistema **multi-tenant** em PHP (MVC leve) onde cada **application** representa um produto/site externo que:

- se autentica na API com **`X-API-KEY`**
- possui **Resend API Key** e **remetente (`from`)** próprios
- define **identidade visual** (logo, cores)
- mantém **templates** com HTML/assunto e variáveis `{{chave}}`
- registra **logs** de envio na tabela `emails`

O envio transacional usa **`POST /api/send/{template_id}`** (JSON) ou o **painel** (envio manual de teste).

---

## Banco de dados

Importe `database/schema.sql`. Tabelas principais:

| Tabela | Descrição |
|--------|-----------|
| **applications** | `nome`, `api_key` (única), `resend_api_key`, `resend_from`, `logo_url`, cores |
| **templates** | Conteúdo global: `nome`, `assunto`, `html`, `variaveis` (JSON) |
| **application_templates** | Vínculo N:N — qual application pode usar qual template |
| **emails** | Log: `application_id`, `template_id`, destinatário, assunto, HTML final, `status`, `resposta_api`, `created_at` |
| **media** | Uploads por application (`url`, `provider`) |

---

## API pública

### `POST /api/send/{template_id}`

- **URL (ex.):** `https://seu-dominio/ProjetoResend/public/api/send/1`
- **Headers:** `X-API-KEY: <api_key da application>` · `Content-Type: application/json`
- **Body:**

```json
{
  "email": "destinatario@gmail.com",
  "data": {
    "nome": "Matheus",
    "link": "https://app.com/reset"
  }
}
```

### Respostas

| HTTP | Situação |
|------|----------|
| **200** | `{"success":true,"message":"E-mail enviado com sucesso"}` |
| **400** | JSON inválido ou e-mail ausente/incorreto |
| **401** | Template inexistente ou não está vinculado à application da chave |
| **403** | `X-API-KEY` ausente ou inválida |
| **500** | Falha no envio Resend (corpo da resposta pode vir em `detail`) |

CORS: `Access-Control-Allow-Origin: *` e suporte a **OPTIONS** para chamadas de browser.

### Fluxo interno

1. `ApiSendController` valida `X-API-KEY` → `ApplicationRepository::findByApiKey`.
2. Carrega o template e confere se existe **vínculo** em `application_templates` entre essa application e o `template_id` (`TemplateRepository::isLinked`).
3. `ApplicationMailComposer` mescla branding (`empresa`, `logo`, `cor_primaria`, `cor_secundaria`) com `data`.
4. `TemplateEngine::render` no assunto e HTML.
5. `EmailService::sendWithCredentials` com `resend_api_key` e `resend_from` **da application**.
6. `EmailRepository::create` grava o log.

---

## Painel administrativo

Rotas principais (sob `public/`):

| Rota | Função |
|------|--------|
| `/` | Dashboard |
| `/applications` | CRUD applications; nova app gera **API Key** automaticamente |
| `/templates` | Biblioteca de templates (globais); `?application_id=` filtra por app e permite vincular/desvincular; mesmo template pode servir a várias applications |
| `/media?application_id=` | Upload/listagem |
| `/envio` | Envio manual (teste) |
| `/logs` | Histórico com filtro por application |

Upload de logo e media usa **`MediaService`** → `caw_upload.api_url` (padrão `https://cloud.caw.agency/api/upload`) com Bearer em `caw_upload.api_key`. Alternativa: variáveis de ambiente `CAW_UPLOAD_API_KEY`, `CAW_UPLOAD_API_URL`, `CAW_UPLOAD_API_BASE`, `CAW_UPLOAD_PROVIDER` (ou `CAW_API_KEY` para a chave).

---

## Configuração (`config/config.php`)

- **`db.*`** — MySQL.
- **`app.base_url`** — ex.: `/ProjetoResend/public` (deve coincidir com a URL pública).
- **`app.default_test_email`** — pré-preenchido no envio manual.
- **`caw_upload.*`** — `api_key`, `api_url`, `api_base`, `provider` (upload de imagens ≠ Resend).

**Resend:** não há chave global; cada **application** guarda `resend_api_key` e `resend_from` no painel.

---

## Variáveis nos templates

Injetadas automaticamente (podem ser usadas como `{{empresa}}`, `{{logo}}`, etc.):

- `empresa` — nome da application  
- `logo` — URL do logo  
- `cor_primaria` / `cor_secundaria`

O JSON **`templates.variaveis`** define campos extras para o painel de envio manual e para documentar chaves usadas em `data` na API.

---

## Boas práticas no código

- **Repositories** com PDO e prepared statements.
- Lógica de envio e renderização em **services** (`ApplicationMailComposer`, `EmailService`, `TemplateEngine`).
- API retorna **JSON** padronizado; erros seguem os códigos acima.
