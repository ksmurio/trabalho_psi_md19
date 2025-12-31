<?php
// Configurações da Base de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '12345');
define('DB_NAME', 'biblioteca_online');

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
