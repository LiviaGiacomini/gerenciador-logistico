
<?php
require_once "conexao.php";

function escapar($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

function dataFormatada($data) {
    return $data ? date("d/m/Y", strtotime($data)) : "—";
}

$totalFretes = (int)$conexao->query(
    "SELECT COUNT(*) FROM fretes"
)->fetchColumn();

$fretesPendentes = (int)$conexao->query(
    "SELECT COUNT(*) FROM fretes WHERE status = 'Pendente'"
)->fetchColumn();

$fretesEmTransito = (int)$conexao->query(
    "SELECT COUNT(*) FROM fretes WHERE status = 'Em trânsito'"
)->fetchColumn();

$fretesEntregues = (int)$conexao->query(
    "SELECT COUNT(*) FROM fretes WHERE status = 'Entregue'"
)->fetchColumn();

$valorTotal = (float)$conexao->query(
    "SELECT COALESCE(SUM(valor), 0) FROM fretes WHERE status <> 'Cancelado'"
)->fetchColumn();

$dataFiltro = $_GET["data"] ?? "";
$dataValida = $dataFiltro !== "" &&
    preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFiltro) &&
    DateTime::createFromFormat("!Y-m-d", $dataFiltro) !== false &&
    DateTime::createFromFormat("!Y-m-d", $dataFiltro)->format("Y-m-d") === $dataFiltro;

$sql = "SELECT
            f.id,
            f.data_envio,
            f.data_entrega,
            f.valor,
            f.status,
            o.nome AS origem,
            o.cidade AS cidade_origem,
            d.nome AS destino,
            d.cidade AS cidade_destino,
            t.nome AS transportadora
        FROM fretes f
        INNER JOIN enderecos o ON f.origem_id = o.id
        INNER JOIN enderecos d ON f.destino_id = d.id
        INNER JOIN transportadores t ON f.transportador_id = t.id";

if ($dataValida) {
    $sql .= " WHERE f.data_envio = ?";
    $consulta = $conexao->prepare($sql . " ORDER BY f.id DESC");
    $consulta->execute([$dataFiltro]);
    $fretes = $consulta->fetchAll(PDO::FETCH_ASSOC);
} else {
    $fretes = $conexao->query($sql . " ORDER BY f.id DESC")
        ->fetchAll(PDO::FETCH_ASSOC);
}

$sqlProdutos = "SELECT fp.frete_id, p.nome, fp.quantidade
                FROM frete_produtos fp
                INNER JOIN produtos p ON p.id = fp.produto_id
                ORDER BY p.nome";
$itens = $conexao->query($sqlProdutos)->fetchAll(PDO::FETCH_ASSOC);

$produtosPorFrete = [];
foreach ($itens as $item) {
    $produtosPorFrete[$item["frete_id"]][] =
        $item["nome"] . " (qtd.: " . $item["quantidade"] . ")";
}

