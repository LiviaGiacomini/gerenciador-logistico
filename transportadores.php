
<?php
require_once "conexao.php";

$mensagem = "";
$transportadorEdicao = null;

function escaparTransportador($valor) {
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $nome = trim($_POST["nome"] ?? "");
    $cnpj = trim($_POST["cnpj"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($nome === "") {
        $mensagem = "Informe o nome da transportadora.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Informe um e-mail válido.";
    } else {
        if ($id > 0) {
            $sql = "UPDATE transportadores
                    SET nome = ?, cnpj = ?, telefone = ?, email = ?
                    WHERE id = ?";
            $consulta = $conexao->prepare($sql);
            $consulta->execute([
                $nome, $cnpj ?: null, $telefone ?: null,
                $email ?: null, $id
            ]);

            header("Location: transportadores.php?editado=1");
            exit;
        } else {
            $sql = "INSERT INTO transportadores (nome, cnpj, telefone, email)
                    VALUES (?, ?, ?, ?)";
            $consulta = $conexao->prepare($sql);
            $consulta->execute([
                $nome, $cnpj ?: null, $telefone ?: null, $email ?: null
            ]);

            header("Location: transportadores.php?sucesso=1");
            exit;
        }
    }
}

if (isset($_GET["editar"])) {
    $consulta = $conexao->prepare(
        "SELECT * FROM transportadores WHERE id = ?"
    );
    $consulta->execute([(int)$_GET["editar"]]);
    $transportadorEdicao = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$transportadorEdicao) {
        header("Location: transportadores.php");
        exit;
    }
}

if (isset($_GET["sucesso"])) {
    $mensagem = "Transportadora cadastrada com sucesso!";
} elseif (isset($_GET["editado"])) {
    $mensagem = "Transportadora atualizada com sucesso!";
}

$transportadores = $conexao->query(
    "SELECT * FROM transportadores ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportadores | Gerenciador Logístico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f3f6fa; color: #253247; }
        header { background: #17365c; color: white; padding: 22px 30px; }
        header p { color: #d7e4f4; margin-bottom: 0; }
        nav { background: white; border-bottom: 1px solid #dfe5ed; }
        nav a { color: #344761; text-decoration: none; padding: 10px 13px; display: inline-block; border-radius: 6px; }
        nav a:hover, nav a.ativo { background: #e5eef9; color: #17365c; }
        main { max-width: 1100px; margin: 30px auto; padding: 0 22px; }
        .painel { background: white; border: 1px solid #e1e7ef; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
        .btn-principal { background: #17365c; color: white; }
        .btn-principal:hover { background: #245080; color: white; }
    </style>
</head>
<body>

<header>
    <h1>Gerenciador Logístico</h1>
    <p>Cadastro e controle de transportadores</p>
</header>

<nav class="px-4 py-2">
    <a href="index.php">Início</a>
    <a href="produtos.php">Produtos</a>
    <a href="enderecos.php">Endereços</a>
    <a href="transportadores.php" class="ativo">Transportadores</a>
    <a href="fretes.php">Fretes</a>
    <a href="relatorios.php">Relatórios</a>
</nav>

<main>
    <section class="painel">
        <h2 class="h4 mb-4">
            <?= $transportadorEdicao ? "Editar transportadora" : "Cadastrar transportadora" ?>
        </h2>

        <?php if ($mensagem !== ""): ?>
            <div class="alert <?= isset($_GET["sucesso"]) || isset($_GET["editado"]) ? "alert-success" : "alert-danger" ?>">
                <?= escaparTransportador($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="transportadores.php">
            <input type="hidden" name="id"
                   value="<?= (int)($transportadorEdicao["id"] ?? 0) ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label for="nome" class="form-label">Nome da transportadora *</label>
                    <input class="form-control" type="text" id="nome" name="nome"
                           maxlength="150" required
                           value="<?= escaparTransportador($transportadorEdicao["nome"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <input class="form-control" type="text" id="cnpj" name="cnpj"
                           maxlength="18" placeholder="00.000.000/0000-00"
                           value="<?= escaparTransportador($transportadorEdicao["cnpj"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input class="form-control" type="tel" id="telefone" name="telefone"
                           maxlength="20" placeholder="(00) 00000-0000"
                           value="<?= escaparTransportador($transportadorEdicao["telefone"] ?? "") ?>">
                </div>

                <div class="col-12">
                    <label for="email" class="form-label">E-mail</label>
                    <input class="form-control" type="email" id="email" name="email"
                           maxlength="150" placeholder="contato@transportadora.com.br"
                           value="<?= escaparTransportador($transportadorEdicao["email"] ?? "") ?>">
                </div>

                <div class="col-12">
                    <button class="btn btn-principal" type="submit">
                        <?= $transportadorEdicao ? "Salvar alterações" : "Cadastrar transportadora" ?>
                    </button>

                    <?php if ($transportadorEdicao): ?>
                        <a href="transportadores.php" class="btn btn-outline-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </section>

    <section class="painel">
        <h2 class="h4 mb-3">Transportadoras cadastradas</h2>

        <?php if (count($transportadores) === 0): ?>
            <p class="text-secondary">Nenhuma transportadora cadastrada ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transportadores as $transportador): ?>
                            <tr>
                                <td><?= (int)$transportador["id"] ?></td>
                                <td><?= escaparTransportador($transportador["nome"]) ?></td>
                                <td><?= escaparTransportador($transportador["cnpj"] ?: "—") ?></td>
                                <td><?= escaparTransportador($transportador["telefone"] ?: "—") ?></td>
                                <td><?= escaparTransportador($transportador["email"] ?: "—") ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="transportadores.php?editar=<?= (int)$transportador["id"] ?>">
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
