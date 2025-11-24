<?php
include_once 'dbconfig.php';

// cria a conexão PDO corretamente
$pdo = getDbConnection();

// pega dados do formulário
$nome  = $_POST['nome'] ?? '';
$preco = $_POST['preco'] ?? '';
$imagem = $_POST['imagem'] ?? '';
$disp = $_POST['disponibilidade'] ?? '';

// comando SQL usando PDO
$sql = "INSERT INTO produtos (nome, preço, imagem, disponibilidade)
        VALUES (?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);

if ($stmt->execute([$nome, $preco, $imagem, $disp])) {
    echo "
        <script>
            alert('Produto adicionado com sucesso!');
            window.location.href = '../catalogo.php';
        </script>
    ";
} else {
    echo "
        <script>
            alert('Erro ao adicionar o produto!');
            window.location.href = '../catalogo.php';
        </script>
    ";
}
?>
