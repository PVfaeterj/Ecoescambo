<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_login();
exigir_post();
$_SESSION = [];
session_destroy();
redirecionar('login.php');
