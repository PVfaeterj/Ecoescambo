<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();
exigir_post();

$produtoId = (int) ($_POST['produto_id'] ?? 0);
$banco = banco();
$banco->beginTransaction();
try {
    $atualizar = $banco->prepare("UPDATE produtos SET estado = 'Finalizada'
                                  WHERE id = ? AND usuario_id = ? AND estado = 'Em aberto'");
    $atualizar->execute([$produtoId, usuario_id()]);
    if ($atualizar->rowCount() > 0) {
        $apagar = $banco->prepare('DELETE FROM propostas WHERE produto_proposto_id = ?');
        $apagar->execute([$produtoId]);
        $apagar = $banco->prepare('DELETE FROM interesses WHERE produto_id = ?');
        $apagar->execute([$produtoId]);
        mensagem('Produto finalizado.');
    } else {
        mensagem('Produto não encontrado.');
    }
    $banco->commit();
} catch (Throwable $erro) {
    $banco->rollBack();
    throw $erro;
}
redirecionar('meus-produtos.php');
