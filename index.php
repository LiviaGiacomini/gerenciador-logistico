
<?php
require_once __DIR__ . "/includes/conexao.php";

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

    <link rel="stylesheet" href="assets/css/style.css">
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
