
<?php
session_start();

require_once __DIR__ . "/../includes/conexao.php";

function voltarEnderecos($mensagem, $tipo = "warning", $dados = []) {
    $_SESSION["endereco_mensagem"] = $mensagem;
    $_SESSION["endereco_tipo"] = $tipo;
    $_SESSION["endereco_dados"] = $dados;

    header("Location: ../enderecos.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../enderecos.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);
$nome = trim($_POST["nome"] ?? "");
$cep = trim($_POST["cep"] ?? "");
$estado = strtoupper(trim($_POST["estado"] ?? ""));
$cidade = trim($_POST["cidade"] ?? "");
$bairro = trim($_POST["bairro"] ?? "");
$rua = trim($_POST["rua"] ?? "");
$numero = trim($_POST["numero"] ?? "");
$complemento = trim($_POST["complemento"] ?? "");

if ($nome === "" || $cidade === "") {
    voltarEnderecos(
        "Preencha a identificação e a cidade.",
        "warning",
        $_POST
    );
}

if ($estado !== "" && !preg_match('/^[A-Z]{2}$/', $estado)) {
    voltarEnderecos(
        "Informe a sigla do estado com duas letras.",
        "warning",
        $_POST
    );
}

$dados = [
    $nome,
    $cep ?: null,
    $estado ?: null,
    $cidade,
    $bairro ?: null,
    $rua ?: null,
    $numero ?: null,
    $complemento ?: null
];

if ($id > 0) {
    $sql = "UPDATE enderecos
            SET nome = ?, cep = ?, estado = ?, cidade = ?,
                bairro = ?, rua = ?, numero = ?, complemento = ?
            WHERE id = ?";

    $consulta = $conexao->prepare($sql);
    $consulta->execute(array_merge($dados, [$id]));

    $_SESSION["endereco_mensagem"] = "Endereço atualizado com sucesso!";
} else {
    $sql = "INSERT INTO enderecos
            (nome, cep, estado, cidade, bairro, rua, numero, complemento)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $consulta = $conexao->prepare($sql);
    $consulta->execute($dados);

    $_SESSION["endereco_mensagem"] = "Endereço cadastrado com sucesso!";
}

$_SESSION["endereco_tipo"] = "success";

header("Location: ../enderecos.php");
exit;
