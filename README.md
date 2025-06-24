# Desafio Técnico: CRUD com Laravel e Vue.js

Este projeto é a resolução de um desafio técnico que implementa um CRUD (Create, Read, Update, Delete) completo para gerenciamento de **Usuários**, **Perfis** e **Endereços**.

A solução é dividida em duas aplicações independentes e desacopladas:
* Um **Backend** desenvolvido com **Laravel** (atuando como uma API RESTful).
* Um **Frontend** desenvolvido com **Vue.js** (como uma Single Page Application - SPA).

---

## 🚀 Tecnologias Utilizadas

### Backend (API RESTful)

* **Linguagem:** PHP 8.x
* **Framework:** Laravel 11.x
* **Banco de Dados:** MySQL (utilizando XAMPP)
* **Gerenciador de Dependências:** Composer

### Frontend (Single Page Application - SPA)

* **Framework:** Vue.js 3.x
* **Ferramenta de Build:** Vite
* **Requisições HTTP:** Axios
* **Roteamento:** Vue Router
* **Máscaras de Input:** Vue The Mask
* **Gerenciador de Pacotes:** NPM (Node Package Manager)

---

## 🎯 Funcionalidades Implementadas

* **CRUD Completo:**
    * **Usuários:** Listar (com filtros), Cadastrar, Editar, Detalhar, Excluir.
    * **Perfis:** Listar, Cadastrar, Editar, Excluir.
    * **Endereços:** Listar, Cadastrar, Editar, Excluir.
* **Tela de Pesquisa de Usuários:** Filtros por Nome, CPF e Período de cadastro.
* **Relações de Entidades:**
    * **Usuário N:1 Perfil:** Um usuário está vinculado a um único perfil (e um perfil pode ter vários usuários).
    * **Usuário N:M Endereço:** Um usuário pode ter múltiplos endereços, e um mesmo endereço pode ser vinculado a vários usuários (relação muitos-para-muitos via tabela pivô `address_user`).
* **Validações Robustas:**
    * **Backend:** Validações para campos obrigatórios, formato de e-mail, e unicidade para **Nome**, **Email** e **CPF** de usuários, além de unicidade para o **Nome** do perfil.
    * **Frontend:** Máscaras automáticas para campos **CPF** (`###.###.###-##`) e **CEP** (`#####-###`).
* **Feedback ao Usuário:** Mensagens claras de sucesso e tratamento detalhado de erros (conexão, servidor, validação) exibidas através de `alert()`s informativos e logs detalhados no console do navegador para depuração.

---

## 📦 Como Rodar o Projeto (Passo a Passo)

### 1. Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas e configuradas em seu ambiente:

