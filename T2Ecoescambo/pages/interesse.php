<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();
exigir_post();

$id = (int) ($_POST['produto_id'] ?? 0);
$consulta = banco()->prepare("SELECT usuario_id FROM produtos WHERE id = ? AND estado = 'Em aberto'");
$consulta->execute([$id]);
$produto = $consulta->fetch();

if (!$produto || (int) $produto['usuario_id'] === usuario_id()) {
    mensagem('Não é possível registrar interesse neste produto.');
    redirecionar('catalogo.php');
}

$verificar = banco()->prepare('SELECT id FROM interesses WHERE produto_id = ? AND interessado_id = ?');
$verificar->execute([$id, usuario_id()]);
if ($verificar->fetch()) {
    mensagem('Seu interesse já foi registrado.');
} else {
    $inserir = banco()->prepare('INSERT INTO interesses (produto_id, interessado_id) VALUES (?, ?)');
    $inserir->execute([$id, usuario_id()]);
    mensagem('Seu interesse foi registrado com sucesso. Aguarde o contato do ofertante');
}
redirecionar('catalogo.php');
