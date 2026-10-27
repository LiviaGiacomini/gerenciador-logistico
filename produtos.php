
<?php
require_once "conexao.php";

$mensagem = "";
$produtoEditar = null;

function escaparProduto($valor) {
    return htmlspecialchars((string) ($valor ?? ""), ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST["id"] ?? 0);
    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $peso = trim($_POST["peso"] ?? "");
    $comprimento = trim($_POST["comprimento"] ?? "");
    $largura = trim($_POST["largura"] ?? "");
    $altura = trim($_POST["altura"] ?? "");

    if ($nome === "") {
        $mensagem = "Informe o nome do produto.";
    } else {
        $medidas = [$peso, $comprimento, $largura, $altura];
        $medidasValidas = true;

        foreach ($medidas as $medida) {
            if ($medida !== "" &&
                (!is_numeric(str_replace(",", ".", $medida)) ||
                (float) str_replace(",", ".", $medida) < 0)) {
                $medidasValidas = false;
            }
        }

        if (!$medidasValidas) {
            $mensagem = "Informe medidas numéricas válidas e não negativas.";
        } else {
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
                    $nome, $descricao ?: null, $peso,
                    $comprimento, $largura, $altura, $id
                ]);

                header("Location: produtos.php?editado=1");
                exit;
            } else {
                $sql = "INSERT INTO produtos
                        (nome, descricao, peso, comprimento, largura, altura)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $consulta = $conexao->prepare($sql);
                $consulta->execute([
                    $nome, $descricao ?: null, $peso,
                    $comprimento, $largura, $altura
                ]);

                header("Location: produtos.php?sucesso=1");
                exit;
            }
        }
    }
}

if (isset($_GET["editar"])) {
    $idEditar = (int) $_GET["editar"];
    $consulta = $conexao->prepare("SELECT * FROM produtos WHERE id = ?");
    $consulta->execute([$idEditar]);
    $produtoEditar = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$produtoEditar) {
        $mensagem = "Produto não encontrado.";
    }
}

if (isset($_GET["sucesso"])) {
    $mensagem = "Produto cadastrado com sucesso!";
} elseif (isset($_GET["editado"])) {
    $mensagem = "Produto atualizado com sucesso!";
}

$produtos = $conexao->query(
    "SELECT * FROM produtos ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produtos | Gerenciador Logístico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f3f6fa; color: #253247; }
.cabecalho { background: #17365c; color: white; padding: 24px; }
.cabecalho p { margin-bottom: 0; color: #d7e4f4; }
main { max-width: 1200px; margin: 30px auto; }
.painel { background: white; border: 1px solid #e1e7ef; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
nav a { text-decoration: none; }
</style>
</head>
<body>
<header class="cabecalho">
    <h1>Gerenciador Logístico</h1>
    <p>Cadastro, consulta e edição de produtos</p>
</header>

<nav class="navbar navbar-expand-lg bg-white border-bottom px-3">
    <div class="container-fluid">
        <div class="navbar-nav flex-row flex-wrap gap-2">
            <a class="nav-link" href="index.php">Início</a>
            <a class="nav-link active fw-bold" href="produtos.php">Produtos</a>
            <a class="nav-link" href="enderecos.php">Endereços</a>
            <a class="nav-link" href="transportadores.php">Transportadores</a>
            <a class="nav-link" href="fretes.php">Fretes</a>
            <a class="nav-link" href="relatorios.php">Relatórios</a>
        </div>
    </div>
</nav>

<main class="container">
    <section class="painel">
        <h2 class="h4 mb-3">
            <?= $produtoEditar ? "Editar produto" : "Cadastrar produto" ?>
        </h2>

        <?php if ($mensagem !== ""): ?>
            <div class="alert <?= isset($_GET["sucesso"]) || isset($_GET["editado"]) ? "alert-success" : "alert-warning" ?>">
                <?= escaparProduto($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="produtos.php">
            <input type="hidden" name="id" value="<?= (int) ($produtoEditar["id"] ?? 0) ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label for="nome" class="form-label">Nome do produto *</label>
                    <input class="form-control" type="text" id="nome" name="nome" maxlength="150" required value="<?= escaparProduto($produtoEditar["nome"] ?? "") ?>">
                </div>

                <div class="col-12">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= escaparProduto($produtoEditar["descricao"] ?? "") ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="peso" class="form-label">Peso (kg)</label>
                    <input class="form-control" type="number" id="peso" name="peso" min="0" step="0.01" value="<?= escaparProduto($produtoEditar["peso"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                    <input class="form-control" type="number" id="comprimento" name="comprimento" min="0" step="0.01" value="<?= escaparProduto($produtoEditar["comprimento"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label for="largura" class="form-label">Largura (cm)</label>
                    <input class="form-control" type="number" id="largura" name="largura" min="0" step="0.01" value="<?= escaparProduto($produtoEditar["largura"] ?? "") ?>">
                </div>

                <div class="col-md-6">
                    <label for="altura" class="form-label">Altura (cm)</label>
                    <input class="form-control" type="number" id="altura" name="altura" min="0" step="0.01" value="<?= escaparProduto($produtoEditar["altura"] ?? "") ?>">
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary">
                        <?= $produtoEditar ? "Salvar alterações" : "Cadastrar produto" ?>
                    </button>

                    <?php if ($produtoEditar): ?>
                        <a href="produtos.php" class="btn btn-outline-secondary">Cancelar edição</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </section>

    <section class="painel">
        <h2 class="h4 mb-3">Produtos cadastrados</h2>

        <?php if (count($produtos) === 0): ?>
            <p class="text-secondary">Nenhum produto cadastrado ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Peso (kg)</th>
                            <th>Dimensões (cm)</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?= (int) $produto["id"] ?></td>
                            <td><?= escaparProduto($produto["nome"]) ?></td>
                            <td><?= escaparProduto($produto["descricao"]) ?></td>
                            <td><?= escaparProduto($produto["peso"]) ?></td>
                            <td>
                                <?= escaparProduto($produto["comprimento"]) ?> ×
                                <?= escaparProduto($produto["largura"]) ?> ×
                                <?= escaparProduto($produto["altura"]) ?>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="produtos.php?editar=<?= (int) $produto["id"] ?>">Editar</a>
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
