
<?php
session_start();

require_once __DIR__ . "/../includes/conexao.php";

function voltarTransportadores($mensagem, $tipo = "danger", $dados = []) {
    $_SESSION["transportador_mensagem"] = $mensagem;
    $_SESSION["transportador_tipo"] = $tipo;
    $_SESSION["transportador_dados"] = $dados;

    header("Location: ../transportadores.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../transportadores.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);
$nome = trim($_POST["nome"] ?? "");
$cnpj = trim($_POST["cnpj"] ?? "");
$telefone = trim($_POST["telefone"] ?? "");
$email = trim($_POST["email"] ?? "");

if ($nome === "") {
    voltarTransportadores(
        "Informe o nome da transportadora.",
        "danger",
        $_POST
    );
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    voltarTransportadores(
        "Informe um e-mail válido.",
        "danger",
        $_POST
    );
}

$dados = [
    $nome,
    $cnpj ?: null,
    $telefone ?: null,
    $email ?: null
];

if ($id > 0) {
    $sql = "UPDATE transportadores
            SET nome = ?, cnpj = ?, telefone = ?, email = ?
            WHERE id = ?";

    $consulta = $conexao->prepare($sql);
    $consulta->execute(array_merge($dados, [$id]));

    $_SESSION["transportador_mensagem"] =
        "Transportadora atualizada com sucesso!";

} else {
    $sql = "INSERT INTO transportadores
            (nome, cnpj, telefone, email)
            VALUES (?, ?, ?, ?)";

    $consulta = $conexao->prepare($sql);
    $consulta->execute($dados);

    $_SESSION["transportador_mensagem"] =
        "Transportadora cadastrada com sucesso!";
}

$_SESSION["transportador_tipo"] = "success";

header("Location: ../transportadores.php");
exit;
