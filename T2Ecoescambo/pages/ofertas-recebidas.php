<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();

$interesseId = (int) ($_GET['interesse'] ?? 0);
$consulta = banco()->prepare(
    "SELECT i.id, i.interessado_id, p.titulo, p.categoria, p.aceita_troca,
            u.nome AS interessado, u.email
     FROM interesses i
     JOIN produtos p ON p.id = i.produto_id
     JOIN usuarios u ON u.id = i.interessado_id
     WHERE i.id = ? AND p.usuario_id = ? AND p.estado = 'Em aberto'"
);
$consulta->execute([$interesseId, usuario_id()]);
$interesse = $consulta->fetch();
if (!$interesse) {
    http_response_code(404);
    exit('Interesse não encontrado.');
}

$consulta = banco()->prepare(
    "SELECT id, titulo, categoria, aceita_troca FROM produtos
     WHERE usuario_id = ? AND estado = 'Em aberto' ORDER BY id ASC"
);
$consulta->execute([(int) $interesse['interessado_id']]);
$produtos = $consulta->fetchAll();

$titulo = 'Ofertas Recebidas';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title">Ofertas Recebidas</h1>
<section class="offer-card">
    <h2>Meu produto</h2>
    <div class="interest-summary">
        <img class="product-image" src="<?= escapar(imagem_produto($interesse['categoria'])) ?>" alt="Produto">
        <div>
            <strong><?= escapar($interesse['titulo']) ?></strong>
            <p>Aceita: <?= escapar($interesse['aceita_troca']) ?></p>
        </div>
    </div>
    <p><strong>Interessado:</strong> <?= escapar($interesse['interessado']) ?> (<?= escapar($interesse['email']) ?>)</p>
    <h2>Produtos do interessado</h2>
    <?php if (!$produtos): ?><p>Este utilizador não possui produtos disponíveis para troca.</p><?php endif; ?>
    <?php foreach ($produtos as $produto): ?>
        <div class="offer-line">
            <img class="product-image" src="<?= escapar(imagem_produto($produto['categoria'])) ?>" alt="Produto">
            <div>
                <strong><?= escapar($produto['titulo']) ?></strong>
                <p>Aceita: <?= escapar($produto['aceita_troca']) ?></p>
            </div>
            <form class="inline-actions" action="processar-interesse.php" method="post">
                <?php campo_csrf(); ?>
                <input type="hidden" name="acao" value="propor">
                <input type="hidden" name="interesse_id" value="<?= (int) $interesse['id'] ?>">
                <input type="hidden" name="produto_proposto_id" value="<?= (int) $produto['id'] ?>">
                <button type="submit" class="btn green">Propor</button>
            </form>
        </div>
    <?php endforeach; ?>
    <form method="post" action="processar-interesse.php">
        <?php campo_csrf(); ?>
        <input type="hidden" name="acao" value="rejeitar">
        <input type="hidden" name="interesse_id" value="<?= (int) $interesse['id'] ?>">
        <button type="submit" class="btn red">Não tenho interesse por nenhum (rejeitar todas as ofertas)</button>
    </form>
</section>
<p><a href="analise-oferta.php">Voltar para análise de oferta</a></p>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
