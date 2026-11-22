<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();
exigir_post();

$interesseId = (int) ($_POST['interesse_id'] ?? 0);
$acao = (string) ($_POST['acao'] ?? '');
$consulta = banco()->prepare(
    "SELECT i.id, i.interessado_id
     FROM interesses i JOIN produtos p ON p.id = i.produto_id
     WHERE i.id = ? AND p.usuario_id = ? AND p.estado = 'Em aberto'"
);
$consulta->execute([$interesseId, usuario_id()]);
$interesse = $consulta->fetch();

if (!$interesse) {
    http_response_code(404);
    exit('Interesse não encontrado.');
}

if ($acao === 'rejeitar') {
    $apagar = banco()->prepare('DELETE FROM interesses WHERE id = ?');
    $apagar->execute([$interesseId]);
    mensagem('Interesse removido.');
    redirecionar('analise-oferta.php');
}

if ($acao === 'propor') {
    $produtoId = (int) ($_POST['produto_proposto_id'] ?? 0);
    $consulta = banco()->prepare(
        "SELECT id FROM produtos WHERE id = ? AND usuario_id = ? AND estado = 'Em aberto'"
    );
    $consulta->execute([$produtoId, (int) $interesse['interessado_id']]);
    if (!$consulta->fetch()) {
        mensagem('Produto indisponível para troca.');
        redirecionar('analise-oferta.php');
    }

    $consulta = banco()->prepare('SELECT id FROM propostas WHERE interesse_id = ? AND produto_proposto_id = ?');
    $consulta->execute([$interesseId, $produtoId]);
    if ($consulta->fetch()) {
        mensagem('Essa proposta já foi enviada.');
    } else {
        $inserir = banco()->prepare('INSERT INTO propostas (interesse_id, produto_proposto_id) VALUES (?, ?)');
        $inserir->execute([$interesseId, $produtoId]);
        mensagem('Proposta de troca registrada com sucesso.');
    }
    redirecionar('analise-oferta.php');
}

http_response_code(400);
exit('Ação inválida.');
