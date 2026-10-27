
<?php
require_once "conexao.php";

$mensagem = "";

function escaparFrete($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $produtosSelecionados = $_POST["produtos"] ?? [];
    $quantidades = $_POST["quantidades"] ?? [];
    $origem_id = (int)($_POST["origem_id"] ?? 0);
    $destino_id = (int)($_POST["destino_id"] ?? 0);
    $transportador_id = (int)($_POST["transportador_id"] ?? 0);
    $data_envio = ($_POST["data_envio"] ?? "") ?: null;
    $data_entrega = ($_POST["data_entrega"] ?? "") ?: null;
    $valor = str_replace(",", ".", $_POST["valor"] ?? "0");
    $status = $_POST["status"] ?? "Pendente";
    $observacoes = trim($_POST["observacoes"] ?? "");

    $statusValidos = ["Pendente", "Em trânsito", "Entregue", "Cancelado"];
    $produtosValidos = [];

    foreach ($produtosSelecionados as $produtoId) {
        $produtoId = (int)$produtoId;
        $quantidade = (int)($quantidades[$produtoId] ?? 1);

        if ($produtoId > 0 && $quantidade > 0) {
            $produtosValidos[$produtoId] = $quantidade;
        }
    }

    if (count($produtosValidos) === 0 || $origem_id <= 0 ||
        $destino_id <= 0 || $transportador_id <= 0) {
        $mensagem = "Selecione pelo menos um produto, a origem, o destino e a transportadora.";
    } elseif ($origem_id === $destino_id) {
        $mensagem = "A origem e o destino precisam ser diferentes.";
    } elseif (!is_numeric($valor) || (float)$valor < 0) {
        $mensagem = "Informe um valor de frete válido.";
    } elseif (!in_array($status, $statusValidos, true)) {
        $mensagem = "Selecione um status válido.";
    } elseif ($data_envio && $data_entrega && $data_entrega < $data_envio) {
        $mensagem = "A data de entrega não pode ser anterior à data de envio.";
    } else {
        try {
            $conexao->beginTransaction();

            // O primeiro produto também é salvo na coluna antiga
            // para manter compatibilidade com os fretes já existentes.
            $primeiroProduto = (int)array_key_first($produtosValidos);

            $sql = "INSERT INTO fretes
                    (produto_id, origem_id, destino_id, transportador_id,
                     data_envio, data_entrega, valor, status, observacoes)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $consulta = $conexao->prepare($sql);
            $consulta->execute([
                $primeiroProduto, $origem_id, $destino_id, $transportador_id,
                $data_envio, $data_entrega, $valor, $status,
                $observacoes ?: null
            ]);

            $freteId = (int)$conexao->lastInsertId();

            $sqlProduto = "INSERT INTO frete_produtos
                           (frete_id, produto_id, quantidade)
                           VALUES (?, ?, ?)";
            $consultaProduto = $conexao->prepare($sqlProduto);

            foreach ($produtosValidos as $produtoId => $quantidade) {
                $consultaProduto->execute([$freteId, $produtoId, $quantidade]);
            }

            $conexao->commit();

            header("Location: fretes.php?sucesso=1");
            exit;
        } catch (Throwable $e) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }
            $mensagem = "Não foi possível cadastrar o frete. Verifique se os dados estão corretos.";
        }
    }
}

if (isset($_GET["sucesso"])) {
    $mensagem = "Frete cadastrado com sucesso!";
}

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

