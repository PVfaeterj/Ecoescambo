<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();

if (empty($_SESSION['contato_troca'])) {
    redirecionar('avaliar-proposta.php');
}
$contato = $_SESSION['contato_troca'];
unset($_SESSION['contato_troca']);
$titulo = 'Troca Aceita';
require __DIR__ . '/../includes/cabecalho.php';
?>
<h1 class="page-title">Troca aceita</h1>
<p class="notice success">Os dois produtos foram finalizados e retirados do catálogo.</p>
<h2>Contato do utilizador-ofertante</h2>
<p><strong>Nome:</strong> <?= escapar($contato['nome']) ?></p>
<p><strong>E-mail:</strong> <?= escapar($contato['email']) ?></p>
<p><a href="meus-produtos.php">Meus Produtos</a></p>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
