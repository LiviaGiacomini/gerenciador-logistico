
<?php
session_start();

require_once __DIR__ . "/../includes/conexao.php";

function voltarFretes($mensagem, $dados = []) {
    $_SESSION["frete_mensagem"] = $mensagem;
    $_SESSION["frete_tipo"] = "danger";
    $_SESSION["frete_dados"] = $dados;

    header("Location: ../fretes.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../fretes.php");
    exit;
}

$produtosSelecionados = $_POST["produtos"] ?? [];
$quantidades = $_POST["quantidades"] ?? [];

$origem_id = (int) ($_POST["origem_id"] ?? 0);
$destino_id = (int) ($_POST["destino_id"] ?? 0);
$transportador_id = (int) ($_POST["transportador_id"] ?? 0);

$data_envio = trim($_POST["data_envio"] ?? "");
$data_entrega = trim($_POST["data_entrega"] ?? "");

$data_envio = $data_envio !== "" ? $data_envio : null;
$data_entrega = $data_entrega !== "" ? $data_entrega : null;

$valor = str_replace(",", ".", $_POST["valor"] ?? "0");
$status = $_POST["status"] ?? "Pendente";
$observacoes = trim($_POST["observacoes"] ?? "");

$statusValidos = [
    "Pendente",
    "Em trânsito",
    "Entregue",
    "Cancelado"
];

$produtosValidos = [];

if (is_array($produtosSelecionados) && is_array($quantidades)) {
    foreach ($produtosSelecionados as $produtoId) {
        $produtoId = (int) $produtoId;
        $quantidade = (int) ($quantidades[$produtoId] ?? 1);

        if ($produtoId > 0 && $quantidade > 0) {
            $produtosValidos[$produtoId] = $quantidade;
        }
    }
}

if (
    count($produtosValidos) === 0 ||
    $origem_id <= 0 ||
    $destino_id <= 0 ||
    $transportador_id <= 0
) {
    voltarFretes(
        "Selecione pelo menos um produto, a origem, o destino e a transportadora.",
        $_POST
    );
}

if ($origem_id === $destino_id) {
    voltarFretes(
        "A origem e o destino precisam ser diferentes.",
        $_POST
    );
}

if (!is_numeric($valor) || (float) $valor < 0) {
    voltarFretes("Informe um valor de frete válido.", $_POST);
}

if (!in_array($status, $statusValidos, true)) {
    voltarFretes("Selecione um status válido.", $_POST);
}

// Validação das datas.
if ($data_envio !== null && $data_entrega !== null) {

    $envio = DateTime::createFromFormat("!Y-m-d", $data_envio);
    $entrega = DateTime::createFromFormat("!Y-m-d", $data_entrega);

    $errosEnvio = DateTime::getLastErrors();

    if (
        !$envio ||
        ($errosEnvio !== false &&
        ($errosEnvio["warning_count"] > 0 || $errosEnvio["error_count"] > 0)) ||
        $envio->format("Y-m-d") !== $data_envio
    ) {
        voltarFretes("Informe uma data de envio válida.", $_POST);
    }

    $errosEntrega = DateTime::getLastErrors();

    if (
        !$entrega ||
        ($errosEntrega !== false &&
        ($errosEntrega["warning_count"] > 0 || $errosEntrega["error_count"] > 0)) ||
        $entrega->format("Y-m-d") !== $data_entrega
    ) {
        voltarFretes("Informe uma data de entrega válida.", $_POST);
    }

    if ($entrega < $envio) {
        voltarFretes(
            "A data de entrega não pode ser anterior à data de envio.",
            $_POST
        );
    }
}

try {
    $conexao->beginTransaction();

    // Confere se os endereços existem.
    $consulta = $conexao->prepare(
        "SELECT COUNT(*) FROM enderecos WHERE id = ?"
    );
    $consulta->execute([$origem_id]);

    if ((int) $consulta->fetchColumn() === 0) {
        throw new Exception("Endereço de origem não encontrado.");
    }

    $consulta = $conexao->prepare(
        "SELECT COUNT(*) FROM enderecos WHERE id = ?"
    );
    $consulta->execute([$destino_id]);

    if ((int) $consulta->fetchColumn() === 0) {
        throw new Exception("Endereço de destino não encontrado.");
    }

    // Confere se a transportadora existe.
    $consulta = $conexao->prepare(
        "SELECT COUNT(*) FROM transportadores WHERE id = ?"
    );
    $consulta->execute([$transportador_id]);

    if ((int) $consulta->fetchColumn() === 0) {
        throw new Exception("Transportadora não encontrada.");
    }

    // Confere se todos os produtos existem.
    $consultaProduto = $conexao->prepare(
        "SELECT COUNT(*) FROM produtos WHERE id = ?"
    );

    foreach ($produtosValidos as $produtoId => $quantidade) {
        $consultaProduto->execute([$produtoId]);

        if ((int) $consultaProduto->fetchColumn() === 0) {
            throw new Exception(
                "Um dos produtos selecionados não foi encontrado."
            );
        }
    }

    // Mantém o primeiro produto na coluna antiga para compatibilidade.
    $primeiroProduto = (int) array_key_first($produtosValidos);

    $sql = "INSERT INTO fretes
            (produto_id, origem_id, destino_id, transportador_id,
             data_envio, data_entrega, valor, status, observacoes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        $primeiroProduto,
        $origem_id,
        $destino_id,
        $transportador_id,
        $data_envio,
        $data_entrega,
        $valor,
        $status,
        $observacoes ?: null
    ]);

    $freteId = (int) $conexao->lastInsertId();

    // Registra todos os produtos vinculados ao frete.
    $sqlProduto = "INSERT INTO frete_produtos
                   (frete_id, produto_id, quantidade)
                   VALUES (?, ?, ?)";

    $consultaProduto = $conexao->prepare($sqlProduto);

    foreach ($produtosValidos as $produtoId => $quantidade) {
        $consultaProduto->execute([
            $freteId,
            $produtoId,
            $quantidade
        ]);
    }

    $conexao->commit();

    $_SESSION["frete_mensagem"] = "Frete cadastrado com sucesso!";
    $_SESSION["frete_tipo"] = "success";

    header("Location: ../fretes.php");
    exit;

} catch (Throwable $e) {

    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    voltarFretes(
        "Não foi possível cadastrar o frete. Verifique se os dados estão corretos.",
        $_POST
    );
}
