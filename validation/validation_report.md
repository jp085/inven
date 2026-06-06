# Relatório de Validação — Inven

> **Documento:** Validação com público-alvo
> **Projeto:** Inven — Sistema de Controle de Estoque
> **Curso:** Análise e Desenvolvimento de Sistemas · UNIFOR

---

## 1. Descrição de Como Ocorreu a Validação

A validação do sistema Inven foi realizada em duas etapas:

**Etapa 1 — Validação técnica interna (equipe)**
Os membros da equipe utilizaram o sistema simulando cenários reais de uso, executando todos os fluxos principais e registrando inconsistências, erros e pontos de melhoria ao longo do desenvolvimento.

**Etapa 2 — Validação presencial com a empreendedora**
Membros da equipe visitaram presencialmente o estabelecimento da empreendedora Marta Elena Silva de Sousa, apresentaram o sistema em funcionamento diretamente no local de trabalho dela e coletaram feedback em tempo real durante o uso.

---

## 2. Data(s), Formato(s) e Participantes

### Validação presencial com público-alvo

| Item | Detalhe |
|---|---|
| **Data** | Junho de 2026 |
| **Formato** | Visita presencial ao estabelecimento comercial |
| **Local** | Loja da empreendedora — Fortaleza, Ceará |
| **Duração** | Aproximadamente 1 hora |
| **Participantes da equipe** | João Paulo Gomes dos Santos, Narcelio Barbosa da Costa |

### Participante — público-alvo

| Campo | Informação |
|---|---|
| **Nome** | Marta Elena Silva de Sousa |
| **CNPJ** | 18.471.321/0001-30 |
| **Tipo de negócio** | Comércio de roupas, acessórios e artigos diversos |
| **Porte** | Microempreendedora Individual (MEI) |
| **Localização** | Maracanaú, Ceará |

### Validação técnica interna

| Item | Detalhe |
|---|---|
| **Período** | Durante o desenvolvimento — Etapa 2 (2025) |
| **Formato** | Sessões de teste individuais e revisão conjunta |
| **Participantes** | 5 membros da equipe de desenvolvimento |

---

## 3. Funcionalidades Apresentadas

Durante a visita presencial, as seguintes funcionalidades foram demonstradas e utilizadas pela empreendedora:

| Funcionalidade | Como foi apresentada |
|---|---|
| Login e cadastro de usuário | Empreendedora acompanhou o processo de criação de conta |
| Dashboard com métricas | Apresentação do painel com indicadores de estoque |
| Cadastro de materiais | Cadastro de produtos reais da loja durante a visita |
| Controle de estoque (+/-) | Demonstração de entrada e saída de peças |
| Alertas de estoque baixo | Apresentação dos indicadores visuais por cores |
| Histórico de movimentações | Visualização do registro de entradas e saídas |
| Busca de materiais | Demonstração da busca por nome e fornecedor |

### Evidências fotográficas

As fotos a seguir registram a visita e a utilização do sistema pela empreendedora:

![Empreendedora organizando o estoque de roupas](evidence/foto1.jpg)
*Marta Elena organizando o estoque da loja antes da demonstração do sistema*

![Vista geral da loja](evidence/foto2.jpg)
*Vista geral do estabelecimento — roupas, acessórios e artigos diversos*

![Membro da equipe com a empreendedora](evidence/foto3.jpg)
*Membro da equipe com a empreendedora Marta Elena no local de validação*

![Membro da equipe configurando o sistema](evidence/foto4.jpg)
*Configuração e instalação do sistema no ambiente da loja*

![Empreendedora usando o sistema](evidence/foto5.jpg)
*Marta Elena utilizando o sistema Inven — tela de Materiais*

![Demonstração do sistema](evidence/foto6.jpg)
*Membro da equipe apresentando as funcionalidades do sistema para a empreendedora*

---

## 4. Feedback Recebido

### Feedback da empreendedora (validação presencial)

