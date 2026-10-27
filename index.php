
<?php
require_once "conexao.php";

function contarRegistros($conexao, $tabela) {
    $tabelasPermitidas = [
        "produtos",
        "enderecos",
        "transportadores",
        "fretes"
    ];

    if (!in_array($tabela, $tabelasPermitidas, true)) {
        return 0;
    }

    $consulta = $conexao->query("SELECT COUNT(*) FROM $tabela");
    return $consulta->fetchColumn();
}

$totalProdutos = contarRegistros($conexao, "produtos");
$totalEnderecos = contarRegistros($conexao, "enderecos");
$totalTransportadores = contarRegistros($conexao, "transportadores");
$totalFretes = contarRegistros($conexao, "fretes");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início | Gerenciador Logístico</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            color: #253247;
        }

        header {
            background: #17365c;
            color: white;
            padding: 22px 30px;
        }

        header h1 {
            margin: 0;
            font-size: 25px;
        }

        header p {
            margin: 7px 0 0;
            color: #d7e4f4;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 15px 30px;
            background: white;
            border-bottom: 1px solid #dfe5ed;
        }

        nav a {
            color: #344761;
            text-decoration: none;
            padding: 10px 13px;
            border-radius: 6px;
        }

        nav a:hover,
        nav a.ativo {
            background: #e5eef9;
            color: #17365c;
        }

        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 25px;
        }

        .boas-vindas {
            margin-bottom: 25px;
        }

        .boas-vindas h2 {
            margin-bottom: 8px;
        }

        .boas-vindas p {
            color: #68778b;
        }

        .indicadores {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            border: 1px solid #e1e7ef;
            box-shadow: 0 3px 10px #1b365c0a;
        }

        .card h3 {
            margin: 0 0 15px;
            color: #65748a;
            font-size: 15px;
            font-weight: normal;
        }

        .numero {
            font-size: 32px;
            font-weight: bold;
            color: #17365c;
        }

        .atalhos {
            margin-top: 28px;
        }

        .atalhos h2 {
            font-size: 20px;
        }

        .atalhos a {
            display: inline-block;
            margin: 5px 8px 5px 0;
            padding: 12px 16px;
            border-radius: 7px;
            background: #17365c;
            color: white;
            text-decoration: none;
        }

        .atalhos a:hover {
            background: #245080;
        }

        footer {
            padding: 25px;
            text-align: center;
            color: #7a8798;
            font-size: 13px;
        }

        @media (max-width: 800px) {
            .indicadores {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .indicadores {
                grid-template-columns: 1fr;
            }

            header,
            nav {
                padding-left: 18px;
                padding-right: 18px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Gerenciador Logístico</h1>
    <p>Sistema de controle e acompanhamento de operações logísticas</p>
</header>

<nav>
    <a href="index.php" class="ativo">Início</a>
    <a href="produtos.php">Produtos</a>
    <a href="enderecos.php">Endereços</a>
    <a href="transportadores.php">Transportadores</a>
    <a href="fretes.php">Fretes</a>
    <a href="relatorios.php">Relatórios</a>
</nav>

<main>
    <section class="boas-vindas">
        <h2>Painel inicial</h2>
        <p>Confira o resumo dos cadastros e das operações logísticas.</p>
    </section>

    <section class="indicadores">
        <div class="card">
            <h3>Produtos cadastrados</h3>
            <div class="numero"><?= $totalProdutos ?></div>
        </div>

        <div class="card">
            <h3>Endereços cadastrados</h3>
            <div class="numero"><?= $totalEnderecos ?></div>
        </div>

        <div class="card">
            <h3>Transportadores cadastrados</h3>
            <div class="numero"><?= $totalTransportadores ?></div>
        </div>

        <div class="card">
            <h3>Fretes registrados</h3>
            <div class="numero"><?= $totalFretes ?></div>
        </div>
    </section>

    <section class="atalhos">
        <h2>Acesso rápido</h2>
        <a href="produtos.php">Cadastrar produtos</a>
        <a href="enderecos.php">Cadastrar endereços</a>
        <a href="transportadores.php">Cadastrar transportadores</a>
        <a href="fretes.php">Consultar fretes</a>
        <a href="relatorios.php">Ver relatórios</a>
    </section>
</main>

<footer>
    Gerenciador Logístico | Projeto acadêmico
</footer>

</body>
</html>
