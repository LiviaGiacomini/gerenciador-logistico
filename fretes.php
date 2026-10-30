
<?php
session_start();

require_once __DIR__ . "/includes/conexao.php";

$mensagem = $_SESSION["frete_mensagem"] ?? "";
$tipoMensagem = $_SESSION["frete_tipo"] ?? "danger";
$dadosFormulario = $_SESSION["frete_dados"] ?? [];

unset(
    $_SESSION["frete_mensagem"],
    $_SESSION["frete_tipo"],
    $_SESSION["frete_dados"]
);

function escaparFrete($valor) {
    return htmlspecialchars((string) ($valor ?? ""), ENT_QUOTES, "UTF-8");
}

$produtosSelecionados = $dadosFormulario["produtos"] ?? [];
$quantidadesFormulario = $dadosFormulario["quantidades"] ?? [];

$origemSelecionada = (int) ($dadosFormulario["origem_id"] ?? 0);
$destinoSelecionado = (int) ($dadosFormulario["destino_id"] ?? 0);
$transportadorSelecionado = (int) ($dadosFormulario["transportador_id"] ?? 0);

$dataEnvioFormulario = $dadosFormulario["data_envio"] ?? "";
$dataEntregaFormulario = $dadosFormulario["data_entrega"] ?? "";
$valorFormulario = $dadosFormulario["valor"] ?? "0.00";
$statusFormulario = $dadosFormulario["status"] ?? "Pendente";
$observacoesFormulario = $dadosFormulario["observacoes"] ?? "";