**Pontos positivos:**
- A empreendedora demonstrou interesse imediato ao ver o sistema funcionando com dados reais da sua loja
- Aprovou a visualização do estoque por cards com indicadores de cor (verde/amarelo/vermelho), considerando intuitiva
- Gostou dos botões +/- para ajuste rápido de estoque, comentando que seria prático no dia a dia
- Considerou o dashboard útil para ter uma visão geral rápida do negócio
- Elogiou a possibilidade de registrar o nome do fornecedor junto ao produto

**Dificuldades observadas:**
- Teve dificuldade inicial para entender o conceito de "movimentação" — foi necessário explicar a diferença entre entrada, saída e ajuste com exemplos do próprio negócio dela
- Sentiu falta de campo para registrar o tamanho/variação das peças (P, M, G, GG), pois vende roupas em diferentes tamanhos
- Perguntou se o sistema funcionaria no celular, mostrando preferência pelo uso mobile

**Sugestões dadas pela empreendedora:**
- Adicionar campo de tamanho/variação no cadastro do produto
- Ter acesso pelo celular sem precisar de computador
- Poder registrar o lucro esperado por peça

### Feedback da equipe (validação técnica interna)

**Problemas identificados e corrigidos:**

| Problema | Correção aplicada |
|---|---|
| Header `Authorization` não chegava ao PHP no XAMPP | Adicionada linha `RewriteRule .* - [E=HTTP_AUTHORIZATION:...]` no `.htaccess` |
| Rota retornando erro de prefixo de subdiretório | Corrigido o roteador para detectar e remover prefixo automaticamente |
| Token retornando "não enviado" mesmo após login | Corrigido `jwt_verificar()` para ler token de múltiplas fontes |
| Frontend conflitando com `.htaccess` do backend | Migrado para arquivo único `index.html` na mesma pasta |

---

## 5. Aprendizados Obtidos

### Técnicos

- **Ambiente de desenvolvimento importa** — problemas de compatibilidade entre XAMPP/Windows e o sistema de roteamento PHP consumiram tempo significativo que não estava no planejamento
- **Simplicidade é uma funcionalidade** — a decisão de usar PHP puro em arquivo único, embora contraintuitiva, foi a mais acertada para o ambiente disponível
- **Testar com o usuário real revela requisitos invisíveis** — o campo de tamanho/variação das peças, por exemplo, só foi identificado como necessidade durante a visita presencial

### De produto e extensão

- **O contato com o público-alvo transforma a perspectiva** — ver a empreendedora usando o sistema no próprio balcão da loja deu concretude ao projeto que nenhuma documentação consegue transmitir
- **Linguagem técnica precisa ser traduzida** — termos como "movimentação" e "fonte" precisam de explicação clara para usuários sem familiaridade com sistemas de gestão
- **O celular é o dispositivo preferido** — a empreendedora perguntou imediatamente sobre acesso mobile, confirmando que uma versão responsiva para smartphone seria prioritária em versões futuras
- **Procrastinação tem custo alto** — atrasos nas reuniões de equipe e no desenvolvimento geraram pressão no final, comprometendo funcionalidades que poderiam ter sido entregues com mais qualidade

---

## 6. Ajustes Implementados com Base na Validação

| Ajuste | Origem | Status |
|---|---|---|
| Correção do header Authorization no XAMPP | Teste técnico interno | ✅ Implementado |
| Correção do roteamento com prefixo de subdiretório | Teste técnico interno | ✅ Implementado |
| Botões +/- diretos na tela de estoque | Feedback de usabilidade interno | ✅ Implementado |
| Toast de confirmação após cada operação | Feedback de usabilidade interno | ✅ Implementado |
| Modal de confirmação antes de excluir | Feedback de usabilidade interno | ✅ Implementado |
| Badges coloridos por nível de estoque (verde/amarelo/vermelho) | Validação com empreendedora | ✅ Implementado |
| Campo de tamanho/variação no cadastro | Sugestão da empreendedora | 🔲 Backlog — versão futura |
| Interface mobile (responsiva para celular) | Sugestão da empreendedora | 🔲 Backlog — versão futura |
| Registro de margem de lucro por produto | Sugestão da empreendedora | 🔲 Backlog — versão futura |
| Explicação inline dos termos técnicos na UI | Dificuldade observada na validação | 🔲 Backlog — versão futura |
