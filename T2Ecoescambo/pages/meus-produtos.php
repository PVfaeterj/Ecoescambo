<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();

$consulta = banco()->prepare(
    'SELECT p.*, (SELECT COUNT(*) FROM interesses i WHERE i.produto_id = p.id) AS interessados
     FROM produtos p WHERE p.usuario_id = ? ORDER BY p.id DESC'
);
$consulta->execute([usuario_id()]);
$produtos = $consulta->fetchAll();

$titulo = 'Meus Produtos';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title">Meus Produtos</h1>
<section class="product-list">
    <?php if (!$produtos): ?><p>Você ainda não cadastrou produtos.</p><?php endif; ?>
    <?php foreach ($produtos as $produto): ?>
        <article class="product-row">
            <img class="product-image" src="<?= escapar(imagem_produto($produto['categoria'])) ?>" alt="Produto">
            <div class="product-info">
                <strong><?= escapar($produto['titulo']) ?></strong>
                <p>Aceita: <?= escapar($produto['aceita_troca']) ?></p>
                <p>Estado: <?= escapar($produto['estado']) ?></p>
                <?php if ((int) $produto['interessados'] > 0 && $produto['estado'] === 'Em aberto'): ?>
                    <p><?= (int) $produto['interessados'] ?> interessado(s)</p>
                <?php endif; ?>
            </div>
            <div class="actions">
                <?php if ($produto['estado'] === 'Em aberto'): ?>
                    <?php if ((int) $produto['interessados'] > 0): ?>
                        <a class="btn green" href="analise-oferta.php">Ver interesses</a>
                    <?php endif; ?>
                    <form method="post" action="retirar-produto.php">
                        <?php campo_csrf(); ?>
                        <input type="hidden" name="produto_id" value="<?= (int) $produto['id'] ?>">
                        <button type="submit" class="btn red">Retirar</button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<a href="cadastro-produto.php" class="btn blue">Cadastrar Novo</a>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
