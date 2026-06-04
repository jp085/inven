**Este documento apresenta os principais endpoints da API Inven, os parâmetros esperados, formatos de resposta, autenticação e exemplos de chamadas. A API será planejada para facilitar o cadastro, consulta e gerenciamento para os empreendedores.**

---
## Sumário

1. [Endpoints Previstos](#endpoints-previstos)
2. [Parâmetros de Requisição](#parâmetros-de-requisição)
3. [Formatos de resposta](#formatos-de-resposta)
4. [Autenticação e Autorização](#autenticação-e-autorização)
5. [Exemplos de chamadas e respostas](#exemplos-de-chamadas-e-respostas)

## Endpoints Previstos

| Método | Rota                          | Descrição                                 | Protegida |
|--------|-------------------------------|-------------------------------------------|-----------|
| POST   | /usuarios/cadastroUsuario     | Cadastro de novo usuário                  | Não       |
| POST   | /usuarios/login               | Autenticação e geração de token JWT       | Não       |
| POST   | /empreendimentos              | Cadastro de materiais                     | Sim       |
| PUT    | /empreendimentos/:id          | Edição de materiais                       | Sim       |
| DELETE | /empreendimentos/:id          | Exclusão de mateirias                     | Sim       |
| GET    | /empreendimentos              | Listagem e busca de materiais             | Não       |

---

## Parâmetros de Requisição

### Headers

```http
Content-Type: application/json
Authorization: Bearer SEU_TOKEN_JWT  // Apenas para rotas protegidas
```

### Body (JSON)

- Selecione a opção raw

- Escolha o tipo JSON

- Insira os dados conforme os exemplos abaixo

## Formatos de resposta

Todas as respostas da API seguirão o formato JSON, com estrutura padronizada: 

```json
{
  "mensagem": "Operação realizada com sucesso",
  "dados": {
    "Conteúdo retornado"
  }
}
```
### Em caso de erro:

```json
{
  "erro": "Descrição do erro"
}
```
---

## Autenticação e Autorização

A API utilizará JWT (JSON Web Token) para autenticação. Após o login, o token deverá ser incluído no header das requisições protegidas:

```Http
Authorization: Bearer SEU_TOKEN_JWT
```
- Rotas públicas: /usuarios/cadastroUsuario, /usuarios/login, /materiais (GET)

- Rotas protegidas: /materiais (POST, PUT, DELETE)

### Integração com o Frontend

No frontend, o token será armazenado no localStorage após o login do usuário. Para chamadas às rotas protegidas, o token será recuperado e incluído no header da requisição utilizando a função fetch.

Exemplo de uso com fetch:

```javascript
const token = localStorage.getItem("token");

fetch("/materiais", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Authorization": `Bearer ${token}`
  },
  body: JSON.stringify({
    nome: "Blusa p",
    quantidade: "10",
    preco: "20",
    // demais campos...
  })
});
```
Essa abordagem garantirá que apenas usuários autenticados possam acessar funcionalidades sensíveis, como cadastro, edição e exclusão de materiais.


---

## Exemplos de chamadas e respostas

### Cadastro de Usuário

**POST /usuarios/cadastroUsuario**

Requisição:

```json
{
  "nome": "Teste",
  "email": "teste@email.com",
  "senha": "123456"
}
```

Resposta esperada:

```json
{
  "mensagem": "Usuário cadastrado com sucesso",
  "_id": "68b4d4cdb98dfb4abe6addff",
  "nome": "Teste Usuário",
  "email": "teste@teste.com",
  "token": "TOKEN_JWT_GERADO"
}
```

### Login

**POST /usuarios/login**

Requisição:

```json
{
  "email": "teste@email.com",
  "senha": "123456"
}
```

Resposta esperada:

```json
{
  "_id": "689fb6a91270e26fb9e6b14a",
  "nome": "Teste Usuário",
  "email": "teste@teste.com",
  "token": "TOKEN_JWT_GERADO"
}
```

### Cadastro de Materiais

**POST /Materiais**

Requisição:

```json
{
  "descricao": "Blusa P",
  "preco"    : "20",
  "fonte"    : "Riachuelo",
  "telefone": "85998989999",
  "email": "Riachuelo.com",
  "palavrasChave": ["Blusa", "p", "vestimenta"]
}
```

### Edição de Materiais

**PUT /Materiais/:id**

Requisição (exemplo de múltiplos campos):

```json
{
  "descricao": "Blusa P",
  "fonte"    : "Ricardo almeida",
  },
  "palavrasChave": ["Blusa P", "Roupa", "Vestimena"]
}
```
### Exclusão de materiais

**DELETE /materiais/:id**

Requisição:

```http
DELETE /material/ID_DO_MATERIAL
Authorization: Bearer SEU_TOKEN_JWT
```
Resposta esperada:

```json
{
  "mensagem": "Material deletado com sucesso"
}
```

### 🔍 Listagem e Busca de Empreendimentos

**GET /Materiais**

A API permitirá listar todos os Materiais cadastrados e realizará buscas específicas utilizando filtros via query params. Abaixo estão os principais filtros planejados:

| Filtro           | Exemplo de Requisição                                               |
|------------------|----------------------------------------------------------------------|
| Material         | `/empreendimentos?material=blusa`            |
| fonte            | `/empreendimentos?fonte=riachuelo`                                     |
| Palavra-chave    | `/empreendimentos?palavra=vestimenta`                                     |

**Resposta esperada:**  

Ao utilizar o query params GET `/material?fonte=riachuelo` a API retornará os dados dos materiais separados por fonte, exibindo também os preços.


```json
[
  {
    "fonte"    : "riachuelo",
    "material" : "Blusa P",
    "preco"    : "80",
    }
    .
    .  
    .
]
```

### Respostas de Erro (Padronizadas)

```json
{
  "erro": "Material já cadastrado"
}
```

```json
{
  "erro": "Credenciais inválidas"
}
```

```json
{
  "erro": "Token inválido ou expirado"
}
```

```json
{
  "erro": "Usuário não autorizado para esta ação"
}
```

```json
{
  "erro": "Erro ao editar material",
  "camposObrigatorios": [
    "material.preco",
    "matrial.descricao",
    "material.fonte",
  ],
  "mensagem": "Os seguintes campos são obrigatórios e não podem estar em branco: material.preco, material.descricao, material.fonte"
}
```

### Tratamento de Erros no Frontend

As mensagens de erro retornadas pela API serão exibidas ao usuário por meio de alertas ou componentes visuais.  
Exemplo com `fetch`:

```javascript
fetch("/usuarios/login", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ email, senha })
})
.then(res => res.json())
.then(data => {
  if (data.erro) {
    alert(data.erro); // ou exibir em um componente de erro
  } else {
    localStorage.setItem("token", data.token);
  }
});
```
### Validação no Frontend

Antes de enviar dados para a API, o frontend fará validações como:

- Garantir que a senha tenha no mínimo 6 caracteres.

- Impedir envio de campos obrigatórios em branco.


### Segurança de Origem (CORS)

Durante a implantação, o backend será configurado para aceitar requisições apenas do domínio oficial do frontend, utilizando políticas de CORS. Isso evita que aplicações externas não autorizadas consumam a API.

### Expiração de Sessão
Se a API retornar erro de token inválido ou expirado, o frontend deverá:
1. Limpar o token do `localStorage`.
2. Redirecionar o usuário para a página de login.
3. Exibir mensagem informando que a sessão expirou.

--- 
