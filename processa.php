<?php
require_once 'crud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar_experiencia'){
    $dados_experiencia = [
        'cargo' => $_POST['cargo'],
        'empresa' => $_POST['empresa'],
        'periodo' => $_POST['periodo'],
        'descricao' => $_POST['descricao']
    ];
    
    $novo_id = create($pdo, 'experiencias', $dados_experiencia);
    
    if($novo_id) {
        header("Location: index.php");
        exit;
    }
}
?>