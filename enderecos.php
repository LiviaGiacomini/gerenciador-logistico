
<?php
require_once "conexao.php";

$mensagem = "";
$enderecoEditar = null;

function escaparEndereco($valor) {
    return htmlspecialchars((string) ($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        $mensagem = "Preencha a identificação e a cidade.";
    } elseif ($estado !== "" && !preg_match('/^[A-Z]{2}$/', $estado)) {
        $mensagem = "Informe a sigla do estado com duas letras.";
    } else {
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

            header("Location: enderecos.php?editado=1");
            exit;
        } else {
            $sql = "INSERT INTO enderecos
                    (nome, cep, estado, cidade, bairro, rua, numero, complemento)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $consulta = $conexao->prepare($sql);
            $consulta->execute($dados);

            header("Location: enderecos.php?sucesso=1");
            exit;
        }
    }
}

if (isset($_GET["editar"])) {
    $consulta = $conexao->prepare("SELECT * FROM enderecos WHERE id = ?");
    $consulta->execute([(int) $_GET["editar"]]);
    $enderecoEditar = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$enderecoEditar) {
        $mensagem = "Endereço não encontrado.";
    }
}

if (isset($_GET["sucesso"])) {
    $mensagem = "Endereço cadastrado com sucesso!";
} elseif (isset($_GET["editado"])) {
    $mensagem = "Endereço atualizado com sucesso!";
}

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
<style>
body { background: #f3f6fa; color: #253247; }
.cabecalho { background: #17365c; color: white; padding: 24px; }
.cabecalho p { margin-bottom: 0; color: #d7e4f4; }
main { max-width: 1200px; margin: 30px auto; }
.painel { background: white; border: 1px solid #e1e7ef; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
</style>
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
            <?= $enderecoEditar ? "Editar endereço" : "Cadastrar endereço" ?>
        </h2>

        <?php if ($mensagem !== ""): ?>
            <div class="alert <?= isset($_GET["sucesso"]) || isset($_GET["editado"]) ? "alert-success" : "alert-warning" ?>">
                <?= escaparEndereco($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="enderecos.php">
            <input type="hidden" name="id" value="<?= (int) ($enderecoEditar["id"] ?? 0) ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="nome">Identificação do endereço *</label>
                    <input class="form-control" id="nome" name="nome" maxlength="150" required value="<?= escaparEndereco($enderecoEditar["nome"] ?? "") ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="cep">CEP</label>
                    <input class="form-control" id="cep" name="cep" maxlength="10" value="<?= escaparEndereco($enderecoEditar["cep"] ?? "") ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="estado">Estado (UF)</label>
                    <input class="form-control text-uppercase" id="estado" name="estado" maxlength="2" value="<?= escaparEndereco($enderecoEditar["estado"] ?? "") ?>">
                </div>

                <div class="col-md-9">
                    <label class="form-label" for="cidade">Cidade *</label>
                    <input class="form-control" id="cidade" name="cidade" maxlength="100" required value="<?= escaparEndereco($enderecoEditar["cidade"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="bairro">Bairro</label>
                    <input class="form-control" id="bairro" name="bairro" maxlength="100" value="<?= escaparEndereco($enderecoEditar["bairro"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="rua">Rua</label>
                    <input class="form-control" id="rua" name="rua" maxlength="150" value="<?= escaparEndereco($enderecoEditar["rua"] ?? "") ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="numero">Número</label>
                    <input class="form-control" id="numero" name="numero" maxlength="20" value="<?= escaparEndereco($enderecoEditar["numero"] ?? "") ?>">
                </div>

                <div class="col-md-8">
                    <label class="form-label" for="complemento">Complemento</label>
                    <input class="form-control" id="complemento" name="complemento" maxlength="100" value="<?= escaparEndereco($enderecoEditar["complemento"] ?? "") ?>">
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <?= $enderecoEditar ? "Salvar alterações" : "Cadastrar endereço" ?>
                    </button>
                    <?php if ($enderecoEditar): ?>
                        <a href="enderecos.php" class="btn btn-outline-secondary">Cancelar edição</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </section>

    <section class="painel">
        <h2 class="h4 mb-3">Endereços cadastrados</h2>

        <?php if (count($enderecos) === 0): ?>
            <p class="text-secondary">Nenhum endereço cadastrado ainda.</p>
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
                            <td><?= escaparEndereco($endereco["nome"]) ?></td>
                            <td><?= escaparEndereco($endereco["cidade"] . "/" . $endereco["estado"]) ?></td>
                            <td><?= escaparEndereco($endereco["cep"]) ?></td>
                            <td><?= escaparEndereco($endereco["bairro"]) ?></td>
                            <td><?= escaparEndereco($endereco["rua"]) ?></td>
                            <td><?= escaparEndereco($endereco["numero"]) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="enderecos.php?editar=<?= (int) $endereco["id"] ?>">Editar</a>
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
