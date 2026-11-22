<?php
require_once __DIR__ . '/../includes/funcoes.php';

$busca = trim((string) ($_GET['busca'] ?? ''));
$condicao = (string) ($_GET['condicao'] ?? '');
$regiao = (string) ($_GET['regiao'] ?? '');
$ordem = (string) ($_GET['ordem'] ?? '');
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 5;

$filtros = ["p.estado = 'Em aberto'"];
$valores = [];
if ($busca !== '') {
    $filtros[] = 'p.titulo LIKE ?';
    $valores[] = '%' . $busca . '%';
}
if (in_array($condicao, ['novo', 'usado'], true)) {
    $filtros[] = 'p.condicao = ?';
    $valores[] = $condicao;
}
if (in_array($regiao, ['centro', 'zonasul', 'zonanorte', 'zonaoeste'], true)) {
    $filtros[] = 'p.regiao = ?';
    $valores[] = $regiao;
}

$onde = implode(' AND ', $filtros);
$contar = banco()->prepare('SELECT COUNT(*) FROM produtos p WHERE ' . $onde);
$contar->execute($valores);
$total = (int) $contar->fetchColumn();
$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina = min($pagina, $totalPaginas);
$ordemSql = $ordem === 'za' ? 'p.titulo DESC' : ($ordem === 'az' ? 'p.titulo ASC' : 'p.id ASC');

$consulta = banco()->prepare(
    'SELECT p.*, u.nome AS ofertante FROM produtos p
     JOIN usuarios u ON u.id = p.usuario_id
     WHERE ' . $onde . ' ORDER BY ' . $ordemSql . ' LIMIT ? OFFSET ?'
);
foreach ($valores as $indice => $valor) {
    $consulta->bindValue($indice + 1, $valor, PDO::PARAM_STR);
}
$consulta->bindValue(count($valores) + 1, $porPagina, PDO::PARAM_INT);
$consulta->bindValue(count($valores) + 2, ($pagina - 1) * $porPagina, PDO::PARAM_INT);
$consulta->execute();
$produtos = $consulta->fetchAll();

function link_pagina(int $numero): string
{
    $parametros = $_GET;
    $parametros['pagina'] = $numero;
    return 'catalogo.php?' . http_build_query($parametros);
}

$titulo = 'Catálogo';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title">Catálogo</h1>
<div class="filters">
    <form method="get" action="catalogo.php">
        <label for="busca">Filtrar:</label>
        <input id="busca" name="busca" type="text" placeholder="Pesquisar..." value="<?= escapar($busca) ?>">
        <select name="condicao" aria-label="Condição">
            <option value="">Qualquer condição</option>
            <option value="novo" <?= $condicao === 'novo' ? 'selected' : '' ?>>Novo</option>
            <option value="usado" <?= $condicao === 'usado' ? 'selected' : '' ?>>Usado</option>
        </select>
        <select name="regiao" aria-label="Região">
            <option value="">Qualquer região</option>
            <?php foreach (['centro', 'zonasul', 'zonanorte', 'zonaoeste'] as $opcao): ?>
                <option value="<?= escapar($opcao) ?>" <?= $regiao === $opcao ? 'selected' : '' ?>><?= escapar(regiao_texto($opcao)) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="ordem" aria-label="Ordem">
            <option value="">Ordem original</option>
            <option value="az" <?= $ordem === 'az' ? 'selected' : '' ?>>A-Z</option>
            <option value="za" <?= $ordem === 'za' ? 'selected' : '' ?>>Z-A</option>
        </select>
        <button class="btn blue" type="submit">Filtrar</button>
    </form>
</div>
<section class="product-list">
    <?php if (!$produtos): ?><p>Nenhum produto disponível.</p><?php endif; ?>
    <?php foreach ($produtos as $produto): ?>
        <article class="product-row">
            <a href="produto.php?id=<?= (int) $produto['id'] ?>">
                <img class="product-image" src="<?= escapar(imagem_produto($produto['categoria'])) ?>" alt="Produto">
            </a>
            <div class="product-info">
                <a href="produto.php?id=<?= (int) $produto['id'] ?>"><?= escapar($produto['titulo']) ?></a>
                <p>Condição: <?= $produto['condicao'] === 'novo' ? 'Novo' : 'Usado' ?> | Região: <?= escapar(regiao_texto($produto['regiao'])) ?></p>
                <p>Aceita: <?= escapar($produto['aceita_troca']) ?></p>
                <p>Ofertante: <?= escapar($produto['ofertante']) ?></p>
            </div>
            <div class="actions">
                <?php if (usuario_id() !== (int) $produto['usuario_id']): ?>
                    <form method="post" action="interesse.php">
                        <?php campo_csrf(); ?>
                        <input type="hidden" name="produto_id" value="<?= (int) $produto['id'] ?>">
                        <button type="submit" class="btn blue">Tenho interesse</button>
                    </form>
                <?php else: ?>
                    <span class="muted">Seu produto</span>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<?php if ($totalPaginas > 1): ?>
    <nav class="pagination" aria-label="Páginas do catálogo">
        <?php if ($pagina > 1): ?><a href="<?= escapar(link_pagina($pagina - 1)) ?>">Anterior</a><?php endif; ?>
        <?php for ($numero = 1; $numero <= $totalPaginas; $numero++): ?>
            <?php if ($numero === $pagina): ?><strong><?= $numero ?></strong>
            <?php else: ?><a href="<?= escapar(link_pagina($numero)) ?>"><?= $numero ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($pagina < $totalPaginas): ?><a href="<?= escapar(link_pagina($pagina + 1)) ?>">Próxima</a><?php endif; ?>
    </nav>
<?php endif; ?>
<?php if (usuario_id() !== null): ?><a class="btn blue" href="cadastro-produto.php">Cadastrar Novo</a><?php endif; ?>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
