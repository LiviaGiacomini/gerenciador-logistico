
<?php
session_start();

require_once __DIR__ . "/includes/conexao.php";

$mensagem = $_SESSION["endereco_mensagem"] ?? "";
$tipoMensagem = $_SESSION["endereco_tipo"] ?? "warning";
$dadosFormulario = $_SESSION["endereco_dados"] ?? [];

unset(
    $_SESSION["endereco_mensagem"],
    $_SESSION["endereco_tipo"],
    $_SESSION["endereco_dados"]
);

$enderecoEditar = null;

function escaparEndereco($valor) {
    return htmlspecialchars((string) ($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if (isset($_GET["editar"])) {
    $consulta = $conexao->prepare("SELECT * FROM enderecos WHERE id = ?");
    $consulta->execute([(int) $_GET["editar"]]);
    $enderecoEditar = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$enderecoEditar) {
        $mensagem = "Endereço não encontrado.";
        $tipoMensagem = "warning";
    }
}

$valoresFormulario = array_merge(
    $enderecoEditar ?? [],
    $dadosFormulario
);

$idFormulario = (int) ($valoresFormulario["id"] ?? 0);
$emEdicao = $idFormulario > 0;

$enderecos = $conexao->query(
    "SELECT * FROM enderecos ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Endereços | Gerenciador Logístico</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/enderecos.css">
</head>

<body>

<header class="cabecalho">
    <h1>Gerenciador Logístico</h1>
    <p>Cadastro, consulta e edição de endereços</p>
</header>

<nav class="navbar navbar-expand-lg bg-white border-bottom px-3">
    <div class="container-fluid">

        <div class="navbar-nav flex-row flex-wrap gap-2">
            <a class="nav-link" href="index.php">Início</a>
            <a class="nav-link" href="produtos.php">Produtos</a>
            <a class="nav-link active fw-bold" href="enderecos.php">Endereços</a>
            <a class="nav-link" href="transportadores.php">Transportadores</a>
            <a class="nav-link" href="fretes.php">Fretes</a>
            <a class="nav-link" href="relatorios.php">Relatórios</a>
        </div>

    </div>
</nav>

<main class="container">

    <section class="painel">

        <h2 class="h4 mb-3">
            <?= $emEdicao ? "Editar endereço" : "Cadastrar endereço" ?>
        </h2>

        <?php if ($mensagem !== ""): ?>
            <div class="alert <?= $tipoMensagem === "success" ? "alert-success" : "alert-warning" ?>">
                <?= escaparEndereco($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="processa/salvar_endereco.php">

            <input
                type="hidden"
                name="id"
                value="<?= $idFormulario ?>"
            >

            <div class="row g-3">

                <div class="col-md-8">
                    <label class="form-label" for="nome">
                        Identificação do endereço *
                    </label>

                    <input
                        class="form-control"
                        id="nome"
                        name="nome"
                        maxlength="150"
                        required
                        value="<?= escaparEndereco($valoresFormulario["nome"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="cep">CEP</label>

                    <input
                        class="form-control"
                        id="cep"
                        name="cep"
                        maxlength="10"
                        value="<?= escaparEndereco($valoresFormulario["cep"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="estado">
                        Estado (UF)
                    </label>

                    <input
                        class="form-control text-uppercase"
                        id="estado"
                        name="estado"
                        maxlength="2"
                        value="<?= escaparEndereco($valoresFormulario["estado"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-9">
                    <label class="form-label" for="cidade">Cidade *</label>

                    <input
                        class="form-control"
                        id="cidade"
                        name="cidade"
                        maxlength="100"
                        required
                        value="<?= escaparEndereco($valoresFormulario["cidade"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="bairro">Bairro</label>

                    <input
                        class="form-control"
                        id="bairro"
                        name="bairro"
                        maxlength="100"
                        value="<?= escaparEndereco($valoresFormulario["bairro"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="rua">Rua</label>

                    <input
                        class="form-control"
                        id="rua"
                        name="rua"
                        maxlength="150"
                        value="<?= escaparEndereco($valoresFormulario["rua"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="numero">Número</label>

                    <input
                        class="form-control"
                        id="numero"
                        name="numero"
                        maxlength="20"
                        value="<?= escaparEndereco($valoresFormulario["numero"] ?? "") ?>"
                    >
                </div>

                <div class="col-md-8">
                    <label class="form-label" for="complemento">
                        Complemento
                    </label>

                    <input
                        class="form-control"
                        id="complemento"
                        name="complemento"
                        maxlength="100"
                        value="<?= escaparEndereco($valoresFormulario["complemento"] ?? "") ?>"
                    >
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">

                    <button type="submit" class="btn btn-primary">
                        <?= $emEdicao ? "Salvar alterações" : "Cadastrar endereço" ?>
                    </button>

                    <?php if ($emEdicao): ?>
                        <a href="enderecos.php" class="btn btn-outline-secondary">
                            Cancelar edição
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        </form>

    </section>

    <section class="painel">

        <h2 class="h4 mb-3">Endereços cadastrados</h2>

        <?php if (count($enderecos) === 0): ?>

            <p class="text-secondary">
                Nenhum endereço cadastrado ainda.
            </p>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Identificação</th>
                            <th>Cidade/UF</th>
                            <th>CEP</th>
                            <th>Bairro</th>
                            <th>Rua</th>
                            <th>Número</th>
                            <th>Ação</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($enderecos as $endereco): ?>

                        <tr>

                            <td><?= (int) $endereco["id"] ?></td>

                            <td>
                                <?= escaparEndereco($endereco["nome"]) ?>
                            </td>

                            <td>
                                <?= escaparEndereco(
                                    $endereco["cidade"] . "/" . $endereco["estado"]
                                ) ?>
                            </td>

                            <td>
                                <?= escaparEndereco($endereco["cep"]) ?>
                            </td>

                            <td>
                                <?= escaparEndereco($endereco["bairro"]) ?>
                            </td>

                            <td>
                                <?= escaparEndereco($endereco["rua"]) ?>
                            </td>

                            <td>
                                <?= escaparEndereco($endereco["numero"]) ?>
                            </td>

                            <td>
                                <a
                                    class="btn btn-sm btn-outline-primary"
                                    href="enderecos.php?editar=<?= (int) $endereco["id"] ?>"
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
