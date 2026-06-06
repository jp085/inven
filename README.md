# Inven — Sistema de Controle de Estoque

> Sistema web de controle de estoque desenvolvido para pequenos e médios empreendedores, permitindo o gerenciamento de materiais, entradas e saídas de estoque e histórico de movimentações de forma simples e acessível via navegador.

---

## Sumário

1. [Problema Social Atendido e Justificativa](#-problema-social-atendido-e-justificativa)
2. [Funcionalidades Implementadas](#-funcionalidades-implementadas)
3. [Tecnologias Utilizadas](#-tecnologias-utilizadas)
4. [Arquitetura do Sistema](#-arquitetura-do-sistema)
5. [Instalação e Execução](#-instalação-e-execução)
6. [Credenciais de Teste](#-credenciais-de-teste)
7. [Deploy e Repositório](#-deploy-e-repositório)
8. [Validação com Público-Alvo](#-validação-com-público-alvo)
9. [Equipe](#-equipe)

---

## 🌆 Problema Social Atendido e Justificativa

O projeto **Inven** está alinhado ao **Objetivo de Desenvolvimento Sustentável nº 11 da ONU (ODS 11 — Cidades e Comunidades Sustentáveis)**, que busca tornar as cidades inclusivas, seguras, resilientes e sustentáveis.

### Problema Identificado

Pequenos empreendedores — feirantes, costureiras, revendedores, lojistas informais — frequentemente gerenciam seus estoques de forma manual (cadernos, planilhas improvisadas ou "de cabeça"). Essa prática leva a:

- Perdas financeiras por falta de controle de entradas e saídas
- Rupturas de estoque que causam perda de vendas e clientes
- Decisões de compra equivocadas por ausência de histórico confiável
- Dificuldade para crescer e profissionalizar o negócio

### Como o Inven atende à ODS 11

- **Fortalece** a capacidade produtiva de pequenos comerciantes locais, tornando-os mais competitivos dentro do ecossistema econômico urbano
- **Reduz** o desperdício gerado por rupturas de estoque ou excesso de produtos, contribuindo para um consumo mais consciente
- **Democratiza** o acesso a ferramentas de gestão profissional, historicamente restritas a grandes empresas, promovendo inclusão econômica e social
- **Apoia** a formalização de pequenos negócios, favorecendo a resiliência financeira de comunidades locais

---

## ✅ Funcionalidades Implementadas

### Autenticação e Controle de Acesso
- Cadastro de usuário com validação de e-mail e senha segura (hash bcrypt)
- Login com geração de token JWT (validade de 7 dias)
- Proteção de rotas via middleware de autenticação
- Logout com limpeza de sessão no frontend

### Gestão de Materiais
- Cadastro completo: descrição, preço, fonte/fornecedor, telefone, e-mail e palavras-chave
- Edição e exclusão de materiais com confirmação
- Busca e filtragem por descrição, fonte ou palavras-chave
- Listagem com indicadores visuais de status de estoque

### Controle de Estoque
- Visualização do estoque atual de cada material em cards individuais
- Ajuste rápido (+/-) diretamente na tela de estoque
- Modal de ajuste detalhado com tipo (entrada / saída / ajuste manual), quantidade e observação
- Alertas visuais para itens com estoque baixo (≤ 4 unidades) ou zerado
- Cálculo automático do valor total em estoque

### Movimentações
- Registro de entradas, saídas e ajustes manuais
- Histórico completo com data, tipo, quantidade e observação
- Validação que impede saída maior que o estoque disponível

### Dashboard
- Painel com métricas em tempo real: total de materiais, itens em estoque, valor estimado e itens zerados
- Tabela resumo dos materiais cadastrados

---

## 🛠 Tecnologias Utilizadas

| Tecnologia | Versão | Finalidade |
|---|---|---|
| PHP | 8.2.12 | Backend — lógica de negócio e API REST |
| MySQL | 8.0.46 | Banco de dados relacional |
| HTML5 | — | Estrutura do frontend |
| CSS3 | — | Estilização e layout responsivo |
| JavaScript | ES2022 | Interatividade e consumo da API |
| XAMPP | 8.2.12 | Servidor de desenvolvimento (Apache + PHP + MySQL) |
| PDO | nativo PHP | Abstração de acesso ao banco de dados |
| JWT | implementação própria | Autenticação stateless segura |
| Git / GitHub | — | Controle de versão e repositório |

---

## 🏗 Arquitetura do Sistema

O sistema adota uma arquitetura **cliente-servidor simplificada**, com frontend e backend comunicando-se via API REST em formato JSON.

```
Frontend (index.html)
      │
      │  HTTP Request + Bearer Token (JSON)
      ▼
Backend (api.php)
      │
      ├── Valida JWT
      ├── Processa a requisição
      │
      ▼
MySQL (inven_db)
      │
      ▼
Resposta JSON → Frontend
```

### Frontend
- Desenvolvido em HTML, CSS e JavaScript puro em um único arquivo `index.html`
- Gerencia todas as telas via manipulação do DOM (login, cadastro, dashboard, materiais, estoque e movimentações)
- Armazena o token JWT no `localStorage` e o inclui no header `Authorization` de todas as requisições autenticadas
- Comunica-se com o backend exclusivamente via Fetch API

### Backend
- API REST desenvolvida em PHP puro (sem frameworks) concentrada em um único arquivo `api.php`
- Roteamento via query string (`?rota=`) para compatibilidade máxima com XAMPP
- JWT implementado manualmente sem bibliotecas externas
- Acesso ao banco via PDO com prepared statements (proteção contra SQL Injection)

### Banco de Dados
- 4 tabelas: `usuarios`, `materiais`, `palavras_chave` e `movimentacoes`
- Integridade referencial garantida por chaves estrangeiras com `ON DELETE CASCADE`
- Senhas armazenadas com hash bcrypt

---

## 🚀 Instalação e Execução

### Pré-requisitos
- [XAMPP](https://www.apachefriends.org/) com PHP 8.2+ e MySQL 8.0+
- Navegador web moderno (Chrome, Firefox, Edge)

### Passo 1 — Clonar o repositório

```bash
git clone https://github.com/jp085/inven.git
```

Ou baixe o ZIP diretamente no GitHub e extraia.

### Passo 2 — Copiar para o htdocs

Copie a pasta `inven` para dentro do diretório do XAMPP:

```
C:\xampp\htdocs\inven\
    index.html   ← frontend
    api.php      ← backend
    banco.sql    ← script do banco
    .htaccess    ← configuração do Apache
```

### Passo 3 — Iniciar o XAMPP

- Abra o **XAMPP Control Panel**
- Clique em **Start** no **Apache**
- Clique em **Start** no **MySQL**

### Passo 4 — Criar o banco de dados

1. Acesse `http://localhost/phpmyadmin`
2. Clique na aba **SQL**
3. Cole o conteúdo do arquivo `banco.sql`
4. Clique em **Executar**

### Passo 5 — Acessar o sistema

```
http://localhost/inven/index.html
```

---

## 🔑 Credenciais de Teste

O sistema não possui usuário padrão pré-cadastrado. Para testar, crie uma conta diretamente na tela de cadastro:

| Campo | Valor |
|---|---|
| E-mail | qualquer e-mail válido (ex: `teste@email.com`) |
| Senha | mínimo 6 caracteres (ex: `123456`) |

Após o cadastro, o login acontece automaticamente e o sistema está pronto para uso.

---

## 🔗 Deploy e Repositório

- **Repositório GitHub:** https://github.com/jp085/inven
- **Deploy em produção:** não realizado nesta etapa — o sistema é executado localmente via XAMPP
- **Vídeo demonstrativo:** a ser produzido e vinculado ao repositório

---

## 🧪 Validação com Público-Alvo

A validação formal com o público-alvo (pequenos empreendedores) está prevista para etapa posterior ao desenvolvimento. As decisões de design e funcionalidades foram baseadas nos seguintes insumos:

- Levantamento de requisitos orientado ao contexto de pequenos comerciantes (feirantes, revendedores, costureiras)
- Análise das principais dores identificadas: falta de controle de estoque, perda de vendas por ruptura e ausência de histórico de movimentações
- Validação interna entre os membros da equipe simulando cenários reais de uso

**Próximos passos:** aplicar testes de usabilidade com ao menos 3 pequenos empreendedores do contexto local, coletar feedback e iterar sobre a interface e funcionalidades.

---

## 👥 Equipe

| Matrícula | Nome | Papel |
|---|---|---|
| 2222876 | Paulo Rafael Baima Cavalcante | Backend PHP / Banco de Dados |
| 2326329 | Thamires Guedes Moura Lopes | Frontend / UI Design |
| 2317792 | Narcelio Barbosa da Costa | Banco de Dados / Documentação |
| 2323778 | João Paulo Gomes dos Santos | Frontend / Integração API |
| 2124682 | Gabriel Eduardo Brasil | Arquitetura / Testes |
| 2418803  | Marcos Aurélio Sousa de Carvalho | Backend PHP / Banco de dados |

---

*Inven · Análise e Desenvolvimento de Sistemas · UNIFOR · 2025*
