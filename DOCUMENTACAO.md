# Documentação — ProjetoResend (Applications + API + Resend)

## Visão geral

Sistema **multi-tenant** em PHP (MVC leve) onde cada **application** representa um produto/site externo que:

- se autentica na API com **`X-API-KEY`**
- possui **Resend API Key** e **remetente (`from`)** próprios
- define **identidade visual** (logo, cores)
- mantém **templates** com HTML/assunto e variáveis `{{chave}}`
- pode opcionalmente definir um **`event_key`** por template para envios **orientados a evento** (sem expor `template_id` na integração)
- registra **logs** de envio na tabela `emails`

Os envios transacionais podem usar:

- **`POST /api/event/{event_key}`** (recomendado para integrações — identifica o template pelo evento), ou
- **`POST /api/send/{template_id}`** (compatível com fluxos que já usam o ID numérico), ou
- o **painel** (envio manual de teste).

A orquestração de compose + Resend + log está centralizada em **`TransactionalMailSender`**, reutilizada pelos dois endpoints da API.

---

## Banco de dados

Importe `database/schema.sql`. Tabelas principais:

| Tabela | Descrição |
|--------|-----------|
| **applications** | `nome`, `api_key` (única), `resend_api_key`, `resend_from`, `logo_url`, cores |
| **templates** | Conteúdo global: `nome`, **`event_key`** (opcional), `assunto`, `html`, `variaveis` (JSON) |
| **application_templates** | Vínculo N:N — qual application pode usar qual template |
| **emails** | Log: `application_id`, `template_id`, destinatário, assunto, HTML final, `status`, `resposta_api`, `created_at` |
| **media** | Uploads por application (`url`, `provider`) |

**Event key:** vários templates podem repetir o mesmo `event_key` em cenários futuros (ex.: A/B). Para uma mesma application, se mais de um template vinculado compartilhar o mesmo `event_key`, o sistema usa o de **menor `id`**.

**Atualização em banco já existente:** se as tabelas foram criadas antes do campo `event_key`, execute `database/migration_templates_event_key.sql`.

---

## API pública

### `POST /api/event/{event_key}`

Envio **orientado a evento**. O cliente não precisa conhecer `template_id`; basta cadastrar no painel o **`event_key`** no template (ex.: `reset_password`, `welcome_user`) e vincular esse template à application.

- **URL (ex.):** `https://seu-dominio/ProjetoResend/public/api/event/reset_password`
- **Headers:** `X-API-KEY: <api_key da application>` · `Content-Type: application/json`
- **Body:**

```json
{
  "email": "destinatario@gmail.com",
  "data": {
    "nome": "Matheus",
    "reset_link": "https://app.com/reset"
  }
}
```

#### Respostas

| HTTP | Situação |
|------|----------|
| **200** | `{"success":true,"message":"E-mail enviado com sucesso"}` |
| **400** | JSON inválido ou e-mail ausente/incorreto |
| **401** | Existe template com esse `event_key`, mas **nenhum** está vinculado à application da chave |
| **403** | `X-API-KEY` ausente ou inválida |
| **404** | Nenhum template no sistema possui esse `event_key` |
| **500** | Falha no envio Resend (detalhes opcionais em `detail`) |

CORS: `Access-Control-Allow-Origin: *` e suporte a **OPTIONS** para chamadas de browser.

#### Fluxo interno

1. `ApiEventController` valida `X-API-KEY` → `ApplicationRepository::findByApiKey`.
2. `TemplateRepository::findFirstLinkedByEventKey` obtém o template vinculado à application com aquele `event_key`.
3. Se não houver vínculo mas existir template com esse `event_key` em outro contexto → **401**. Se não existir template com esse `event_key` → **404**.
4. **`TransactionalMailSender`**: `ApplicationMailComposer` + `TemplateEngine` + `EmailService::sendWithCredentials` + `EmailRepository::create`.

---

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

#### Respostas

| HTTP | Situação |
|------|----------|
| **200** | `{"success":true,"message":"E-mail enviado com sucesso"}` |
| **400** | JSON inválido ou e-mail ausente/incorreto |
| **401** | Template inexistente ou não está vinculado à application da chave |
| **403** | `X-API-KEY` ausente ou inválida |
| **500** | Falha no envio Resend (corpo da resposta pode vir em `detail`) |

CORS: `Access-Control-Allow-Origin: *` e suporte a **OPTIONS** para chamadas de browser.

#### Fluxo interno

1. `ApiSendController` valida `X-API-KEY` → `ApplicationRepository::findByApiKey`.
2. Carrega o template e confere se existe **vínculo** em `application_templates` entre essa application e o `template_id` (`TemplateRepository::isLinked`).
3. **`TransactionalMailSender`** → mescla branding com `data`, renderiza, envia via Resend com credenciais da application, grava log em `emails`.

---

## Painel administrativo

Rotas principais (sob `public/`):

| Rota | Função |
|------|--------|
| `/` | Dashboard |
| `/applications` | CRUD applications; nova app gera **API Key** automaticamente |
| `/templates` | Biblioteca de templates (globais); `?application_id=` filtra por app e permite vincular/desvincular; formulário inclui **Event key (API)** opcional para `POST /api/event/...` |
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
- Lógica de envio transacional em **`TransactionalMailSender`**; composição e branding em **`ApplicationMailComposer`**, render em **`TemplateEngine`**, envio em **`EmailService`**.
- API retorna **JSON** padronizado; erros seguem os códigos da tabela de cada endpoint.
