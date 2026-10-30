
<?php
session_start();

require_once __DIR__ . "/includes/conexao.php";

$mensagem = $_SESSION["transportador_mensagem"] ?? "";
$tipoMensagem = $_SESSION["transportador_tipo"] ?? "danger";
$dadosFormulario = $_SESSION["transportador_dados"] ?? [];

unset(
    $_SESSION["transportador_mensagem"],
    $_SESSION["transportador_tipo"],
    $_SESSION["transportador_dados"]
);

$transportadorEdicao = null;

function escaparTransportador($valor) {
    return htmlspecialchars((string) ($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if (isset($_GET["editar"])) {
    $consulta = $conexao->prepare(
        "SELECT * FROM transportadores WHERE id = ?"
    );

    $consulta->execute([(int) $_GET["editar"]]);

    $transportadorEdicao = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$transportadorEdicao) {
        $mensagem = "Transportadora não encontrada.";
        $tipoMensagem = "danger";
    }
}

$valoresFormulario = array_merge(
    $transportadorEdicao ?? [],
    $dadosFormulario
);

$idFormulario = (int) ($valoresFormulario["id"] ?? 0);
$emEdicao = $idFormulario > 0;

$transportadores = $conexao->query(
    "SELECT * FROM transportadores ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transportadores | Gerenciador Logístico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/transportadores.css">
</head>

<body>

<header>
    <h1>Gerenciador Logístico</h1>
    <p>Cadastro e controle de transportadores</p>
</header>

<nav class="px-4 py-2">
    <a href="index.php">Início</a>
    <a href="produtos.php">Produtos</a>
    <a href="enderecos.php">Endereços</a>
    <a href="transportadores.php" class="ativo">Transportadores</a>
    <a href="fretes.php">Fretes</a>
    <a href="relatorios.php">Relatórios</a>
</nav>

<main>

    <section class="painel">

        <h2 class="h4 mb-4">
            <?= $emEdicao ? "Editar transportadora" : "Cadastrar transportadora" ?>
        </h2>

        <?php if ($mensagem !== ""): ?>
            <div class="alert <?= $tipoMensagem === "success" ? "alert-success" : "alert-danger" ?>">
                <?= escaparTransportador($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="processa/salvar_transportador.php">

            <input
                type="hidden"
                name="id"
                value="<?= $idFormulario ?>"
            >

            <div class="row g-3">

                <div class="col-12">
                    <label for="nome" class="form-label">
                        Nome da transportadora *
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        id="nome"
                        name="nome"
                        maxlength="150"
                        required
                        value="<?= escaparTransportador($valoresFormulario["nome"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-6">
                    <label for="cnpj" class="form-label">CNPJ</label>

                    <input
                        class="form-control"
                        type="text"
                        id="cnpj"
                        name="cnpj"
                        maxlength="18"
                        placeholder="00.000.000/0000-00"
                        value="<?= escaparTransportador($valoresFormulario["cnpj"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>

                    <input
                        class="form-control"
                        type="tel"
                        id="telefone"
                        name="telefone"
                        maxlength="20"
                        placeholder="(00) 00000-0000"
                        value="<?= escaparTransportador($valoresFormulario["telefone"] ?? "") ?>"
                    >
                </div>

                <div class="col-12">
                    <label for="email" class="form-label">E-mail</label>

                    <input
                        class="form-control"
                        type="email"
                        id="email"
                        name="email"
                        maxlength="150"
                        placeholder="contato@transportadora.com.br"
                        value="<?= escaparTransportador($valoresFormulario["email"] ?? "") ?>"
                    >
                </div>

                <div class="col-12">

                    <button class="btn btn-principal" type="submit">
                        <?= $emEdicao ? "Salvar alterações" : "Cadastrar transportadora" ?>
                    </button>

                    <?php if ($emEdicao): ?>
                        <a href="transportadores.php" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        </form>

    </section>

    <section class="painel">

        <h2 class="h4 mb-3">Transportadoras cadastradas</h2>

        <?php if (count($transportadores) === 0): ?>

            <p class="text-secondary">
                Nenhuma transportadora cadastrada ainda.
            </p>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th>Ação</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($transportadores as $transportador): ?>

                        <tr>

                            <td>
                                <?= (int) $transportador["id"] ?>
                            </td>

                            <td>
                                <?= escaparTransportador($transportador["nome"]) ?>
                            </td>

                            <td>
                                <?= escaparTransportador($transportador["cnpj"] ?: "—") ?>
                            </td>

                            <td>
                                <?= escaparTransportador($transportador["telefone"] ?: "—") ?>
                            </td>

                            <td>
                                <?= escaparTransportador($transportador["email"] ?: "—") ?>
                            </td>

                            <td>
                                <a
                                    class="btn btn-sm btn-outline-primary"
                                    href="transportadores.php?editar=<?= (int) $transportador["id"] ?>"
                                >
                                    Editar
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>
