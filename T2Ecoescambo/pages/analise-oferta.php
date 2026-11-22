<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();

$consulta = banco()->prepare(
    "SELECT i.id, p.titulo AS produto, p.categoria, u.nome AS interessado, u.email,
            (SELECT COUNT(*) FROM produtos outros
             WHERE outros.usuario_id = i.interessado_id AND outros.estado = 'Em aberto') AS quantidade
     FROM interesses i
     JOIN produtos p ON p.id = i.produto_id
     JOIN usuarios u ON u.id = i.interessado_id
     WHERE p.usuario_id = ? AND p.estado = 'Em aberto'
     ORDER BY i.id DESC"
);
$consulta->execute([usuario_id()]);
$interesses = $consulta->fetchAll();

$titulo = 'Análise de Oferta';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title">Análise de Oferta</h1>
<?php if (!$interesses): ?><p>Nenhum interesse recebido.</p><?php endif; ?>
<?php foreach ($interesses as $interesse): ?>
    <section class="offer-card">
        <div class="interest-summary">
            <img class="product-image" src="<?= escapar(imagem_produto($interesse['categoria'])) ?>" alt="Produto">
            <div>
                <strong>Meu produto: <?= escapar($interesse['produto']) ?></strong>
                <p>Interessado: <?= escapar($interesse['interessado']) ?> (<?= escapar($interesse['email']) ?>)</p>
                <p>Produtos disponíveis do interessado: <?= (int) $interesse['quantidade'] ?></p>
            </div>
        </div>
        <div class="actions">
            <a class="btn green" href="ofertas-recebidas.php?interesse=<?= (int) $interesse['id'] ?>">Ver interesse</a>
            <form method="post" action="processar-interesse.php">
                <?php campo_csrf(); ?>
                <input type="hidden" name="interesse_id" value="<?= (int) $interesse['id'] ?>">
                <input type="hidden" name="acao" value="rejeitar">
                <button class="btn red" type="submit">Rejeitar todas as ofertas</button>
            </form>
        </div>
    </section>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
