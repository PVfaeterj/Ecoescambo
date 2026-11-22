<?php
require_once __DIR__ . '/../includes/funcoes.php';

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $senha = (string) ($_POST['senha'] ?? '');
    $confirmacao = (string) ($_POST['confirmacao'] ?? '');

    if ($nome === '') {
        $erros['nome'] = 'Informe seu nome.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Informe um e-mail válido.';
    }
    if (strlen($senha) < 6 || !preg_match('/[a-z]/', $senha)
        || !preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $erros['senha'] = 'A senha deve ter ao menos 6 caracteres, com letras minúsculas, maiúsculas e números.';
    }
    if ($confirmacao === '' || $senha !== $confirmacao) {
        $erros['confirmacao'] = 'As senhas devem ser iguais.';
    }

    if (!$erros) {
        $consulta = banco()->prepare('SELECT id FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);
        if ($consulta->fetch()) {
            $erros['email'] = 'Este e-mail já está cadastrado.';
        } else {
            $inserir = banco()->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
            $inserir->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
            mensagem('Utilizador registrado com sucesso. Ative sua conta com o link enviado para seu e-mail.');
            redirecionar('login.php');
        }
    }
}

$titulo = 'Cadastro Utilizador';
require __DIR__ . '/../includes/cabecalho.php';
?>
<section class="form-box">
    <h1 class="page-title">Cadastro Utilizador</h1>
    <form action="cadastro.php" method="post" novalidate>
        <?php campo_csrf(); ?>
        <label for="nome">Nome:</label>
        <input id="nome" type="text" name="nome" value="<?= escapar($nome) ?>" class="<?= isset($erros['nome']) ? 'invalid' : '' ?>">
        <?php if (isset($erros['nome'])): ?><p class="field-error"><?= escapar($erros['nome']) ?></p><?php endif; ?>

        <label for="email">E-mail:</label>
        <input id="email" type="text" name="email" value="<?= escapar($email) ?>" placeholder="fulano@email.com" class="<?= isset($erros['email']) ? 'invalid' : '' ?>">
        <?php if (isset($erros['email'])): ?><p class="field-error"><?= escapar($erros['email']) ?></p><?php endif; ?>

        <label for="senha">Senha:</label>
        <input id="senha" type="password" name="senha" class="<?= isset($erros['senha']) ? 'invalid' : '' ?>">
        <?php if (isset($erros['senha'])): ?><p class="field-error"><?= escapar($erros['senha']) ?></p><?php endif; ?>

        <label for="confirmacao">Repita a senha:</label>
        <input id="confirmacao" type="password" name="confirmacao" class="<?= isset($erros['confirmacao']) ? 'invalid' : '' ?>">
        <?php if (isset($erros['confirmacao'])): ?><p class="field-error"><?= escapar($erros['confirmacao']) ?></p><?php endif; ?>

        <p class="small-text">A senha deve ter pelo menos 6 caracteres, letras maiúsculas, minúsculas e números.</p>
        <button class="btn blue" type="submit">Cadastrar</button>
    </form>
    <p><a href="login.php">Entrar no sistema</a></p>
</section>
<?php require __DIR__ . '/../includes/rodape.php'; ?>
