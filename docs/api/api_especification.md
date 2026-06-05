# Documentação da API Inven

> **Base URL (desenvolvimento):** `http://localhost/inven/api.php`  
> **Formato:** Todas as requisições e respostas utilizam `Content-Type: application/json`  
> **Autenticação:** JWT via header `Authorization: Bearer <token>`

---

## Sumário

1. [Autenticação e Autorização](#1-autenticação-e-autorização)
2. [Formato Padrão de Resposta](#2-formato-padrão-de-resposta)
3. [Endpoints de Usuários](#3-endpoints-de-usuários)
4. [Endpoints de Materiais](#4-endpoints-de-materiais)
5. [Endpoints de Estoque e Movimentações](#5-endpoints-de-estoque-e-movimentações)
6. [Códigos de Status HTTP](#6-códigos-de-status-http)
7. [Exemplos de Chamadas](#7-exemplos-de-chamadas)

---

## 1. Autenticação e Autorização

A API utiliza **JWT (JSON Web Token)** para autenticação. O token é gerado no login e deve ser enviado no header de todas as rotas protegidas.

```http
Authorization: Bearer SEU_TOKEN_JWT
```

**Características do token:**
- Algoritmo: HMAC-SHA256
- Expiração: 7 dias
- Payload: `{ id, email, exp }`

**Rotas públicas** (não exigem token):
- `POST ?rota=cadastro`
- `POST ?rota=login`
- `GET  ?rota=status`

**Rotas protegidas** (exigem token válido):
- Todas as rotas de materiais, estoque e movimentações

---

## 2. Formato Padrão de Resposta

### Sucesso

```json
{
  "mensagem": "Operação realizada com sucesso",
  "dados": { }
}
```

### Erro

```json
{
  "erro": "Descrição do erro"
}
```

---

## 3. Endpoints de Usuários

### 3.1 Health Check

```
GET http://localhost/inven/api.php?rota=status
```

**Autenticação:** Não  
**Parâmetros:** Nenhum

**Resposta (200):**
```json
{
  "app": "Inven API",
  "status": "online"
}
```

---

### 3.2 Cadastro de Usuário

```
POST http://localhost/inven/api.php?rota=cadastro
```

**Autenticação:** Não

**Body (JSON):**

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `nome` | string | Sim | Nome completo do usuário |
| `email` | string | Sim | E-mail válido e único |
| `senha` | string | Sim | Mínimo 6 caracteres |

```json
{
  "nome": "Maria Silva",
  "email": "maria@email.com",
  "senha": "minhasenha123"
}
```

**Resposta (201):**
```json
{
  "mensagem": "Usuário cadastrado com sucesso",
  "id": 1,
  "nome": "Maria Silva",
  "email": "maria@email.com",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Erros possíveis:**

| Status | Mensagem |
|---|---|
| 422 | `Preencha nome, email e senha.` |
| 422 | `E-mail inválido.` |
| 422 | `Senha precisa ter mínimo 6 caracteres.` |
| 409 | `E-mail já cadastrado.` |
| 500 | `Banco de dados indisponível.` |

---

### 3.3 Login

```
POST http://localhost/inven/api.php?rota=login
```

**Autenticação:** Não

**Body (JSON):**

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `email` | string | Sim | E-mail cadastrado |
| `senha` | string | Sim | Senha do usuário |

```json
{
  "email": "maria@email.com",
  "senha": "minhasenha123"
}
```

**Resposta (200):**
```json
{
  "id": 1,
  "nome": "Maria Silva",
  "email": "maria@email.com",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Erros possíveis:**

| Status | Mensagem |
|---|---|
| 422 | `Informe email e senha.` |
| 401 | `Email ou senha incorretos.` |

---

## 4. Endpoints de Materiais

> Todos os endpoints de materiais exigem autenticação. Os dados retornados pertencem exclusivamente ao usuário autenticado.

---

### 4.1 Listar Materiais

```
GET http://localhost/inven/api.php?rota=materiais
```

**Autenticação:** Sim  
**Parâmetros de query (opcionais):**

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `busca` | string | Filtra por descrição ou fonte (busca parcial) |

**Exemplo com filtro:**
```
GET http://localhost/inven/api.php?rota=materiais&busca=blusa
```

**Resposta (200):**
```json
{
  "dados": [
    {
      "id": 1,
      "usuario_id": 1,
      "descricao": "Blusa P",
      "preco": "20.00",
      "fonte": "Riachuelo",
      "telefone": "85 99999-9999",
      "email": "riachuelo@email.com",
      "estoque": "10.000",
      "criado_em": "2025-03-10 14:32:00",
      "palavras_chave": ["Blusa", "Roupa", "Feminino"]
    }
  ]
}
```

---

### 4.2 Cadastrar Material

```
POST http://localhost/inven/api.php?rota=materiais
```

**Autenticação:** Sim

**Body (JSON):**

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `descricao` | string | Sim | Nome/descrição do material |
| `preco` | number | Sim | Preço unitário |
| `fonte` | string | Sim | Nome do fornecedor/origem |
| `telefone` | string | Não | Telefone do fornecedor |
| `email` | string | Não | E-mail do fornecedor |
| `palavras_chave` | array | Não | Lista de tags do material |

```json
{
  "descricao": "Blusa P",
  "preco": 20.00,
  "fonte": "Riachuelo",
  "telefone": "85 99999-9999",
  "email": "contato@riachuelo.com",
  "palavras_chave": ["Blusa", "Roupa", "Feminino"]
}
```

**Resposta (201):**
```json
{
  "mensagem": "Material cadastrado com sucesso",
  "dados": {
    "id": 1,
    "usuario_id": 1,
    "descricao": "Blusa P",
    "preco": "20.00",
    "fonte": "Riachuelo",
    "telefone": "85 99999-9999",
    "email": "contato@riachuelo.com",
    "estoque": "0.000",
    "criado_em": "2025-03-10 14:32:00"
  }
}
```

**Erros possíveis:**

| Status | Mensagem |
|---|---|
| 401 | `Token não enviado.` |
| 401 | `Token inválido ou expirado.` |
| 422 | `Campos obrigatórios: descricao, preco, fonte.` |

---

### 4.3 Editar Material

```
PUT http://localhost/inven/api.php?rota=materiais/{id}
```

**Autenticação:** Sim  
**Parâmetros de rota:**

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `id` | integer | ID do material a editar |

**Body (JSON):** Envie apenas os campos que deseja atualizar.

```json
{
  "descricao": "Blusa P (nova coleção)",
  "preco": 25.00,
  "palavras_chave": ["Blusa", "Roupa", "Nova Coleção"]
}
```

**Resposta (200):**
```json
{
  "mensagem": "Material atualizado com sucesso",
  "dados": {
    "id": 1,
    "descricao": "Blusa P (nova coleção)",
    "preco": "25.00",
    "fonte": "Riachuelo",
    "estoque": "10.000"
  }
}
```

**Erros possíveis:**

| Status | Mensagem |
|---|---|
| 401 | `Token não enviado.` |
| 404 | `Material não encontrado.` |

---

### 4.4 Excluir Material

```
DELETE http://localhost/inven/api.php?rota=materiais/{id}
```

**Autenticação:** Sim  
**Parâmetros de rota:**

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `id` | integer | ID do material a excluir |

**Resposta (200):**
```json
{
  "mensagem": "Material excluído com sucesso."
}
```

**Erros possíveis:**

| Status | Mensagem |
|---|---|
| 401 | `Token não enviado.` |
| 404 | `Material não encontrado.` |

---

## 5. Endpoints de Estoque e Movimentações

---

### 5.1 Registrar Movimentação de Estoque

```
POST http://localhost/inven/api.php?rota=estoque/{id}/movimentar
```

**Autenticação:** Sim  
**Parâmetros de rota:**

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `id` | integer | ID do material |

**Body (JSON):**

| Campo | Tipo | Obrigatório | Descrição |
|---|---|---|---|
| `tipo` | string | Sim | `entrada`, `saida` ou `ajuste` |
| `quantidade` | number | Sim | Quantidade a movimentar (> 0) |
| `observacao` | string | Não | Descrição da movimentação |

```json
{
  "tipo": "entrada",
  "quantidade": 5,
  "observacao": "Compra nova coleção"
}
```

**Resposta (200):**
```json
{
  "mensagem": "Movimentação registrada.",
  "novo_estoque": 15
}
```

**Comportamento por tipo:**

| Tipo | Efeito no estoque |
|---|---|
| `entrada` | `estoque = estoque + quantidade` |
| `saida` | `estoque = estoque - quantidade` (mínimo 0) |
| `ajuste` | `estoque = quantidade` (valor absoluto) |

**Erros possíveis:**

| Status | Mensagem |
|---|---|
| 401 | `Token não enviado.` |
| 404 | `Material não encontrado.` |
| 422 | `Tipo inválido. Use: entrada, saida ou ajuste.` |
| 422 | `Quantidade deve ser maior que zero.` |
| 422 | `Quantidade maior que o estoque atual (X).` |

---

### 5.2 Histórico de Movimentações

```
GET http://localhost/inven/api.php?rota=movimentacoes
```

**Autenticação:** Sim  
**Parâmetros de query (opcionais):**

| Parâmetro | Tipo | Descrição |
|---|---|---|
| `tipo` | string | Filtra por tipo: `entrada`, `saida` ou `ajuste` |
| `material_id` | integer | Filtra por material específico |

**Resposta (200):**
```json
{
  "dados": [
    {
      "id": 3,
      "material_id": 1,
      "usuario_id": 1,
      "tipo": "entrada",
      "quantidade": "5.00",
      "observacao": "Compra nova coleção",
      "criado_em": "2025-03-10 15:00:00",
      "material": "Blusa P"
    }
  ]
}
```

> Retorna as últimas 100 movimentações em ordem cronológica decrescente.

---

## 6. Códigos de Status HTTP

| Código | Significado | Quando ocorre |
|---|---|---|
| 200 | OK | Requisição bem-sucedida |
| 201 | Created | Recurso criado com sucesso |
| 204 | No Content | Preflight CORS (OPTIONS) |
| 401 | Unauthorized | Token ausente, inválido ou expirado |
| 404 | Not Found | Rota ou recurso não encontrado |
| 409 | Conflict | E-mail já cadastrado |
| 422 | Unprocessable Entity | Dados inválidos ou campos obrigatórios faltando |
| 500 | Internal Server Error | Erro no servidor ou banco de dados |

---

## 7. Exemplos de Chamadas

### JavaScript (Fetch API) — usado no frontend

```javascript
// URL base da API
const API = 'http://localhost/inven/api.php';

// Função helper
async function api(method, rota, body = null) {
  const token = localStorage.getItem('inven_token');
  const opts = {
    method,
    headers: { 'Content-Type': 'application/json' }
  };
  if (token) opts.headers['Authorization'] = 'Bearer ' + token;
  if (body)  opts.body = JSON.stringify(body);

  const res = await fetch(`${API}?rota=${rota}`, opts);
  return await res.json();
}

// Exemplos de uso:
const login      = await api('POST', 'login', { email, senha });
const materiais  = await api('GET',  'materiais');
const novo       = await api('POST', 'materiais', { descricao, preco, fonte });
const editado    = await api('PUT',  `materiais/${id}`, { preco: 25 });
const deletado   = await api('DELETE', `materiais/${id}`);
const entrada    = await api('POST', `estoque/${id}/movimentar`, { tipo: 'entrada', quantidade: 10 });
const historico  = await api('GET',  'movimentacoes');
```

### cURL — para testes no terminal

```bash
# Cadastrar usuário
curl -X POST "http://localhost/inven/api.php?rota=cadastro" \
  -H "Content-Type: application/json" \
  -d '{"nome":"Maria Silva","email":"maria@email.com","senha":"123456"}'

# Login
curl -X POST "http://localhost/inven/api.php?rota=login" \
  -H "Content-Type: application/json" \
  -d '{"email":"maria@email.com","senha":"123456"}'

# Listar materiais (com token)
curl -X GET "http://localhost/inven/api.php?rota=materiais" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"

# Cadastrar material
curl -X POST "http://localhost/inven/api.php?rota=materiais" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{"descricao":"Blusa P","preco":20,"fonte":"Riachuelo","palavras_chave":["Blusa","Roupa"]}'

# Registrar entrada de estoque
curl -X POST "http://localhost/inven/api.php?rota=estoque/1/movimentar" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{"tipo":"entrada","quantidade":5,"observacao":"Compra inicial"}'

# Excluir material
curl -X DELETE "http://localhost/inven/api.php?rota=materiais/1" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```
