
<?php
session_start();

require_once __DIR__ . "/includes/conexao.php";

$mensagem = $_SESSION["produto_mensagem"] ?? "";
$tipoMensagem = $_SESSION["produto_tipo"] ?? "warning";
$dadosFormulario = $_SESSION["produto_dados"] ?? [];

unset(
    $_SESSION["produto_mensagem"],
    $_SESSION["produto_tipo"],
    $_SESSION["produto_dados"]
);

$produtoEditar = null;

function escaparProduto($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

// Busca os dados do produto selecionado para edição.
if (isset($_GET["editar"])) {
    $idEditar = (int)$_GET["editar"];

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$idEditar]);
    $produtoEditar = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$produtoEditar) {
        $mensagem = "Produto não encontrado.";
        $tipoMensagem = "warning";
    }
}

// Mantém os dados enviados caso ocorra algum erro no processamento.
$valoresFormulario = array_merge(
    $produtoEditar ?? [],
    $dadosFormulario
);

$idFormulario = (int)($valoresFormulario["id"] ?? 0);
$emEdicao = $idFormulario > 0;

// Consulta todos os produtos cadastrados.
$produtos = $conexao->query(
    "SELECT * FROM produtos ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produtos | Gerenciador Logístico</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/produtos.css">
</head>

<body>

<header class="cabecalho">
    <h1>Gerenciador Logístico</h1>
    <p>Cadastro, consulta e edição de produtos</p>
</header>

<nav class="navbar navbar-expand-lg bg-white border-bottom px-3">
    <div class="container-fluid">

        <div class="navbar-nav flex-row flex-wrap gap-2">

            <a class="nav-link" href="index.php">
                Início
            </a>

            <a class="nav-link active fw-bold" href="produtos.php">
                Produtos
            </a>

            <a class="nav-link" href="enderecos.php">
                Endereços
            </a>

            <a class="nav-link" href="transportadores.php">
                Transportadores
            </a>

            <a class="nav-link" href="fretes.php">
                Fretes
            </a>

            <a class="nav-link" href="relatorios.php">
                Relatórios
            </a>

        </div>
    </div>
</nav>

<main class="container">

    <section class="painel">

        <h2 class="h4 mb-3">
            <?= $emEdicao ? "Editar produto" : "Cadastrar produto" ?>
        </h2>

        <?php if ($mensagem !== ""): ?>

            <div class="alert alert-<?= escaparProduto($tipoMensagem) ?>">
                <?= escaparProduto($mensagem) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="processa/salvar_produto.php">

            <input
                type="hidden"
                name="id"
                value="<?= $idFormulario ?>"
            >

            <div class="row g-3">

                <div class="col-12">

                    <label for="nome" class="form-label">
                        Nome do produto *
                    </label>

                    <input
                        class="form-control"
                        type="text"
                        id="nome"
                        name="nome"
                        maxlength="150"
                        required
                        value="<?= escaparProduto($valoresFormulario["nome"] ?? "") ?>"
                    >

                </div>

                <div class="col-12">

                    <label for="descricao" class="form-label">
                        Descrição
                    </label>

                    <textarea
                        class="form-control"
                        id="descricao"
                        name="descricao"
                        rows="3"
                    ><?= escaparProduto($valoresFormulario["descricao"] ?? "") ?></textarea>

                </div>

                <div class="col-md-6">

                    <label for="peso" class="form-label">
                        Peso (kg)
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="peso"
                        name="peso"
                        min="0"
                        step="0.01"
                        value="<?= escaparProduto($valoresFormulario["peso"] ?? "") ?>"
                    >

                </div>

                <div class="col-md-6">

                    <label for="comprimento" class="form-label">
                        Comprimento (cm)
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="comprimento"
                        name="comprimento"
                        min="0"
                        step="0.01"
                        value="<?= escaparProduto($valoresFormulario["comprimento"] ?? "") ?>"
                    >

                </div>

                <div class="col-md-6">

                    <label for="largura" class="form-label">
                        Largura (cm)
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="largura"
                        name="largura"
                        min="0"
                        step="0.01"
                        value="<?= escaparProduto($valoresFormulario["largura"] ?? "") ?>"
                    >

                </div>

                <div class="col-md-6">

                    <label for="altura" class="form-label">
                        Altura (cm)
                    </label>

                    <input
                        class="form-control"
                        type="number"
                        id="altura"
                        name="altura"
                        min="0"
                        step="0.01"
                        value="<?= escaparProduto($valoresFormulario["altura"] ?? "") ?>"
                    >

                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">

                    <button type="submit" class="btn btn-primary">
                        <?= $emEdicao ? "Salvar alterações" : "Cadastrar produto" ?>
                    </button>

                    <?php if ($emEdicao): ?>

                        <a
                            href="produtos.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar edição
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </form>

    </section>

    <section class="painel">

        <h2 class="h4 mb-3">
            Produtos cadastrados
        </h2>

        <?php if (count($produtos) === 0): ?>

            <p class="text-secondary">
                Nenhum produto cadastrado ainda.
            </p>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Peso (kg)</th>
                            <th>Dimensões (cm)</th>
                            <th>Ação</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($produtos as $produto): ?>

                            <tr>

                                <td>
                                    <?= (int)$produto["id"] ?>
                                </td>

                                <td>
                                    <?= escaparProduto($produto["nome"]) ?>
                                </td>

                                <td>
                                    <?= escaparProduto($produto["descricao"]) ?>
                                </td>

                                <td>
                                    <?= escaparProduto($produto["peso"]) ?>
                                </td>

                                <td>
                                    <?= escaparProduto($produto["comprimento"]) ?>
                                    ×
                                    <?= escaparProduto($produto["largura"]) ?>
                                    ×
                                    <?= escaparProduto($produto["altura"]) ?>
                                </td>

                                <td>

                                    <a
                                        class="btn btn-sm btn-outline-primary"
                                        href="produtos.php?editar=<?= (int)$produto["id"] ?>"
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
