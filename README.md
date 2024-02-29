# DESAFIO BACKEND

## Configuração do Ambiente

### Requisitos

- _PHP >= 8.0_ e [extensões](https://www.php.net/manual/pt_BR/extensions.php) (**não esquecer de instalar as seguintes extensões: _pdo_, _pdo_sqlite_ e _sqlite3_**);
- _SQLite_;
- _Composer_.

### Instalação

- Instalar dependências pelo composer com `composer install` na raiz do projeto;
- Servir a pasta _public_ do projeto através de algum servidor.
  (_Sugestão [PHP Built in Server](https://www.php.net/manual/en/features.commandline.webserver.)_)

## Sobre o Projeto

- O cliente XPTO Ltda. contratou seu serviço para realizar alguns ajustes em seu sistema de cadastro de produtos;
- O sistema permite o cadastro, edição e remoção de _produtos_ e _categorias de produtos_ para uma _empresa_;
- Para que sejam possíveis os cadastros, alterações e remoções é necessário um usuário administrador;
- O sistema possui categorias padrão que pertencem a todas as empresas, bem como categorias personalizadas dedicadas a uma dada empresa. As categorias padrão são: (`clothing`, `phone`, `computer` e `house`) e **devem** aparecer para todas as _empresas_;
- O sistema tem um relatório de dados dedicado ao cliente.

## Sobre a API

As rotas estão divididas em:

- _CRUD_ de _categorias_;
- _CRUD_ de _produtos_;
- Rota de busca de um _relatório_ que retorna um _html_.

**Atenção**, é bem importante que se adicione o _header_ `admin_user_id` com o id do usuário desejado ao acessar as rotas para simular o uso de um usuário no sistema.

A documentação da API se encontra na pasta `docs/api-docs.pdf`

- A documentação assume que a url base é `localhost:8000` mas você pode usar qualquer outra url ao configurar o servidor;
- O _header_ `admin_user_id` na documentação está indicado com valor `1` mas pode ser usado o id de qualquer outro usuário caso deseje (_pesquisando no banco de dados é possível ver os outros id's de usuários_).

Caso opte por usar o [Insomnia](https://insomnia.rest/) o arquivo para importação se encontra em `docs/insomnia-api.json`.
Caso opte por usar o [Postman](https://www.postman.com/) o arquivo para importação se encontra em `docs/postman-api.json`.

## Sobre o Banco de Dados

- O banco de dados é um _sqlite_ simples e já vem com dados preenchidos por padrão no projeto;
- O banco tem um arquivo de backup em `db/db-backup.sqlite` com o estado inicial do projeto caso precise ser "resetado".

## Demandas

Abaixo, as solicitações do cliente:

### Categorias

- [x] A categoria está vindo errada na listagem de produtos para alguns casos
      (_exemplo: produto `blue trouser` está vindo na categoria `phone` e deveria ser `clothing`_);

- [x] Alguns produtos estão vindo com a categoria `null` ao serem pesquisados individualmente (_exemplo: produto `iphone 8`_);
- [x] Cadastrei o produto `king size bed` em mais de uma categoria, mas ele aparece **apenas** na categoria `furniture` na busca individual do produto.

### Filtros e Ordenamento

Para a listagem de produtos:

- [x] Gostaria de poder filtrar os produtos ativos e inativos;
- [x] Gostaria de poder filtrar os produtos por categoria;
- [x] Gostaria de poder ordenar os produtos por data de cadastro.

### Relatório

- [x] O relatório não está mostrando a coluna de logs corretamente, se possível, gostaria de trazer no seguinte formato:
      (Nome do usuário, Tipo de alteração e Data),
      (Nome do usuário, Tipo de alteração e Data),
      (Nome do usuário, Tipo de alteração e Data)
      Exemplo:
      (John Doe, Criação, 01/12/2023 12:50:30),
      (Jane Doe, Atualização, 11/12/2023 13:51:40),
      (Joe Doe, Remoção, 21/12/2023 14:52:50)

### Logs

- [x] Gostaria de saber qual usuário mudou o preço do produto `iphone 8` por último.

### Extra

- [] Aqui fica um desafio extra **opcional**: _criar um ambiente com_ Docker _para a api_.

**Seu trabalho é atender às 7 demandas solicitadas pelo cliente.**

Caso julgue necessário, podem ser adicionadas ou modificadas as rotas da api. Caso altere, por favor, explique o porquê e indique as alterações nesse `README`.

Sinta-se a vontade para refatorar o que achar pertinente, considerando questões como arquitetura, padrões de código, padrões restful, _segurança_ e quaisquer outras boas práticas. Levaremos em conta essas mudanças.

Boa sorte! :)

## Suas Respostas, Duvidas e Observações

1. **Categoria Errada na Listagem de Produtos:**

- Exemplo: O produto `blue trouser` estava sendo exibido na categoria `phone` em vez de `clothing`.
    - Solução: Corrigido o erro nos Joins da consulta SQL, utilizando o ID correto.

2. **Produtos com Categoria `null` em Pesquisas Individuais:**

- Exemplo: Alguns produtos, como `iphone 8`, estavam apresentando a categoria `null`.
    - Solução: Modificada a função `getProductCategory()` para retornar o título da categoria. Ajustes feitos no método `getOne()`.

3. **Produto em Múltiplas Categorias não Exibido Corretamente:**
- Exemplo:_ O produto `king size bed` cadastrado em mais de uma categoria não aparecia corretamente na busca individual.
    - _Solução:_ Ajustado o método de busca para utilizar `fetchAll()` em vez de `fetch`, permitindo a exibição de todas as categorias associadas ao produto.

### Filtros e Ordenamento

- Adicionadas opções de parametro na função `getAll()` para permitir filtros na consulta.

### Relatório

-- Aprimorado o formato do relatório para incluir informações mais detalhadas, utilizando a consulta SQL ajustada em `getLog()`.

### Logs

- **Última Alteração de Preço do Produto `iphone 8`:**
   - _Solução:_ Adicionado filtros no relatório para identificar qual usuário foi responsável pela última alteração no produto que desejar`.
   - Resposta: `iphone 8` teve seu preço alterado pelo usuário rivers na data: 22/12/2023 18:08:12.

   