* **XAMPP (ou ambiente PHP/MySQL similar):** Com Apache e MySQL em execução.
* **Composer:** [https://getcomposer.org/](https://getcomposer.org/)
* **Node.js (versão LTS recomendada) e NPM (ou Yarn):** [https://nodejs.org/](https://nodejs.org/)
* **Git:** [https://git-scm.com/](https://git-scm.com/)
* **Editor de Código:** VS Code (ou sua preferência).

### 2. Configuração do Backend (Laravel API)

1.  **Clone o repositório:**
    Abra seu terminal na pasta onde deseja armazenar o projeto (ex: `C:\xampp\htdocs` se for usar o Apache do XAMPP) e execute:
    ```bash
    git clone [https://github.com/ViictorM0ura/desafio-tecnico-laravel-vue-crud.git](https://github.com/ViictorM0ura/desafio-tecnico-laravel-vue-crud.git)
    cd desafio-tecnico-laravel-vue-crud
    ```
    *Obs: A pasta `desafio-tecnico-laravel-vue-crud` agora contém as duas subpastas: `crud-vue-laravel-backend` e `crud-vue-laravel-frontend`.*

2.  **Navegue para a pasta do backend:**
    ```bash
    cd crud-vue-laravel-backend
    ```

3.  **Instale as dependências do Composer:**
    ```bash
    composer install
    ```

4.  **Configure o arquivo de ambiente (`.env`):**
    * Copie o arquivo de exemplo:
        ```bash
        cp .env.example .env
        ```
    * Abra o arquivo `.env` no seu editor e configure as seguintes variáveis para a conexão com o banco de dados e o CORS:
        ```env
        # Configurações do Banco de Dados
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=crud_laravel_vue_db # <- Nome do banco de dados a ser criado
        DB_USERNAME=root             # <- Usuário do MySQL (comum no XAMPP)
        DB_PASSWORD=                 # <- Senha do MySQL (vazio se não houver no XAMPP)

        # Configuração de Localização (para mensagens de validação em PT-BR)
        APP_LOCALE=pt_BR

        # Configuração de CORS para o Frontend (IMPORTANTE!)
        CORS_ALLOWED_ORIGINS="http://localhost:5173" # Endereço do seu frontend Vue.js
        CORS_ALLOWED_METHODS="*"
        CORS_ALLOWED_HEADERS="*"
        ```
    * *Certifique-se de que o `APP_LOCALE` esteja como `pt_BR` para as mensagens de validação em português.*

5.  **Crie o banco de dados no phpMyAdmin:**
    * Acesse `http://localhost/phpmyadmin/` em seu navegador.
    * No menu lateral, clique em `New` (ou `Databases`) e crie um novo banco de dados com o nome exato que você definiu em `DB_DATABASE` (ex: `crud_laravel_vue_db`).

6.  **Execute as migrações (para criar as tabelas no banco de dados):**
    ```bash
    php artisan migrate
    ```
    * *Se encontrar erros de chave estrangeira (`Foreign key constraint is incorrectly formed`), pode ser necessário dropar e recriar o banco de dados via phpMyAdmin, e então executar `php artisan migrate:fresh`.*

7.  **Servir o Backend:**
    * Certifique-se de que os módulos **Apache** e **MySQL** no seu painel de controle do XAMPP estejam com o status "Running".
    * O backend (API) será acessível via: `http://localhost/crud-vue-laravel-backend/public/api`

### 3. Configuração do Frontend (Vue.js SPA)

1.  **Navegue para a pasta do frontend (a partir do diretório raiz do repositório clonado):**
    ```bash
    cd ../crud-vue-laravel-frontend
    ```

2.  **Instale as dependências do NPM:**
    ```bash
    npm install
    ```

3.  **Inicie o servidor de desenvolvimento:**
    ```bash
    npm run dev
    ```
    * Isso iniciará o servidor Vite, geralmente na porta `5173`. Você verá uma mensagem no terminal como: `Local: http://localhost:5173/`.

4.  **Acesse a aplicação no navegador:**
    * Abra seu navegador e acesse a URL fornecida pelo `npm run dev` (geralmente `http://localhost:5173/`).

---

## ✅ Teste o Projeto

Após seguir os passos acima, sua aplicação estará funcionando!

* **Backend:** Acesse `http://localhost/crud-vue-laravel-backend/public/api/users` no navegador para confirmar que a API está retornando um array JSON (inicialmente vazio).
* **Frontend:** No navegador, utilize a interface para:
    * Navegar entre as seções "Usuários", "Perfis" e "Endereços".
    * Cadastrar novos Perfis.
    * Cadastrar novos Endereços.
    * Cadastrar novos Usuários (vinculando a perfis e endereços já criados).
    * Testar os filtros de pesquisa na lista de Usuários.
    * Editar e Excluir registros.
    * Verificar as mensagens de erro detalhadas ao tentar operações inválidas (ex: nome/email/cpf duplicado).

---

## 📧 Contato

Qualquer dúvida ou necessidade de suporte, sinta-se à vontade para entrar em contato:

* **Discord:** xviictorx
* **Link da comunidade no Discord:** [FULL STACK HUB](https://discord.gg/FvwXhJzDn2)

---