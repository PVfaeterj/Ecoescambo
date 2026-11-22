<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();

$campos = [
    'titulo' => trim((string) ($_POST['titulo'] ?? '')),
    'categoria' => trim((string) ($_POST['categoria'] ?? '')),
    'marca' => trim((string) ($_POST['marca'] ?? '')),
    'condicao' => (string) ($_POST['condicao'] ?? ''),
    'regiao' => (string) ($_POST['regiao'] ?? ''),
    'aceita_troca' => trim((string) ($_POST['aceita_troca'] ?? ''))
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    foreach (['titulo', 'categoria', 'aceita_troca'] as $campo) {
        if ($campos[$campo] === '') {
            $erros[$campo] = 'Campo obrigatório.';
        }
    }
    if (!in_array($campos['condicao'], ['novo', 'usado'], true)) {
        $erros['condicao'] = 'Selecione uma condição.';
    }
    if (!in_array($campos['regiao'], ['centro', 'zonasul', 'zonanorte', 'zonaoeste'], true)) {
        $erros['regiao'] = 'Selecione uma região.';
    }
    if (!$erros) {
        $inserir = banco()->prepare(
            'INSERT INTO produtos (usuario_id, titulo, categoria, marca, condicao, regiao, aceita_troca)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $inserir->execute([
            usuario_id(), $campos['titulo'], $campos['categoria'], $campos['marca'] ?: null,
            $campos['condicao'], $campos['regiao'], $campos['aceita_troca']
        ]);
        mensagem('Produto cadastrado com sucesso.');
        redirecionar('meus-produtos.php');
    }
}

$titulo = 'Cadastro de Produto';
require __DIR__ . '/../includes/cabecalho.php';
?>
<section class="form-box">
    <h1 class="page-title">Novo Produto</h1>
    <form action="cadastro-produto.php" method="post" novalidate>
        <?php campo_csrf(); ?>
        <label for="titulo">Título:</label>
        <input id="titulo" name="titulo" type="text" value="<?= escapar($campos['titulo']) ?>">
        <?php if (isset($erros['titulo'])): ?><p class="field-error"><?= escapar($erros['titulo']) ?></p><?php endif; ?>

        <label for="categoria">Categoria:</label>
        <input id="categoria" name="categoria" type="text" list="categorias" value="<?= escapar($campos['categoria']) ?>">
        <datalist id="categorias">
            <option value="Livros">
            <option value="Eletrônicos">
            <option value="Roupas e Calçados">
            <option value="Móveis e Decoração">
            <option value="Videogames e Consoles">
            <option value="Instrumentos Musicais">
            <option value="Esporte e Lazer">
        </datalist>
        <?php if (isset($erros['categoria'])): ?><p class="field-error"><?= escapar($erros['categoria']) ?></p><?php endif; ?>

        <label for="marca">Marca (opcional):</label>
        <input id="marca" name="marca" type="text" value="<?= escapar($campos['marca']) ?>">

        <label for="condicao">Condição:</label>
        <select id="condicao" name="condicao">
            <option value="">Selecione...</option>
            <option value="novo" <?= $campos['condicao'] === 'novo' ? 'selected' : '' ?>>Novo</option>
            <option value="usado" <?= $campos['condicao'] === 'usado' ? 'selected' : '' ?>>Usado</option>
        </select>
        <?php if (isset($erros['condicao'])): ?><p class="field-error"><?= escapar($erros['condicao']) ?></p><?php endif; ?>

        <label for="regiao">Região:</label>
        <select id="regiao" name="regiao">
            <option value="">Selecione...</option>
            <?php foreach (['centro', 'zonasul', 'zonanorte', 'zonaoeste'] as $opcao): ?>
                <option value="<?= escapar($opcao) ?>" <?= $campos['regiao'] === $opcao ? 'selected' : '' ?>><?= escapar(regiao_texto($opcao)) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($erros['regiao'])): ?><p class="field-error"><?= escapar($erros['regiao']) ?></p><?php endif; ?>

        <label for="aceita_troca">O que aceita em troca:</label>
        <input id="aceita_troca" name="aceita_troca" type="text" value="<?= escapar($campos['aceita_troca']) ?>">
        <?php if (isset($erros['aceita_troca'])): ?><p class="field-error"><?= escapar($erros['aceita_troca']) ?></p><?php endif; ?>

        <button type="submit" class="btn blue">Cadastrar</button>
    </form>
</section>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