// Exibe o produto antigo para fretes que ainda não tenham itens na tabela nova.
foreach ($fretes as &$frete) {
    if (empty($produtosPorFrete[$frete["id"]])) {
        $consultaProduto = $conexao->prepare(
            "SELECT p.nome FROM fretes f
             INNER JOIN produtos p ON p.id = f.produto_id
             WHERE f.id = ?"
        );
        $consultaProduto->execute([$frete["id"]]);
        $nomeProduto = $consultaProduto->fetchColumn();

        if ($nomeProduto) {
            $produtosPorFrete[$frete["id"]][] = $nomeProduto . " (qtd.: 1)";
        }
    }
}
unset($frete);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatórios | Gerenciador Logístico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { margin: 0; font-family: Arial, sans-serif; background: #f3f6fa; color: #253247; }
header { background: #17365c; color: white; padding: 22px 30px; }
header p { color: #d7e4f4; margin-bottom: 0; }
nav { background: white; border-bottom: 1px solid #dfe5ed; }
nav a { color: #344761; text-decoration: none; padding: 10px 13px; display: inline-block; border-radius: 6px; }
nav a:hover, nav a.ativo { background: #e5eef9; color: #17365c; }
main { max-width: 1200px; margin: 30px auto; padding: 0 22px; }
.indicadores { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px; }
.cartao, .painel { background: white; border: 1px solid #e1e7ef; border-radius: 10px; padding: 22px; }
.cartao p { margin: 0 0 10px; color: #68778b; font-size: 14px; }
.numero { font-size: 28px; font-weight: bold; color: #17365c; }
.painel { margin-bottom: 24px; }
.tabela { overflow-x: auto; }
table { width: 100%; min-width: 950px; }
.acoes { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-principal { background: #17365c; color: white; }
.btn-principal:hover { background: #245080; color: white; }
@media (max-width: 800px) { .indicadores { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 520px) { .indicadores { grid-template-columns: 1fr; } }
@media print {
    nav, .acoes, .filtro { display: none !important; }
    body { background: white; }
    header { background: white; color: #17365c; }
    header p { color: #344761; }
    main { max-width: none; margin: 10px 0; padding: 0; }
}
</style>
</head>
<body>
<header>
    <h1>Gerenciador Logístico</h1>
    <p>Relatórios e indicadores de fretes</p>
</header>

<nav class="px-4 py-2">
    <a href="index.php">Início</a>
    <a href="produtos.php">Produtos</a>
    <a href="enderecos.php">Endereços</a>
    <a href="transportadores.php">Transportadores</a>
    <a href="fretes.php">Fretes</a>
    <a href="relatorios.php" class="ativo">Relatórios</a>
</nav>

<main>
    <section class="indicadores">
        <div class="cartao">
            <p>Total de fretes</p>
            <div class="numero"><?= $totalFretes ?></div>
        </div>
        <div class="cartao">
            <p>Fretes pendentes</p>
            <div class="numero"><?= $fretesPendentes ?></div>
        </div>
        <div class="cartao">
            <p>Fretes em trânsito</p>
            <div class="numero"><?= $fretesEmTransito ?></div>
        </div>
        <div class="cartao">
            <p>Fretes entregues</p>
            <div class="numero"><?= $fretesEntregues ?></div>
        </div>
        <div class="cartao">
            <p>Valor total dos fretes não cancelados</p>
            <div class="numero">R$ <?= number_format($valorTotal, 2, ",", ".") ?></div>
        </div>
    </section>

    <section class="painel filtro">
        <h2 class="h4 mb-3">Relatório por dia de envio</h2>
        <form method="GET" action="relatorios.php" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="data" class="form-label">Selecione a data</label>
                <input type="date" class="form-control" id="data" name="data"
                       value="<?= escapar($dataValida ? $dataFiltro : "") ?>" required>
            </div>
            <div class="col-md-7 acoes">
                <button class="btn btn-principal" type="submit">Filtrar por data</button>
                <a class="btn btn-success"
                   href="baixar_relatorio.php<?= $dataValida ? "?data=" . urlencode($dataFiltro) : "" ?>">
                    Baixar CSV
                </a>
                <a class="btn btn-outline-secondary" href="relatorios.php">Limpar filtro</a>
                <button class="btn btn-outline-primary" type="button" onclick="window.print()">Imprimir</button>
            </div>
        </form>
        <?php if ($dataValida): ?>
            <p class="mt-3 mb-0">Exibindo fretes enviados em <strong><?= dataFormatada($dataFiltro) ?></strong>.</p>
        <?php else: ?>
            <p class="text-secondary mt-3 mb-0">Selecione uma data para filtrar. Sem filtro, todos os fretes são exibidos.</p>
        <?php endif; ?>
    </section>

    <section class="painel">
        <h2 class="h4 mb-3">Detalhamento dos fretes</h2>

        <?php if (count($fretes) === 0): ?>
            <p class="text-secondary">Não há fretes para a data selecionada.</p>
        <?php else: ?>
            <div class="tabela">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Produtos e quantidades</th>
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
                                <td><?= (int)$frete["id"] ?></td>
                                <td>
                                    <?php foreach (($produtosPorFrete[$frete["id"]] ?? []) as $nomeProduto): ?>
                                        <div><?= escapar($nomeProduto) ?></div>
                                    <?php endforeach; ?>
                                </td>
                                <td><?= escapar($frete["origem"] . " - " . $frete["cidade_origem"]) ?></td>
                                <td><?= escapar($frete["destino"] . " - " . $frete["cidade_destino"]) ?></td>
                                <td><?= escapar($frete["transportadora"]) ?></td>
                                <td><?= dataFormatada($frete["data_envio"]) ?></td>
                                <td><?= dataFormatada($frete["data_entrega"]) ?></td>
                                <td>R$ <?= number_format((float)$frete["valor"], 2, ",", ".") ?></td>
                                <td><?= escapar($frete["status"]) ?></td>
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
