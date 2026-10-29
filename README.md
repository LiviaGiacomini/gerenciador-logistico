# Gerenciador Logístico

## Sobre o projeto

O Gerenciador Logístico é um sistema web desenvolvido como projeto acadêmico da disciplina Fundamentos de Programação para Internet, da UNIPAR EAD.

O sistema permite cadastrar e editar produtos, endereços e transportadores, registrar fretes com múltiplos produtos e gerar relatórios por data.

O repositório também contém um protótipo visual desenvolvido com React e TypeScript, utilizado como referência para a interface do sistema.

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
- Tailwind CSS
- Vite
- Node.js e npm
- XAMPP
- Git e GitHub

## Estrutura de pastas e arquivos

O repositório contém o sistema web em PHP e o protótipo visual desenvolvido com React e TypeScript.

```text
gerenciador_logistico/
├── .figma/
│   └── make/
│       ├── analyze-routes
│       ├── deploy
│       ├── deploy-preview
│       ├── dev
│       ├── dev.json
│       ├── format
│       ├── install
│       ├── langserver
│       └── site.json
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
├── AGENTS.md
├── CLAUDE.md
├── baixar_relatorio.php
├── conexao.php
├── enderecos.php
├── fretes.php
├── index.html
├── index.php
├── package.json
├── package-lock.json
├── pnpm-lock.yaml
├── produtos.php
├── relatorios.php
├── transportadores.php
├── tsconfig.json
├── vite.config.ts
└── README.md
```

*Observação: a estrutura apresenta os principais arquivos do projeto. A pasta `.figma/make/` contém arquivos auxiliares de configuração do ambiente Figma Make.*

### Descrição dos principais diretórios e arquivos

- `database/`: contém o arquivo SQL utilizado para criar as tabelas do banco de dados.
- `public/`: contém arquivos públicos utilizados pelo protótipo visual.
- `src/`: contém os arquivos-fonte do protótipo desenvolvido com React e TypeScript.
- `.figma/make/`: contém arquivos auxiliares de configuração utilizados pelo ambiente Figma Make.
- `conexao.php`: configura a conexão do sistema com o banco de dados MySQL.
- `index.php`: apresenta a página inicial do sistema PHP.
- `produtos.php`: gerencia os produtos cadastrados.
- `enderecos.php`: gerencia os endereços de origem e destino.
- `transportadores.php`: gerencia os transportadores.
- `fretes.php`: permite registrar e consultar fretes com múltiplos produtos.
- `relatorios.php`: permite consultar relatórios de fretes por data.
- `baixar_relatorio.php`: permite exportar relatórios em formato CSV.
- `database/banco.sql`: contém os comandos SQL para criar as tabelas necessárias ao sistema.
- `index.html`: contém a estrutura HTML inicial do protótipo React.
- `package.json`: define as dependências e os comandos disponíveis para executar o protótipo.
- `package-lock.json` e `pnpm-lock.yaml`: registram informações sobre as dependências dos gerenciadores de pacotes.
- `vite.config.ts`: configura o Vite, o React, o Tailwind CSS e as configurações específicas do Figma Make.
- `AGENTS.md` e `CLAUDE.md`: arquivos relacionados a instruções e configurações auxiliares de desenvolvimento.
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

**Importante:** o arquivo `database/banco.sql` cria as tabelas que ainda não existem. Ele não apaga tabelas existentes nem corrige automaticamente a estrutura de tabelas já criadas. Para uma instalação limpa, utilize um banco de dados vazio.

## Como executar o protótipo visual

O projeto também contém um protótipo visual desenvolvido com React, TypeScript, Vite e Tailwind CSS.

Para executar o protótipo:

1. Instale o Node.js.
2. Abra o terminal na pasta do projeto.
3. Execute o comando `npm install`.
4. Execute o comando `npm run dev`.
5. Acesse no navegador o endereço indicado pelo terminal.

O protótipo visual é separado do sistema PHP conectado ao banco de dados MySQL. A configuração do Vite utiliza arquivos auxiliares da pasta `.figma/make/`, que devem ser mantidos para preservar essa configuração.

## Projeto acadêmico

Desenvolvido para a disciplina Fundamentos de Programação para Internet, da UNIPAR EAD.