// Compatibilidade para fretes antigos que ainda não tenham itens na tabela nova.
foreach ($fretes as $frete) {
    if (empty($produtosPorFrete[$frete["id"]])) {
        $consultaAntigo = $conexao->prepare(
            "SELECT nome FROM produtos WHERE id = ?"
        );
        $consultaAntigo->execute([$frete["produto_id"]]);
        $nomeAntigo = $consultaAntigo->fetchColumn();

        if ($nomeAntigo) {
            $produtosPorFrete[$frete["id"]][] = $nomeAntigo . " (qtd.: 1)";
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
<style>
body { background: #f3f6fa; color: #253247; }
header { background: #17365c; color: white; padding: 22px 30px; }
header p { color: #d7e4f4; margin-bottom: 0; }
nav { background: white; border-bottom: 1px solid #dfe5ed; }
nav a { color: #344761; text-decoration: none; padding: 10px 13px; display: inline-block; border-radius: 6px; }
nav a:hover, nav a.ativo { background: #e5eef9; color: #17365c; }
main { max-width: 1200px; margin: 30px auto; padding: 0 22px; }
.painel { background: white; border: 1px solid #e1e7ef; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
.btn-principal { background: #17365c; color: white; }
.btn-principal:hover { background: #245080; color: white; }
.lista-produtos { border: 1px solid #dce3ec; border-radius: 8px; padding: 14px; }
.item-produto { display: grid; grid-template-columns: 1fr 110px; gap: 12px; align-items: center; padding: 8px 0; border-bottom: 1px solid #edf0f4; }
.item-produto:last-child { border-bottom: 0; }
@media (max-width: 600px) { .item-produto { grid-template-columns: 1fr; } }
</style>
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
        <div class="alert <?= isset($_GET["sucesso"]) ? "alert-success" : "alert-danger" ?>">
            <?= escaparFrete($mensagem) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="fretes.php">
        <div class="row g-3">

            <div class="col-12">
                <label class="form-label">Produtos e quantidades *</label>
                <div class="lista-produtos">
                    <?php if (!$produtos): ?>
                        <p class="mb-0">Cadastre pelo menos um produto antes de criar um frete.</p>
                    <?php else: ?>
                        <?php foreach ($produtos as $produto): ?>
                            <div class="item-produto">
                                <div class="form-check">
                                    <input class="form-check-input produto-check"
                                           type="checkbox"
                                           name="produtos[]"
                                           value="<?= (int)$produto["id"] ?>"
                                           id="produto_<?= (int)$produto["id"] ?>">
                                    <label class="form-check-label"
                                           for="produto_<?= (int)$produto["id"] ?>">
                                        <?= escaparFrete($produto["nome"]) ?>
                                    </label>
                                </div>
                                <div>
                                    <label class="form-label small"
                                           for="qtd_<?= (int)$produto["id"] ?>">Quantidade</label>
                                    <input class="form-control form-control-sm"
                                           type="number"
                                           name="quantidades[<?= (int)$produto["id"] ?>]"
                                           id="qtd_<?= (int)$produto["id"] ?>"
                                           value="1" min="1" step="1">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <small class="text-secondary">Marque um ou mais produtos e informe a quantidade de cada um.</small>
            </div>

            <div class="col-md-6">
                <label for="transportador_id" class="form-label">Transportadora *</label>
                <select class="form-select" id="transportador_id" name="transportador_id" required>
                    <option value="">Selecione uma transportadora</option>
                    <?php foreach ($transportadores as $transportador): ?>
                        <option value="<?= (int)$transportador["id"] ?>">
                            <?= escaparFrete($transportador["nome"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="origem_id" class="form-label">Endereço de origem *</label>
                <select class="form-select" id="origem_id" name="origem_id" required>
                    <option value="">Selecione a origem</option>
                    <?php foreach ($enderecos as $endereco): ?>
                        <option value="<?= (int)$endereco["id"] ?>">
                            <?= escaparFrete($endereco["nome"] . " - " . $endereco["cidade"] . "/" . $endereco["estado"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="destino_id" class="form-label">Endereço de destino *</label>
                <select class="form-select" id="destino_id" name="destino_id" required>
                    <option value="">Selecione o destino</option>
                    <?php foreach ($enderecos as $endereco): ?>
                        <option value="<?= (int)$endereco["id"] ?>">
                            <?= escaparFrete($endereco["nome"] . " - " . $endereco["cidade"] . "/" . $endereco["estado"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="data_envio" class="form-label">Data de envio</label>
                <input class="form-control" type="date" id="data_envio" name="data_envio">
            </div>

            <div class="col-md-6">
                <label for="data_entrega" class="form-label">Data prevista de entrega</label>
                <input class="form-control" type="date" id="data_entrega" name="data_entrega">
            </div>

            <div class="col-md-6">
                <label for="valor" class="form-label">Valor do frete (R$) *</label>
                <input class="form-control" type="number" id="valor" name="valor"
                       min="0" step="0.01" value="0.00" required>
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">Status *</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Pendente">Pendente</option>
                    <option value="Em trânsito">Em trânsito</option>
                    <option value="Entregue">Entregue</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>

            <div class="col-12">
                <label for="observacoes" class="form-label">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes"
                          rows="3" placeholder="Informações adicionais sobre o frete"></textarea>
            </div>

            <div class="col-12">
                <button class="btn btn-principal" type="submit">Cadastrar frete</button>
            </div>
        </div>
    </form>
</section>

<section class="painel">
    <h2 class="h4 mb-3">Fretes cadastrados</h2>

    <?php if (count($fretes) === 0): ?>
        <p class="text-secondary">Nenhum frete cadastrado ainda.</p>
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
                            <td><?= (int)$frete["id"] ?></td>
                            <td>
                                <?php foreach (($produtosPorFrete[$frete["id"]] ?? []) as $nomeProduto): ?>
                                    <div><?= escaparFrete($nomeProduto) ?></div>
                                <?php endforeach; ?>
                            </td>
                            <td><?= escaparFrete($frete["origem_nome"] . " - " . $frete["origem_cidade"]) ?></td>
                            <td><?= escaparFrete($frete["destino_nome"] . " - " . $frete["destino_cidade"]) ?></td>
                            <td><?= escaparFrete($frete["transportador_nome"]) ?></td>
                            <td><?= $frete["data_envio"] ? date("d/m/Y", strtotime($frete["data_envio"])) : "—" ?></td>
                            <td><?= $frete["data_entrega"] ? date("d/m/Y", strtotime($frete["data_entrega"])) : "—" ?></td>
                            <td>R$ <?= number_format((float)$frete["valor"], 2, ",", ".") ?></td>
                            <td><?= escaparFrete($frete["status"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
</main>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const produtos = document.querySelectorAll(
        'input[name="produtos[]"]'
    );

    produtos.forEach(function (produto) {
        produto.addEventListener("change", function () {
            const quantidade = document.querySelector(
                'input[name="quantidades[' + produto.value + ']"]'
            );

            if (quantidade) {
                quantidade.disabled = !produto.checked;

                if (!produto.checked) {
                    quantidade.value = 1;
                }
            }
        });
    });
});
</script>
</body>
</html>
