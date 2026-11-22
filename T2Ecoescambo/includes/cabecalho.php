<?php
// $titulo é definido pela página que inclui este arquivo.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoEscambo - <?= escapar($titulo) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="browser">
    <header class="browser-title">EcoEscambo - <?= escapar($titulo) ?></header>
    <div class="address">EcoEscambo</div>
    <main class="page-content">
        <nav class="top-nav">
            <a href="catalogo.php">Catálogo</a>
            <?php if (usuario_id() !== null): ?>
                | <a href="meus-produtos.php">Meus Produtos</a>
                | <a href="analise-oferta.php">Análise de Oferta</a>
                | <a href="avaliar-proposta.php">Avaliar Proposta</a>
                | <form action="sair.php" method="post" class="inline-form">
                    <?php campo_csrf(); ?>
                    <button type="submit" class="link-button">Sair</button>
                </form>
            <?php else: ?>
                | <a href="login.php">Entrar no sistema</a>
                | <a href="cadastro.php">Cadastro Utilizador</a>
            <?php endif; ?>
        </nav>
        <?php mostrar_mensagem(); ?>
