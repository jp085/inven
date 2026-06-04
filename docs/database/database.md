# Modelo de Dados do Sistema Inven

Este documento descreve o modelo de dados planejado para o sistema **Inven**.  
O objetivo é apresentar as entidades principais, seus relacionamentos, o diagrama ER e o dicionário de dados que servirão de base para a implementação do banco de dados.  
A modelagem foi pensada para garantir flexibilidade, integridade e escalabilidade, permitindo o gerenciamento eficiente de usuários, materiais e entre outros.

---

## Sumário

- [Modelo de dados](#modelo-de-dados)
- [Descrição das entidades e relacionamentos](#descrição-das-entidades-e-relacionamentos)
- [Diagrama ER ou similar](#diagrama-er-ou-similar)
- [Dicionário de dados](#dicionário-de-dados)

---

## Modelo de dados

O modelo de dados do projeto Inven foi planejado para garantir flexibilidade, integridade e escalabilidade. Utilizando MySQl como banco de dados SQL.

---

## Descrição das entidades e relacionamentos

O sistema é composto por 6 entidades principais:

PARTE PARA AJUSTAR...

- DONO/USUARIO: representa os usuários da plataforma.

- MATERIAIS: São os materiais criados pelos usuários.

- FONTE: Local onde foi adquirido o material/produto originalmente.

- PRECO: Representa o preço dos materiais.

- MOVI_MATE: Guarda todas as movimentações dos materiais.
- 
- ESTO_MATE: Guarda as informações de estoque.

- Relacionamentos:

  - Cada Material está relacionado dono/usuário.

  - Cada Material está relacionado a uma ou mais fontes.

  - Cada Material está relacionado a tabela de estoque.

---

## Diagrama ER ou similar

A imagem abaixo representa visualmente o modelo de dados e os relacionamentos entre as entidades do sistema:

<img alt="gráfico de modelo de dados" src="Inven_bd.png"/>

---

## Dicionário de dados

### USUARIOS

| Campo      | Tipo   | Descrição                          |
|------------|--------|------------------------------------|
| `idusers`      | int | Identificador único do usuário     |
| `nome`     | Varchar | Nome completo                      |
| `nomered`    | Varchar | Nome reduzido             |
| `senha`    | Varchar | Senha criptografada                |
| `data_cria`| date   | Data de criação do registro        |
| `data_atua`| date   | Data da última atualização         |

---

### MATERIAIS

| Campo           | Tipo    | Descrição                                      |
|-----------------|---------|-----------------------------------------------|
| `idmateriais`           | int  | Identificador único do material          |
| `descricaomate`          | Varchar  | Descrição dos materiais                         |
| `datacad`     | date  | Data de cadastro             |
| `dataalt`      | date  | Data de alteração/atualização             |
| `usuarios_idusers`      | int  | Chave estrangeira                            |

---

### FONTE

| Campo         | Tipo   | Descrição                          |
|---------------|--------|------------------------------------|
| `id_fonte`         | Varchar | Identificador único da fonte                      |
| `desc_mate`         | Varchar | Descrição do material                        |
| `preco_mate`      | double | Preço do material                             |
| `desc_fonte`      | Varchar | Descrição da fonte                   |

---

### ESTO_MATE

| Campo     | Tipo   | Descrição                          |
|-----------|--------|------------------------------------|
| `esto_quan`     | double | Quantidade de materiais em estoque      |
| `materiais_ idmateriais`    | int | Chave estrangeira                     |
| `desc_mate`  | Varchar | Descrição do material                    |
| `preco_mate`  | double | Preço do material                    |

---

### MOVI_MATE

| Campo    | Tipo   | Descrição                                   |
|----------|--------|---------------------------------------------|
| `doc_movi_mate` | int | Chave primária não-nula                   |
| `desc_mate`  | Varchar | Descrição do material         |
| `tipo_movi_mate`  | Varchar | Tipo de movimento         |
| `saldo_movi_mate`  | double | Valor movimentado         |
| `materiais_idmateriais`  | int | chave estrangeira         |
| `desc_user_movi_mate`  | Varchar | Usuário que realizou a movimentação        |
| `usuarios_idusers`  | int | Chave estrangeira        |
| `un_movi_mate`  | Varchar | Unidade de medida da movimentação       |



---
