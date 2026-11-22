<?php
require_once __DIR__ . '/../includes/funcoes.php';

$id = (int) ($_GET['id'] ?? 0);
$consulta = banco()->prepare(
    "SELECT p.*, u.nome AS ofertante FROM produtos p
     JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = ? AND p.estado = 'Em aberto'"
);
$consulta->execute([$id]);
$produto = $consulta->fetch();
if (!$produto) {
    http_response_code(404);
    exit('Produto não encontrado.');
}

$titulo = 'Detalhes do Produto';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title"><?= escapar($produto['titulo']) ?></h1>
<section class="product-detail">
    <img src="<?= escapar(imagem_produto($produto['categoria'])) ?>" alt="Produto">
    <div>
        <p><strong>Categoria:</strong> <?= escapar($produto['categoria']) ?></p>
        <?php if ($produto['marca']): ?><p><strong>Marca:</strong> <?= escapar($produto['marca']) ?></p><?php endif; ?>
        <p><strong>Condição:</strong> <?= $produto['condicao'] === 'novo' ? 'Novo' : 'Usado' ?></p>
        <p><strong>Região:</strong> <?= escapar(regiao_texto($produto['regiao'])) ?></p>
        <p><strong>Aceita em troca:</strong> <?= escapar($produto['aceita_troca']) ?></p>
        <p><strong>Ofertante:</strong> <?= escapar($produto['ofertante']) ?></p>
        <?php if (usuario_id() !== (int) $produto['usuario_id']): ?>
            <form method="post" action="interesse.php">
                <?php campo_csrf(); ?>
                <input type="hidden" name="produto_id" value="<?= (int) $produto['id'] ?>">
                <button class="btn blue" type="submit">Tenho interesse</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<p><a href="catalogo.php">Voltar ao catálogo</a></p>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