$produtos = $conexao->query(
    "SELECT id, nome FROM produtos ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);

$enderecos = $conexao->query(
    "SELECT id, nome, cidade, estado FROM enderecos ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);

$transportadores = $conexao->query(
    "SELECT id, nome FROM transportadores ORDER BY nome"
)->fetchAll(PDO::FETCH_ASSOC);

$sqlFretes = "SELECT
                f.*,
                o.nome AS origem_nome,
                o.cidade AS origem_cidade,
                d.nome AS destino_nome,
                d.cidade AS destino_cidade,
                t.nome AS transportador_nome
              FROM fretes f
              INNER JOIN enderecos o ON f.origem_id = o.id
              INNER JOIN enderecos d ON f.destino_id = d.id
              INNER JOIN transportadores t ON f.transportador_id = t.id
              ORDER BY f.id DESC";

$fretes = $conexao->query($sqlFretes)->fetchAll(PDO::FETCH_ASSOC);

$produtosPorFrete = [];

$sqlItens = "SELECT fp.frete_id, p.nome, fp.quantidade
             FROM frete_produtos fp
             INNER JOIN produtos p ON p.id = fp.produto_id
             ORDER BY p.nome";

$itens = $conexao->query($sqlItens)->fetchAll(PDO::FETCH_ASSOC);

foreach ($itens as $item) {
    $produtosPorFrete[$item["frete_id"]][] =
        $item["nome"] . " (qtd.: " . $item["quantidade"] . ")";
}

// Compatibilidade com fretes antigos sem itens na tabela nova.
foreach ($fretes as $frete) {
    if (empty($produtosPorFrete[$frete["id"]])) {
        $consultaAntigo = $conexao->prepare(
            "SELECT nome FROM produtos WHERE id = ?"
        );

        $consultaAntigo->execute([$frete["produto_id"]]);
        $nomeAntigo = $consultaAntigo->fetchColumn();

        if ($nomeAntigo) {
            $produtosPorFrete[$frete["id"]][] =
                $nomeAntigo . " (qtd.: 1)";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Fretes | Gerenciador Logístico</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/fretes.css">
</head>

<body>

<header>
    <h1>Gerenciador Logístico</h1>
    <p>Cadastro e acompanhamento de fretes</p>
</header>

<nav class="px-4 py-2">
    <a href="index.php">Início</a>
    <a href="produtos.php">Produtos</a>
    <a href="enderecos.php">Endereços</a>
    <a href="transportadores.php">Transportadores</a>
    <a href="fretes.php" class="ativo">Fretes</a>
    <a href="relatorios.php">Relatórios</a>
</nav>

<main>

<section class="painel">

    <h2 class="h4 mb-4">Cadastrar frete</h2>

    <?php if ($mensagem !== ""): ?>
        <div class="alert <?= $tipoMensagem === "success" ? "alert-success" : "alert-danger" ?>">
            <?= escaparFrete($mensagem) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="processa/salvar_frete.php">

        <div class="row g-3">

            <div class="col-12">

                <label class="form-label">
                    Produtos e quantidades *
                </label>

                <div class="lista-produtos">

                    <?php if (!$produtos): ?>

                        <p class="mb-0">
                            Cadastre pelo menos um produto antes de criar um frete.
                        </p>

                    <?php else: ?>

                        <?php foreach ($produtos as $produto): ?>

                            <?php
                            $produtoId = (int) $produto["id"];
                            $produtoMarcado = in_array(
                                $produtoId,
                                array_map("intval", (array) $produtosSelecionados),
                                true
                            );
                            ?>

                            <div class="item-produto">

                                <div class="form-check">

                                    <input
                                        class="form-check-input produto-check"
                                        type="checkbox"
                                        name="produtos[]"
                                        value="<?= $produtoId ?>"
                                        id="produto_<?= $produtoId ?>"
                                        <?= $produtoMarcado ? "checked" : "" ?>
                                    >

                                    <label
                                        class="form-check-label"
                                        for="produto_<?= $produtoId ?>"
                                    >
                                        <?= escaparFrete($produto["nome"]) ?>
                                    </label>

                                </div>

                                <div>

                                    <label
                                        class="form-label small"
                                        for="qtd_<?= $produtoId ?>"
                                    >
                                        Quantidade
                                    </label>

                                    <input
                                        class="form-control form-control-sm"
                                        type="number"
                                        name="quantidades[<?= $produtoId ?>]"
                                        id="qtd_<?= $produtoId ?>"
                                        value="<?= escaparFrete($quantidadesFormulario[$produtoId] ?? 1) ?>"
                                        min="1"
                                        step="1"
                                    >

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

                <small class="text-secondary">
                    Marque um ou mais produtos e informe a quantidade de cada um.
                </small>

            </div>

            <div class="col-md-6">

                <label for="transportador_id" class="form-label">
                    Transportadora *
                </label>

                <select
                    class="form-select"
                    id="transportador_id"
                    name="transportador_id"
                    required
                >

                    <option value="">Selecione uma transportadora</option>

                    <?php foreach ($transportadores as $transportador): ?>

                        <option
                            value="<?= (int) $transportador["id"] ?>"
                            <?= $transportadorSelecionado === (int) $transportador["id"] ? "selected" : "" ?>
                        >
                            <?= escaparFrete($transportador["nome"]) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-md-6">

                <label for="origem_id" class="form-label">
                    Endereço de origem *
                </label>

                <select
                    class="form-select"
                    id="origem_id"
                    name="origem_id"
                    required
                >

                    <option value="">Selecione a origem</option>

                    <?php foreach ($enderecos as $endereco): ?>

                        <option
                            value="<?= (int) $endereco["id"] ?>"
                            <?= $origemSelecionada === (int) $endereco["id"] ? "selected" : "" ?>
                        >
                            <?= escaparFrete(
                                $endereco["nome"] . " - " .
                                $endereco["cidade"] . "/" .
                                $endereco["estado"]
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-md-6">

                <label for="destino_id" class="form-label">
                    Endereço de destino *
                </label>

                <select
                    class="form-select"
                    id="destino_id"
                    name="destino_id"
                    required
                >

                    <option value="">Selecione o destino</option>

                    <?php foreach ($enderecos as $endereco): ?>

                        <option
                            value="<?= (int) $endereco["id"] ?>"
                            <?= $destinoSelecionado === (int) $endereco["id"] ? "selected" : "" ?>
                        >
                            <?= escaparFrete(
                                $endereco["nome"] . " - " .
                                $endereco["cidade"] . "/" .
                                $endereco["estado"]
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-md-6">

                <label for="data_envio" class="form-label">
                    Data de envio
                </label>

                <input
                    class="form-control"
                    type="date"
                    id="data_envio"
                    name="data_envio"
                    value="<?= escaparFrete($dataEnvioFormulario) ?>"
                >

            </div>

            <div class="col-md-6">

                <label for="data_entrega" class="form-label">
                    Data prevista de entrega
                </label>

                <input
                    class="form-control"
                    type="date"
                    id="data_entrega"
                    name="data_entrega"
                    value="<?= escaparFrete($dataEntregaFormulario) ?>"
                >

            </div>

            <div class="col-md-6">

                <label for="valor" class="form-label">
                    Valor do frete (R$) *
                </label>

                <input
                    class="form-control"
                    type="number"
                    id="valor"
                    name="valor"
                    min="0"
                    step="0.01"
                    value="<?= escaparFrete($valorFormulario) ?>"
                    required
                >

            </div>

            <div class="col-md-6">

                <label for="status" class="form-label">
                    Status *
                </label>

                <select
                    class="form-select"
                    id="status"
                    name="status"
                    required
                >

                    <?php
                    $statusOpcoes = [
                        "Pendente",
                        "Em trânsito",
                        "Entregue",
                        "Cancelado"
                    ];
                    ?>

                    <?php foreach ($statusOpcoes as $statusOpcao): ?>

                        <option
                            value="<?= escaparFrete($statusOpcao) ?>"
                            <?= $statusFormulario === $statusOpcao ? "selected" : "" ?>
                        >
                            <?= escaparFrete($statusOpcao) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-12">

                <label for="observacoes" class="form-label">
                    Observações
                </label>

                <textarea
                    class="form-control"
                    id="observacoes"
                    name="observacoes"
                    rows="3"
                    placeholder="Informações adicionais sobre o frete"
                ><?= escaparFrete($observacoesFormulario) ?></textarea>

            </div>

            <div class="col-12">

                <button class="btn btn-principal" type="submit">
                    Cadastrar frete
                </button>

            </div>

        </div>

    </form>

</section>

<section class="painel">

    <h2 class="h4 mb-3">Fretes cadastrados</h2>

    <?php if (count($fretes) === 0): ?>

        <p class="text-secondary">
            Nenhum frete cadastrado ainda.
        </p>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Produtos</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Transportadora</th>
                        <th>Envio</th>
                        <th>Entrega</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($fretes as $frete): ?>

                        <tr>

                            <td><?= (int) $frete["id"] ?></td>

                            <td>
                                <?php foreach (($produtosPorFrete[$frete["id"]] ?? []) as $nomeProduto): ?>
                                    <div><?= escaparFrete($nomeProduto) ?></div>
                                <?php endforeach; ?>
                            </td>

                            <td>
                                <?= escaparFrete(
                                    $frete["origem_nome"] . " - " .
                                    $frete["origem_cidade"]
                                ) ?>
                            </td>

                            <td>
                                <?= escaparFrete(
                                    $frete["destino_nome"] . " - " .
                                    $frete["destino_cidade"]
                                ) ?>
                            </td>

                            <td>
                                <?= escaparFrete($frete["transportador_nome"]) ?>
                            </td>

                            <td>
                                <?= $frete["data_envio"]
                                    ? date("d/m/Y", strtotime($frete["data_envio"]))
                                    : "—" ?>
                            </td>

                            <td>
                                <?= $frete["data_entrega"]
                                    ? date("d/m/Y", strtotime($frete["data_entrega"]))
                                    : "—" ?>
                            </td>

                            <td>
                                R$ <?= number_format(
                                    (float) $frete["valor"],
                                    2,
                                    ",",
                                    "."
                                ) ?>
                            </td>

                            <td>
                                <?= escaparFrete($frete["status"]) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>

</main>

<script src="assets/js/fretes.js"></script>

</body>
</html>
