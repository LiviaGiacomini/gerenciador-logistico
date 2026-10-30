
<?php
session_start();

require_once __DIR__ . "/../includes/conexao.php";

function voltarProdutos($mensagem, $tipo = "warning", $dados = []) {
    $_SESSION["produto_mensagem"] = $mensagem;
    $_SESSION["produto_tipo"] = $tipo;
    $_SESSION["produto_dados"] = $dados;

    header("Location: ../produtos.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../produtos.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);
$nome = trim($_POST["nome"] ?? "");
$descricao = trim($_POST["descricao"] ?? "");
$peso = trim($_POST["peso"] ?? "");
$comprimento = trim($_POST["comprimento"] ?? "");
$largura = trim($_POST["largura"] ?? "");
$altura = trim($_POST["altura"] ?? "");

if ($nome === "") {
    voltarProdutos("Informe o nome do produto.", "warning", $_POST);
}

$medidas = [$peso, $comprimento, $largura, $altura];
$medidasValidas = true;

foreach ($medidas as $medida) {
    $medidaNormalizada = str_replace(",", ".", $medida);

    if (
        $medida !== "" &&
        (!is_numeric($medidaNormalizada) || (float)$medidaNormalizada < 0)
    ) {
        $medidasValidas = false;
        break;
    }
}

if (!$medidasValidas) {
    voltarProdutos(
        "Informe medidas numéricas válidas e não negativas.",
        "warning",
        $_POST
    );
}

$peso = $peso !== "" ? str_replace(",", ".", $peso) : null;
$comprimento = $comprimento !== "" ? str_replace(",", ".", $comprimento) : null;
$largura = $largura !== "" ? str_replace(",", ".", $largura) : null;
$altura = $altura !== "" ? str_replace(",", ".", $altura) : null;

if ($id > 0) {
    $sql = "UPDATE produtos
            SET nome = ?, descricao = ?, peso = ?,
                comprimento = ?, largura = ?, altura = ?
            WHERE id = ?";

    $consulta = $conexao->prepare($sql);

    $consulta->execute([
        $nome,
        $descricao ?: null,
        $peso,
        $comprimento,
        $largura,
        $altura,
        $id
    ]);

    $_SESSION["produto_mensagem"] = "Produto atualizado com sucesso!";
    $_SESSION["produto_tipo"] = "success";

    header("Location: ../produtos.php");
    exit;
}

$sql = "INSERT INTO produtos
        (nome, descricao, peso, comprimento, largura, altura)
        VALUES (?, ?, ?, ?, ?, ?)";

$consulta = $conexao->prepare($sql);

$consulta->execute([
    $nome,
    $descricao ?: null,
    $peso,
    $comprimento,
    $largura,
    $altura
]);

$_SESSION["produto_mensagem"] = "Produto cadastrado com sucesso!";
$_SESSION["produto_tipo"] = "success";

header("Location: ../produtos.php");
exit;
