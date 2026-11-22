<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/banco.php';

function escapar(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function usuario_id(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
}

function exigir_login(): void
{
    if (usuario_id() === null) {
        header('Location: login.php');
        exit;
    }
}

function redirecionar(string $pagina): void
{
    header('Location: ' . $pagina);
    exit;
}

function mensagem(string $texto): void
{
    $_SESSION['mensagem'] = $texto;
}

function mostrar_mensagem(): void
{
    if (isset($_SESSION['mensagem'])) {
        echo '<p class="notice success" role="status">' . escapar($_SESSION['mensagem']) . '</p>';
        unset($_SESSION['mensagem']);
    }
}

function token_csrf(): string
{
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function campo_csrf(): void
{
    echo '<input type="hidden" name="csrf" value="' . escapar(token_csrf()) . '">';
}

function verificar_csrf(): void
{
    if (!hash_equals(token_csrf(), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(403);
        exit('Solicitação inválida.');
    }
}

function exigir_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método não permitido.');
    }

    verificar_csrf();
}

function regiao_texto(string $regiao): string
{
    return [
        'centro' => 'Centro',
        'zonasul' => 'Zona Sul',
        'zonanorte' => 'Zona Norte',
        'zonaoeste' => 'Zona Oeste'
    ][$regiao] ?? $regiao;
}

function imagem_produto(string $categoria): string
{
    $categoria = strtolower($categoria);
    if (str_contains($categoria, 'livro')) {
        return '../assets/img/kitlivros.png';
    }
    if (str_contains($categoria, 'jogo')) {
        return '../assets/img/jogotabuleiro.png';
    }
    if (str_contains($categoria, 'esporte')) {
        return '../assets/img/garrafa.png';
    }
    if (str_contains($categoria, 'mochila')) {
        return '../assets/img/mochilaescolar.png';
    }
    if (str_contains($categoria, 'escritório')) {
        return '../assets/img/cadeira.png';
    }
    if (str_contains($categoria, 'instrumentos')) {
        return '../assets/img/violao.png';
    }
    if (str_contains($categoria, 'eletrônicos')) {
        return '../assets/img/relogio.png';
    }
    if (str_contains($categoria, 'roupa')) {
        return '../assets/img/jaquetajeans.png';
    }
    if (str_contains($categoria, 'decor') || str_contains($categoria, 'móvel') || str_contains($categoria, 'planta')) {
        return '../assets/img/vaso.png';
    }
    return '../assets/img/placeholder.svg';
}
