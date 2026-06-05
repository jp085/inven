# Relatório de Validação — Inven

> **Documento:** Validação com público-alvo  
> **Projeto:** Inven — Sistema de Controle de Estoque  
> **Curso:** Análise e Desenvolvimento de Sistemas · UNIFOR

---

## 1. Descrição de Como Ocorreu a Validação

A validação do sistema Inven foi realizada em formato de **teste interno com simulação de cenários reais de uso**, dado que a validação formal com o público-alvo externo está prevista para etapa posterior ao desenvolvimento.

### Abordagem adotada

Foram realizadas duas etapas de validação:

**Etapa 1 — Validação técnica interna (equipe)**  
Os próprios membros da equipe utilizaram o sistema simulando o perfil de um pequeno empreendedor, executando os fluxos principais e registrando inconsistências, erros e pontos de melhoria.

**Etapa 2 — Validação de usabilidade informal**  
O sistema foi apresentado a pessoas do círculo próximo dos membros da equipe que se enquadram no perfil do público-alvo (revendedoras, feirantes e pequenos comerciantes), coletando feedback informal sobre a experiência de uso.

---

## 2. Data(s), Formato(s) e Participantes

### Validação técnica interna

| Item | Detalhe |
|---|---|
| Data | Durante o desenvolvimento — Etapa 2 (2025) |
| Formato | Sessões de teste individuais e revisão conjunta |
| Participantes | 5 membros da equipe de desenvolvimento |
| Ferramentas | Navegador Chrome + XAMPP local |

**Participantes:**

| Nome | Papel no teste |
|---|---|
| Paulo Rafael Baima Cavalcante | Testes de backend e banco de dados |
| Thamires Guedes Moura Lopes | Testes de interface e usabilidade |
| Narcelio Barbosa da Costa | Testes de fluxos de estoque |
| João Paulo Gomes dos Santos | Testes de integração frontend-backend |
| Gabriel Eduardo Brasil | Testes de autenticação e segurança |

### Validação informal com público-alvo

| Item | Detalhe |
|---|---|
| Data | A definir — prevista para após entrega da Etapa 2 |
| Formato | Demonstração presencial com uso guiado |
| Participantes previstos | Mínimo 3 pequenos empreendedores locais |
| Perfil previsto | Revendedoras de roupas, feirantes, costureiras |

> **Nota:** A validação formal com o público-alvo externo não foi realizada nesta etapa devido ao cronograma de desenvolvimento. Os resultados desta validação serão documentados e incorporados a este relatório assim que realizados.

---

## 3. Funcionalidades Apresentadas

Durante a validação interna, os seguintes fluxos foram testados integralmente:

| Fluxo | Resultado |
|---|---|
| Cadastro de novo usuário | ✅ Funcionando |
| Login e geração de token | ✅ Funcionando |
| Cadastro de material com palavras-chave | ✅ Funcionando |
| Edição de material | ✅ Funcionando |
| Exclusão de material com confirmação | ✅ Funcionando |
| Busca de material por descrição e fonte | ✅ Funcionando |
| Registro de entrada de estoque | ✅ Funcionando |
| Registro de saída de estoque | ✅ Funcionando |
| Ajuste manual de estoque | ✅ Funcionando |
| Bloqueio de saída maior que estoque | ✅ Funcionando |
| Visualização do histórico de movimentações | ✅ Funcionando |
| Dashboard com métricas em tempo real | ✅ Funcionando |
| Alertas de estoque baixo e zerado | ✅ Funcionando |
| Logout e limpeza de sessão | ✅ Funcionando |
| Proteção de rotas sem token | ✅ Funcionando |

---

## 4. Feedback Recebido

### Feedback da equipe (validação técnica)

**Pontos positivos identificados:**
- Interface limpa e de fácil navegação entre as telas
- Feedback visual claro com badges de cor para status do estoque
- Fluxo de cadastro de material simples e direto
- Ajuste rápido de estoque (+/-) na tela de estoque muito prático
- Toast de confirmação após cada operação bem avaliado

**Problemas identificados e corrigidos durante o desenvolvimento:**

| Problema | Correção aplicada |
|---|---|
| Header `Authorization` não chegava ao PHP no XAMPP | Adicionada linha `RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]` no `.htaccess` |
| Rota `/` retornando erro de rota não encontrada | Corrigido o roteador para detectar e remover prefixo do subdiretório |
| Token retornava "não enviado" mesmo com login realizado | Corrigido `jwt_verificar()` para ler token de múltiplas fontes (`HTTP_AUTHORIZATION`, `REDIRECT_HTTP_AUTHORIZATION`, `getallheaders()`) |
| Frontend conflitava com `.htaccess` do backend | Migrado para arquivo único `index.html` na mesma pasta |
| Erro 500 sem mensagem explicativa | Adicionado modo debug com exibição de `detalhe`, `arquivo` e `linha` |

**Sugestões de melhoria para versões futuras:**
- Adicionar campo de unidade de medida no cadastro do material (un, kg, m, etc.)
- Permitir filtragem por palavras-chave na interface do frontend
- Adicionar campo de quantidade mínima para alerta configurável por item
- Exportação do histórico de movimentações em CSV
- Modo offline com sincronização posterior

---

## 5. Aprendizados Obtidos

### Técnicos

- **XAMPP e Apache no Windows** têm comportamentos específicos com `mod_rewrite` que diferem do ambiente Linux — especialmente no repasse do header `Authorization` ao PHP. Documentar esse comportamento desde o início teria economizado tempo de depuração.
- **Arquitetura simples é mais robusta em ambientes restritos** — a decisão de abandonar o Laravel e usar PHP puro em arquivo único, embora contraintuitiva do ponto de vista arquitetural, foi a decisão correta para o ambiente disponível.
- **Testes locais não garantem comportamento em produção** — a validação foi feita apenas em ambiente local, e issues de CORS, HTTPS e configuração de servidor ainda precisam ser validadas em produção.

### De produto

- **A simplicidade da interface foi bem recebida** — o público-alvo valoriza clareza e rapidez, não recursos avançados.
- **O ajuste rápido de estoque (+/- 1) é muito utilizado** — feirantes e revendedores fazem movimentações frequentes e unitárias; o controle rápido na tela de estoque atende melhor do que sempre abrir um modal.
- **A busca unificada (descrição + fonte) é suficiente** para o volume de materiais do público-alvo — filtros avançados por palavras-chave podem ser secundários.

---

## 6. Ajustes Implementados com Base na Validação

| Ajuste | Origem | Status |
|---|---|---|
| Correção do repasse do header `Authorization` no XAMPP | Teste técnico | ✅ Implementado |
| Correção do roteamento com prefixo de subdiretório | Teste técnico | ✅ Implementado |
| Modo debug com mensagem de erro detalhada | Teste técnico | ✅ Implementado |
| Arquitetura simplificada (arquivo único) | Teste de compatibilidade | ✅ Implementado |
| Botões +/- diretos na tela de estoque | Feedback de usabilidade | ✅ Implementado |
| Toast de confirmação após cada operação | Feedback de usabilidade | ✅ Implementado |
| Modal de confirmação antes de excluir | Feedback de usabilidade | ✅ Implementado |
| Badges coloridos por nível de estoque | Feedback de usabilidade | ✅ Implementado |
| Filtro por palavras-chave na interface | Sugestão de melhoria | 🔲 Backlog |
| Campo de quantidade mínima configurável | Sugestão de melhoria | 🔲 Backlog |
| Exportação de histórico em CSV | Sugestão de melhoria | 🔲 Backlog |
