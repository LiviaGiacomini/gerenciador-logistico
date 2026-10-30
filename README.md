# Gerenciador Logístico

## Sobre o projeto

O Gerenciador Logístico é um sistema web desenvolvido como projeto acadêmico da disciplina Fundamentos de Programação para Internet, da UNIPAR EAD.

O sistema permite cadastrar e editar produtos, endereços e transportadores, registrar fretes com múltiplos produtos e gerar relatórios por período.

O repositório também contém um protótipo visual desenvolvido com React e TypeScript, utilizado como referência para a interface do sistema.

## Funcionalidades

- Cadastro, listagem e edição de produtos.
- Cadastro, listagem e edição de endereços de origem e destino.
- Cadastro, listagem e edição de transportadores.
- Registro de fretes com múltiplos produtos e suas respectivas quantidades.
- Associação entre fretes, produtos, endereços e transportadores.
- Consulta de relatórios de fretes por período.
- Exportação de relatórios em formato CSV.

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
- pnpm
- XAMPP
- Git e GitHub

## Estrutura de pastas e arquivos

O repositório reúne o sistema web desenvolvido em PHP e o protótipo visual desenvolvido com React e TypeScript.

```text
gerenciador_logistico/
├── .figma/
│   └── make/
├── assets/
│   ├── css/
│   │   ├── enderecos.css
│   │   ├── fretes.css
│   │   ├── produtos.css
│   │   ├── relatorios.css
│   │   ├── style.css
│   │   └── transportadores.css
│   └── js/
│       └── fretes.js
├── database/
│   └── banco.sql
├── includes/
│   └── conexao.php
├── processa/
│   ├── salvar_endereco.php
│   ├── salvar_frete.php
│   ├── salvar_produto.php
│   └── salvar_transportador.php
├── public/
│   └── favicon.svg
├── src/
│   ├── App.tsx
│   ├── index.css
│   ├── main.tsx
│   └── vite-env.d.ts
├── .gitignore
├── .mise.toml
├── AGENTS.md
├── CLAUDE.md
├── baixar_relatorio.php
├── enderecos.php
├── fretes.php
├── index.html
├── index.php
├── package.json
├── package-lock.json
├── pnpm-lock.yaml
├── produtos.php
├── README.md
├── relatorios.php
├── transportadores.php
├── tsconfig.json
└── vite.config.ts
```

*Observação: a estrutura apresenta os principais arquivos do projeto. A pasta `.figma/make/` contém arquivos auxiliares de configuração utilizados pelo ambiente Figma Make.*

### Descrição dos principais diretórios e arquivos

- `assets/css/`: armazena os arquivos de estilo CSS utilizados nas páginas do sistema PHP.
- `assets/js/`: contém os arquivos JavaScript utilizados nas funcionalidades do sistema.
- `database/`: contém o script SQL utilizado para criar as tabelas do banco de dados.
- `includes/`: reúne arquivos reutilizáveis pelo sistema, incluindo a conexão com o banco de dados.
- `processa/`: contém os scripts PHP responsáveis pelo processamento dos cadastros e do registro de fretes.
- `public/`: contém arquivos públicos utilizados pelo protótipo visual.
- `src/`: contém os arquivos-fonte do protótipo desenvolvido com React e TypeScript.
- `.figma/make/`: contém arquivos auxiliares de configuração do ambiente Figma Make.
- `index.php`: apresenta a página inicial do sistema PHP.
- `produtos.php`: permite cadastrar, listar e editar produtos.
- `enderecos.php`: permite cadastrar, listar e editar endereços de origem e destino.
- `transportadores.php`: permite cadastrar, listar e editar transportadores.
- `fretes.php`: permite registrar e