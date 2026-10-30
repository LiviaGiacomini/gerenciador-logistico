
<?php
require_once __DIR__ . "/includes/conexao.php";
$data = $_GET["data"] ?? "";

if ($data !== "") {
    $dataObj = DateTime::createFromFormat("!Y-m-d", $data);

    if (!$dataObj || $dataObj->format("Y-m-d") !== $data) {
        http_response_code(400);
        exit("Data inválida. Selecione uma data válida no relatório.");
    }
}

$sql = "SELECT
            f.id,
            f.data_envio,
            f.data_entrega,
            f.valor,
            f.status,
            f.produto_id,
            o.nome AS origem,
            o.cidade AS cidade_origem,
            d.nome AS destino,
            d.cidade AS cidade_destino,
            t.nome AS transportadora
        FROM fretes f
        INNER JOIN enderecos o ON f.origem_id = o.id
        INNER JOIN enderecos d ON f.destino_id = d.id
        INNER JOIN transportadores t ON f.transportador_id = t.id";

$params = [];

if ($data !== "") {
    $sql .= " WHERE f.data_envio = ?";
    $params[] = $data;
}

$sql .= " ORDER BY f.id DESC";

$stmt = $conexao->prepare($sql);
$stmt->execute($params);
$fretes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtItens = $conexao->query(
    "SELECT fp.frete_id, p.nome, fp.quantidade
     FROM frete_produtos fp
     INNER JOIN produtos p ON p.id = fp.produto_id"
);

$itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
$produtosPorFrete = [];

foreach ($itens as $item) {
    $produtosPorFrete[$item["frete_id"]][] =
        $item["nome"] . " (quantidade: " . $item["quantidade"] . ")";
}

$stmtProduto = $conexao->prepare(
    "SELECT nome FROM produtos WHERE id = ?"
);

foreach ($fretes as $frete) {
    $idFrete = $frete["id"];

    if (empty($produtosPorFrete[$idFrete])) {
        $stmtProduto->execute([$frete["produto_id"]]);
        $nomeProduto = $stmtProduto->fetchColumn();

        if ($nomeProduto) {
            $produtosPorFrete[$idFrete][] =
                $nomeProduto . " (quantidade: 1)";
        }
    }
}

$nomeArquivo = $data !== ""
    ? "relatorio_fretes_" . $data . ".csv"
    : "relatorio_fretes.csv";

header("Content-Type: text/csv; charset=UTF-8");
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
header("Pragma: no-cache");

echo "\xEF\xBB\xBF";

$saida = fopen("php://output", "w");

fputcsv($saida, [
    "ID",
    "Produtos e quantidades",
    "Origem",
    "Cidade de origem",
    "Destino",
    "Cidade de destino",
    "Transportadora",
    "Data de envio",
    "Data de entrega",
    "Valor (R$)",
    "Status"
], ";");

foreach ($fretes as $frete) {
    $listaProdutos = implode(
        " | ",
        $produtosPorFrete[$frete["id"]] ?? []
    );

    fputcsv($saida, [
        $frete["id"],
        $listaProdutos,
        $frete["origem"],
        $frete["cidade_origem"],
        $frete["destino"],
        $frete["cidade_destino"],
        $frete["transportadora"],
        $frete["data_envio"]
            ? date("d/m/Y", strtotime($frete["data_envio"]))
            : "",
        $frete["data_entrega"]
            ? date("d/m/Y", strtotime($frete["data_entrega"]))
            : "",
        number_format((float) $frete["valor"], 2, ",", "."),
        $frete["status"]
    ], ";");
}

fclose($saida);
exit;
?>
