<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();
exigir_post();

$propostaId = (int) ($_POST['proposta_id'] ?? 0);
$acao = (string) ($_POST['acao'] ?? '');
if (!in_array($acao, ['aceitar', 'recusar'], true)) {
    http_response_code(400);
    exit('Ação inválida.');
}

$banco = banco();
$banco->beginTransaction();
try {
    $consulta = $banco->prepare(
        "SELECT prop.id, i.produto_id AS produto_ofertante_id,
                prop.produto_proposto_id AS meu_produto_id,
                ofertado.estado AS estado_ofertante, proprio.estado AS meu_estado,
                u.nome AS nome_ofertante, u.email AS email_ofertante
         FROM propostas prop
         JOIN interesses i ON i.id = prop.interesse_id
         JOIN produtos proprio ON proprio.id = prop.produto_proposto_id
         JOIN produtos ofertado ON ofertado.id = i.produto_id
         JOIN usuarios u ON u.id = ofertado.usuario_id
         WHERE prop.id = ? AND i.interessado_id = ? FOR UPDATE"
    );
    $consulta->execute([$propostaId, usuario_id()]);
    $proposta = $consulta->fetch();

    if (!$proposta || $proposta['estado_ofertante'] !== 'Em aberto'
        || $proposta['meu_estado'] !== 'Em aberto') {
        $banco->rollBack();
        mensagem('Proposta indisponível.');
        redirecionar('avaliar-proposta.php');
    }

    if ($acao === 'recusar') {
        $apagar = $banco->prepare('DELETE FROM propostas WHERE id = ?');
        $apagar->execute([$propostaId]);
        $banco->commit();
        mensagem('Proposta recusada.');
        redirecionar('avaliar-proposta.php');
    }

    $produtoOfertante = (int) $proposta['produto_ofertante_id'];
    $meuProduto = (int) $proposta['meu_produto_id'];

    $atualizar = $banco->prepare("UPDATE produtos SET estado = 'Finalizada' WHERE id IN (?, ?)");
    $atualizar->execute([$produtoOfertante, $meuProduto]);

    // Elimina propostas e interesses que envolvem produtos que já foram trocados.
    $apagar = $banco->prepare('DELETE FROM propostas WHERE produto_proposto_id IN (?, ?)');
    $apagar->execute([$produtoOfertante, $meuProduto]);
    $apagar = $banco->prepare('DELETE FROM interesses WHERE produto_id IN (?, ?)');
    $apagar->execute([$produtoOfertante, $meuProduto]);

    $banco->commit();
    $_SESSION['contato_troca'] = [
        'nome' => $proposta['nome_ofertante'],
        'email' => $proposta['email_ofertante']
    ];
    redirecionar('contato.php');
} catch (Throwable $erro) {
    if ($banco->inTransaction()) {
        $banco->rollBack();
    }
    throw $erro;
}
