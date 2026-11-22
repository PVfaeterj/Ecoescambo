<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();

$consulta = banco()->prepare(
    "SELECT prop.id, proprio.titulo AS meu_produto, proprio.categoria AS minha_categoria,
            ofertado.titulo AS produto_ofertante, ofertado.categoria AS categoria_ofertante,
            u.nome AS ofertante
     FROM propostas prop
     JOIN interesses i ON i.id = prop.interesse_id
     JOIN produtos proprio ON proprio.id = prop.produto_proposto_id
     JOIN produtos ofertado ON ofertado.id = i.produto_id
     JOIN usuarios u ON u.id = ofertado.usuario_id
     WHERE i.interessado_id = ? AND proprio.estado = 'Em aberto'
       AND ofertado.estado = 'Em aberto'
     ORDER BY prop.id DESC"
);
$consulta->execute([usuario_id()]);
$propostas = $consulta->fetchAll();

$titulo = 'Avaliar Proposta';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title">Avaliar Proposta</h1>
<?php if (!$propostas): ?><p>Nenhuma proposta de troca recebida.</p><?php endif; ?>
<?php foreach ($propostas as $proposta): ?>
    <section class="offer-card">
        <p><strong>Proposta enviada por:</strong> <?= escapar($proposta['ofertante']) ?></p>
        <div class="interest-summary">
            <img class="product-image" src="<?= escapar(imagem_produto($proposta['minha_categoria'])) ?>" alt="Meu produto">
            <div><strong>Seu produto:</strong> <?= escapar($proposta['meu_produto']) ?></div>
        </div>
        <div class="interest-summary">
            <img class="product-image" src="<?= escapar(imagem_produto($proposta['categoria_ofertante'])) ?>" alt="Produto do ofertante">
            <div><strong>Produto do ofertante:</strong> <?= escapar($proposta['produto_ofertante']) ?></div>
        </div>
        <div class="actions">
            <form method="post" action="processar-proposta.php">
                <?php campo_csrf(); ?>
                <input type="hidden" name="proposta_id" value="<?= (int) $proposta['id'] ?>">
                <input type="hidden" name="acao" value="aceitar">
                <button class="btn green" type="submit">Aceitar</button>
            </form>
            <form method="post" action="processar-proposta.php">
                <?php campo_csrf(); ?>
                <input type="hidden" name="proposta_id" value="<?= (int) $proposta['id'] ?>">
                <input type="hidden" name="acao" value="recusar">
                <button class="btn red" type="submit">Recusar</button>
            </form>
        </div>
    </section>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
