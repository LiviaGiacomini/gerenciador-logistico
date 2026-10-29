# Gerenciador Logístico

## Sobre o projeto

O Gerenciador Logístico é um sistema web desenvolvido como projeto acadêmico da disciplina Fundamentos de Programação para Internet, da UNIPAR EAD.

O sistema permite cadastrar e editar produtos, endereços e transportadores, registrar fretes com múltiplos produtos e gerar relatórios por data.

## Funcionalidades

- Cadastro, listagem e edição de produtos.
- Cadastro, listagem e edição de endereços de origem e destino.
- Cadastro, listagem e edição de transportadores.
- Registro de fretes com múltiplos produtos e suas quantidades.
- Associação entre fretes, produtos, endereços e transportadores.
- Consulta de relatórios de fretes por data.
- Exportação de relatórios em CSV.

## Tecnologias utilizadas

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- Bootstrap
- React e TypeScript (protótipo visual)
- Vite
- XAMPP
- Git e GitHub

## Estrutura de pastas e arquivos

O projeto está organizado da seguinte forma:

```text
gerenciador_logistico/
├── database/
│   └── banco.sql
├── public/
│   └── favicon.svg
├── src/
│   ├── App.tsx
│   ├── index.css
│   ├── main.tsx
│   └── vite-env.d.ts
├── .gitignore
├── baixar_relatorio.php
├── conexao.php
├── enderecos.php
├── fretes.php
├── index.html
├── index.php
├── package.json
├── package-lock.json
├── produtos.php
├── relatorios.php
├── transportadores.php
├── tsconfig.json
├── vite.config.ts
└── README.md
```

### Descrição dos principais diretórios e arquivos

- `database/`: contém o arquivo SQL utilizado para criar as tabelas necessárias ao sistema.
- `public/`: contém o ícone utilizado pelo protótipo visual.
- `src/`: contém os arquivos-fonte do protótipo visual desenvolvido com React e TypeScript.
- `conexao.php`: configura a conexão do sistema com o banco de dados MySQL.
- `index.php`: apresenta a página inicial do sistema.
- `produtos.php`: gerencia os produtos cadastrados.
- `enderecos.php`: gerencia os endereços de origem e destino.
- `transportadores.php`: gerencia os transportadores.
- `fretes.php`: permite registrar e consultar fretes com múltiplos produtos.
- `relatorios.php`: permite consultar relatórios de fretes por data.
- `baixar_relatorio.php`: permite exportar relatórios em formato CSV.
- `database/banco.sql`: contém os comandos SQL para criar as tabelas necessárias ao sistema.
- `README.md`: apresenta a descrição do projeto, suas funcionalidades e as instruções de instalação.

## Como executar o projeto PHP

1. Instale o XAMPP.
2. Inicie os serviços Apache e MySQL pelo Painel de Controle do XAMPP.
3. Coloque a pasta `gerenciador_logistico` dentro do diretório `C:\xampp\htdocs`.
4. Abra o phpMyAdmin pelo endereço `http://localhost/phpmyadmin`.
5. Crie um banco de dados vazio chamado `gerenciador_logistico`.
6. Selecione o banco de dados criado, clique em **Importar** e selecione o arquivo `database/banco.sql`.
7. Confira as configurações de conexão com o banco de dados no arquivo `conexao.php`.
8. Acesse o sistema pelo endereço `http://localhost/gerenciador_logistico/`.

**Importante:** o arquivo `database/banco.sql` cria as tabelas que ainda não existem. Ele não apaga tabelas existentes, mas também não corrige automaticamente a estrutura de tabelas já criadas. Para uma instalação limpa, utilize um banco de dados vazio.

## Como executar o protótipo visual

O projeto também contém um protótipo visual desenvolvido com React, TypeScript e Vite.

Para executar o protótipo:

1. Instale o Node.js.
2. Abra o terminal na pasta do projeto.
3. Execute o comando `npm install`.
4. Execute o comando `npm run dev`.
5. Acesse no navegador o endereço indicado pelo terminal.

O protótipo visual é separado do sistema PHP conectado ao banco de dados MySQL.

## Projeto acadêmico

Desenvolvido para a disciplina Fundamentos de Programação para Internet, da UNIPAR EAD.