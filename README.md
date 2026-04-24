# ProjetoResend — rodar com Docker

## Requisitos

- Windows 10/11 com **Docker Desktop** instalado e com o engine Linux habilitado.
- (Opcional) Git para clonar/atualizar o repositório.

Se o comando `docker` não existir no PowerShell, instale o Docker Desktop e reinicie o terminal.

## Subir o projeto

1) (Opcional) Crie um `.env` a partir do exemplo:

- Copie `.env.example` para `.env`
- Ajuste `WEB_PORT` se quiser outra porta
- Ajuste `MYSQL_ROOT_PASSWORD` / `MYSQL_DATABASE` se quiser

2) Suba os containers:

3) Acesse no navegador:

- `http://localhost:8080/` (ou a porta definida em `WEB_PORT`)

## Banco de dados (MySQL)

- O MySQL sobe como serviço `db` e inicializa automaticamente com `database/schema.sql`.
- Os dados ficam persistidos no volume `mysql_data`.

## Variáveis de ambiente importantes

O container `web` já injeta as variáveis de banco via `docker-compose.yml`:

- `DB_HOST=db`
- `DB_PORT=3306`
- `DB_NAME` (default `projeto_resend`)
- `DB_USER=root`
- `DB_PASS` (default igual ao `MYSQL_ROOT_PASSWORD`)

Para o painel/API:

- `APP_BASE_URL`: no Docker fica vazio (`""`) para a aplicação rodar em `/`.
- `DEFAULT_TEST_EMAIL`: e-mail padrão do envio manual.

## Tamanho das imagens (Docker)

- A imagem **`web`**, construída a partir do `Dockerfile` (PHP **CLI Alpine** + servidor embutido), em geral fica **por volta de 40–55 MB** no `docker images` — depende da tag base e de quantas camadas você já tiver no cache.
- A imagem **`db`** usa **`mysql:8.0`**; ela continua **grande** (centenas de MB). Só a app + PHP é que fica leve. Isso é esperado.
