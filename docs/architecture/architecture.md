    # Arquitetura do Sistema Inven

    Este documento descreve a arquitetura planejada para o sistema **Inven**.  
    O objetivo é apresentar de forma clara os componentes principais, os padrões arquiteturais que serão adotados, o diagrama de arquitetura e as decisões técnicas que fundamentarão o projeto.  
    A arquitetura foi pensada para garantir organização, escalabilidade, segurança e facilidade de manutenção, servindo como guia para a implementação na Etapa 2.

    ---

    ## Sumário

    - [Descrição da Arquitetura](#descrição-da-arquitetura)
    - [Componentes do Sistema](#componentes-do-sistema)
    - [Padrões Arquiteturais Utilizados](#padrões-arquiteturais-utilizados)
    - [Diagrama da Arquitetura](#diagrama-da-arquitetura)
    - [Decisões Técnicas e Justificativas](#decisões-técnicas-e-justificativas)

    ---

    ## Descrição da Arquitetura

    O projeto Inven adotará uma arquitetura baseada no padrão MVC (Model-View-Controller), adaptada para uma API RESTful desenvolvida em PHP com Laravel. A estrutura será planejada para garantir organização, escalabilidade e facilidade de manutenção. O backend será responsável por autenticação, gerenciamento de empreendimentos, integração com APIs externas e resposta em formato JSON para qualquer cliente HTTP (web ou mobile).

    ---

    ## Componentes do Sistema

    | Componente                 | Descrição                                                                 |
    |---------------------------|---------------------------------------------------------------------------|
    | Frontend Web/Mobile       | Interface de acesso para usuários (a ser desenvolvida na Etapa 2)         |
    | Backend (API RESTful)     | Responsável por autenticação, CRUD de materiais |
    | Banco de Dados (MySql)    | Armazenará usuários, materiais e dados normalizados                   |
    | Serviços Externos         | integração com outros sistemas com apis direcionadas       |
    | Middleware de Autenticação| Validará tokens JWT e protege rotas sensíveis                               |
    | Testes      | Cobrirá os principais fluxos com Jest e Supertest                      |

    ---

    ## Padrões Arquiteturais Utilizados

    MVC (Model-View-Controller): separação clara entre dados, lógica de negócio e rotas.

    RESTful API: uso semântico dos métodos HTTP (GET, POST, PUT, DELETE) e comunicação via JSON.

    Repository Pattern (implícito): abstração do acesso aos dados via Mongoose.

    JWT (JSON Web Token): autenticação segura e stateless.

    Modularização por responsabilidade: separação em controllers, models, routes, services, middleware, utils.

    ---

    ## Diagrama da Arquitetura

    <img alt="gráfico de arquitetura" src="Arquitetura.jpg"/>

    O diagrama apresentado representa a estrutura conceitual da aplicação Inven, destacando os principais componentes do sistema e suas interações. A arquitetura segue o padrão MVC (Model-View-Controller) adaptado para uma API RESTful que será desenvolvida com PHP e Laravel.

    ### Frontend

    Na parte superior do diagrama, temos o Frontend, que será desenvolvido na Etapa 2 utilizando HTML, CSS e JavaScript puro. Ele será responsável por consumir a API, enviando requisições HTTP e exibindo os dados recebidos em formato JSON. O frontend permitirá que usuários realizem buscas, cadastros e visualizações de materiais

    ### API REST (MVC)

    O núcleo da aplicação é a API RESTful, que será construída com o framework Laravel e organizada segundo o padrão MVC. Essa camada estará dividida em três principais controllers:

    **Usuários Controller**: Gerenciará o cadastro, login e autenticação dos usuários. Estará conectado ao modelo `Usuario.php`, que define a estrutura dos dados no MySQl.

    **Materiais Controller**: Responsável pelo CRUD de materiais cadastrados pelos usuários. Utilizará o modelo `materiais.php` para persistência dos dados.

    **Integrações Externas**: Englobará as apis para outros sistemas:

    ### Banco de Dados

    Os dados serão armazenados em um banco SQL, utilizando Mysql para modelagem e validação. Cada controller fará a interação com seu respectivo modelo para realizar operações de leitura, escrita, atualização e exclusão.

    ### Fluxo de Dados

    O fluxo representado no diagrama segue esta lógica:

    1 - O usuário interage com o frontend, que envia requisições HTTP para a API.

    2 - A API REST recebe a requisição e direciona para o controller correspondente.

    3 - O controller pode:

    - Interagir com o MySQl.

    4 - A resposta é montada em formato JSON e enviada de volta ao frontend.

    ---

    ## Decisões Técnicas e Justificativas

    - **PHP** foi escolhido pela leveza, popularidade e facilidade de modularização.

    - **MySQL** será adotado por sua flexibilidade na modelagem de dados.

    - **JWT** garantirá autenticação segura sem necessidade de sessões persistentes.

    - Arquitetura modular permitirá que cada parte do sistema evolua separadamente, facilitando manutenção e colaboração.

    - No frontend será usado **HTML**, **CSS** e **JS** puro,  garantindo responsividade para acesso via web e dispositivos móveis.

    ---
