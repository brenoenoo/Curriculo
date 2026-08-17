<?php
require_once 'crud.php';

if (isset($_GET['tabela']) && isset($_GET['id'])) {
    $tabela = $_GET['tabela'];
    $id = (int) $_GET['id'];
    $tabelas_permitidas = ['experiencias', 'formacoes', 'competencias'];
    
    if (in_array($tabela, $tabelas_permitidas)) {
        delete($pdo, $tabela, "id = $id");
    }
}

header("Location: index.php");
exit;
?>