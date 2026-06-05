# Arquitetura do Sistema Inven

> **Curso:** Análise e Desenvolvimento de Sistemas · UNIFOR  
> **Documento:** Arquitetura final implementada — Etapa 2

---

## Sumário

1. [Arquitetura Final Implementada](#1-arquitetura-final-implementada)
2. [Componentes do Sistema](#2-componentes-do-sistema)
3. [Integrações Realizadas](#3-integrações-realizadas)
4. [Principais Mudanças Arquiteturais](#4-principais-mudanças-arquiteturais)
5. [Decisões Técnicas e Justificativas](#5-decisões-técnicas-e-justificativas)

---

## 1. Arquitetura Final Implementada

O sistema Inven adota uma arquitetura **cliente-servidor simplificada**, com frontend e backend separados comunicando-se via API REST em formato JSON.

```
┌─────────────────────────────────────────────────────┐
│                    CLIENTE                          │
│                                                     │
│   index.html  (HTML + CSS + JavaScript)             │
│   ┌──────────┐ ┌──────────┐ ┌──────────────────┐   │
│   │  Login   │ │Dashboard │ │Materiais/Estoque │   │
│   └──────────┘ └──────────┘ └──────────────────┘   │
│                                                     │
│   Fetch API → Authorization: Bearer <JWT>           │
└─────────────────────┬───────────────────────────────┘
                      │ HTTP/JSON
                      ▼
┌─────────────────────────────────────────────────────┐
│                    SERVIDOR (XAMPP)                 │
│                                                     │
│   Apache 2.4  →  .htaccess (mod_rewrite)            │
│                      │                              │
│                      ▼                              │
│               api.php (PHP 8.2)                     │
│   ┌──────────────────────────────────────────┐      │
│   │  Roteador (?rota=)                       │      │
│   │  ├── jwt_verificar()                     │      │
│   │  ├── Rotas de Usuários                   │      │
│   │  ├── Rotas de Materiais                  │      │
│   │  └── Rotas de Estoque/Movimentações      │      │
│   └──────────────────┬───────────────────────┘      │
│                      │ PDO                          │
│                      ▼                              │
│              MySQL 8.0 (inven_db)                   │
│   ┌──────────┐ ┌──────────┐ ┌────────────────────┐  │
│   │usuarios  │ │materiais │ │movimentacoes       │  │
│   └──────────┘ └──────────┘ └────────────────────┘  │
│                  ┌──────────────┐                   │
│                  │palavras_chave│                   │
│                  └──────────────┘                   │
└─────────────────────────────────────────────────────┘
```

---

## 2. Componentes do Sistema

### 2.1 Frontend — `index.html`

Aplicação de página única (SPA manual) desenvolvida em HTML, CSS e JavaScript puro, concentrada em um único arquivo.

| Responsabilidade | Detalhes |
|---|---|
| Gerenciamento de telas | Controle via `display` CSS e manipulação do DOM |
| Autenticação | Armazena JWT no `localStorage`, envia no header de cada requisição |
| Comunicação com API | Fetch API com JSON, função centralizada `api(method, rota, body)` |
| Estado da aplicação | Variável global `materiais[]` sincronizada após cada operação |
| Feedback visual | Toast de notificação, badges de status, modais de confirmação |

**Telas implementadas:**
- Login e Cadastro
- Dashboard (métricas + tabela resumo)
- Materiais (CRUD completo com busca)
- Estoque (cards com controles +/-)
- Movimentações (histórico + registro)

### 2.2 Backend — `api.php`

API REST em PHP puro (sem frameworks) em arquivo único.

| Componente | Função |
|---|---|
| `db()` | Singleton de conexão PDO ao MySQL |
| `resposta()` | Padroniza saída JSON com HTTP status code |
| `body()` | Lê e decodifica o corpo da requisição |
| `jwt_criar()` | Gera token JWT assinado com HMAC-SHA256 |
| `jwt_verificar()` | Valida token e retorna payload ou encerra com 401 |
| Roteador | Lê `?rota=` e despacha para o bloco correspondente |

### 2.3 Banco de Dados — MySQL 8.0

Quatro tabelas com relacionamentos via chaves estrangeiras:

```sql
usuarios
  id, nome, email, senha, criado_em

materiais
  id, usuario_id (FK), descricao, preco,
  fonte, telefone, email, estoque, criado_em

palavras_chave
  id, material_id (FK), palavra

movimentacoes
  id, material_id (FK), usuario_id (FK),
  tipo, quantidade, observacao, criado_em
```

**Regras de integridade:**
- `ON DELETE CASCADE` em todas as chaves estrangeiras
- Índice único em `usuarios.email`
- `tipo` de movimentação restrito por `ENUM('entrada','saida','ajuste')`

### 2.4 Servidor Web — Apache (XAMPP)

Configurado via `.htaccess` na pasta `inven/`:

```apache
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^api/(.*)$ api.php?rota=$1 [QSA,L]
```

A linha `E=HTTP_AUTHORIZATION` é essencial para que o Apache repasse o header `Authorization` ao PHP no XAMPP.

---

## 3. Integrações Realizadas

| Integração | Tipo | Descrição |
|---|---|---|
| Frontend ↔ Backend | HTTP/REST/JSON | Fetch API com header `Authorization: Bearer <token>` |
| Backend ↔ MySQL | PDO | Prepared statements para todas as queries |
| Apache ↔ PHP | FastCGI / mod_php | Processamento das requisições pelo XAMPP |

O sistema não realiza integrações com APIs externas nesta etapa (alertas via WhatsApp, serviços de e-mail e relatórios em PDF estão previstos para versões futuras).

---

## 4. Principais Mudanças Arquiteturais

### 4.1 De MVC com Laravel → PHP puro em arquivo único

| Aspecto | Planejado | Implementado |
|---|---|---|
| Framework | Laravel | PHP puro (sem framework) |
| Organização | MVC com Controllers, Models, Middleware separados | Funções em arquivo único `api.php` |
| Roteamento | `REQUEST_URI` via `.htaccess` puro | Query string `?rota=` |
| Autoload | Composer PSR-4 | `require_once` manual |

**Motivo:** Incompatibilidades entre a configuração do XAMPP no Windows e o sistema de roteamento do Laravel (conflitos de `.htaccess` em subdiretórios, `mod_rewrite` com comportamento inconsistente). A abordagem de arquivo único eliminou essas dependências e garantiu funcionamento estável.

### 4.2 De 6 tabelas normalizadas → 4 tabelas simplificadas

| Aspecto | Planejado | Implementado |
|---|---|---|
| Tabelas | 6 (USUARIO, MATERIAIS, FONTE, PRECO, MOVI_MATE, ESTO_MATE) | 4 (usuarios, materiais, palavras_chave, movimentacoes) |
| Preço | Tabela separada `PRECO` com histórico | Campo `preco` diretamente em `materiais` |
| Estoque | Tabela separada `ESTO_MATE` | Campo `estoque` diretamente em `materiais` |
| Fonte | Tabela separada `FONTE` | Campos `fonte`, `telefone`, `email` em `materiais` |

**Motivo:** A desnormalização simplificou as queries (sem JOINs complexos) e reduziu o código do backend, sem perda funcional para o escopo atual (preço único por material, não histórico de preços).

### 4.3 Frontend multi-arquivo → arquivo único

| Aspecto | Planejado | Implementado |
|---|---|---|
| Estrutura | HTML, CSS e JS separados em múltiplos arquivos e pastas | Tudo em `index.html` |
| Acesso | Via servidor web normal | Via `http://localhost/inven/index.html` |

**Motivo:** A estrutura multi-arquivo gerava conflitos com o `.htaccess` do backend quando ambos estavam no mesmo `htdocs` do XAMPP. O arquivo único eliminou o problema de roteamento indevido de arquivos estáticos.

---

## 5. Decisões Técnicas e Justificativas

### PHP puro sem framework
**Decisão:** Não utilizar Laravel ou outro framework PHP.  
**Justificativa:** Garantir funcionamento confiável no ambiente XAMPP disponível para a equipe, sem dependências externas (Composer, configurações adicionais do servidor). O PHP puro com PDO atende a todos os requisitos do projeto.

### JWT implementado manualmente
**Decisão:** Implementar JWT sem bibliotecas externas (`firebase/php-jwt` ou similar).  
**Justificativa:** Eliminar dependências externas (Composer) e garantir funcionamento imediato no ambiente XAMPP. A implementação cobre os requisitos de segurança necessários: assinatura HMAC-SHA256, verificação de expiração e validação de integridade com `hash_equals`.

### Roteamento via query string
**Decisão:** Usar `?rota=` em vez de URLs REST puras (`/materiais/5`).  
**Justificativa:** O XAMPP no Windows apresenta comportamento inconsistente do `mod_rewrite` para subdiretórios. A query string funciona em qualquer configuração do Apache sem necessidade de ajustes adicionais.

### PDO com prepared statements
**Decisão:** Usar PDO com prepared statements para todas as queries.  
**Justificativa:** Proteção contra SQL Injection sem overhead de um ORM completo. O PDO permite migração futura para outro banco de dados (PostgreSQL, SQLite) com mínimas alterações.

### Bcrypt para senhas
**Decisão:** Usar `password_hash($senha, PASSWORD_BCRYPT)` e `password_verify()`.  
**Justificativa:** Padrão seguro e nativo do PHP, com custo computacional adaptável e sem dependências externas.

### `ON DELETE CASCADE` no banco
**Decisão:** Configurar exclusão em cascata em todas as chaves estrangeiras.  
**Justificativa:** Garante integridade referencial automática — ao excluir um material, suas palavras-chave e movimentações são removidas automaticamente, sem necessidade de múltiplas queries no backend.

### CORS aberto (`*`) em desenvolvimento
**Decisão:** `Access-Control-Allow-Origin: *` durante o desenvolvimento.  
**Justificativa:** Facilita o desenvolvimento local sem restrições de origem. Em produção, deve ser substituído pelo domínio específico do frontend.
