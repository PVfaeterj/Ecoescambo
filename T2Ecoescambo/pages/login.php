<?php
require_once __DIR__ . '/../includes/funcoes.php';

if (usuario_id() !== null) {
    redirecionar('catalogo.php');
}

$erro = '';
$email = trim((string) ($_POST['email'] ?? ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $senha = (string) ($_POST['senha'] ?? '');
    $consulta = banco()->prepare('SELECT id, senha FROM usuarios WHERE email = ?');
    $consulta->execute([$email]);
    $usuario = $consulta->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        redirecionar('catalogo.php');
    }

    $erro = 'E-mail ou senha incorretos.';
}

$titulo = 'Login';
require __DIR__ . '/../includes/cabecalho.php';
?>
<section class="auth-card">
    <h1>Autenticação</h1>
    <?php if ($erro): ?><p class="error" role="alert"><?= escapar($erro) ?></p><?php endif; ?>
    <form method="post" action="login.php" novalidate>
        <?php campo_csrf(); ?>
        <label for="loginEmail">E-mail:</label>
        <input id="loginEmail" name="email" type="text" value="<?= escapar($email) ?>">
        <label for="loginPassword">Senha:</label>
        <input id="loginPassword" name="senha" type="password">
        <button class="btn blue" type="submit">Login</button>
    </form>
    <hr>
    <p class="small-text">Ainda não é usuário?</p>
    <a class="btn blue small-btn" href="cadastro.php">Cadastro</a>
</section>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
