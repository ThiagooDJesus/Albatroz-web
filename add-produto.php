<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Produto</title>
    <link rel="stylesheet" href="./bootstrap-5.3.8-dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Adicionar Novo Produto</h2>

<form action="backend/add-produto-action.php" method="POST">

    <label>Nome:</label>
    <input type="text" name="nome" class="form-control" required>

    <label>Preço:</label>
    <input type="text" name="preco" class="form-control" required>

    <label>URL da imagem:</label>
    <input type="text" name="imagem" class="form-control">

    <label>Disponibilidade:</label>
    <input type="text" name="disponibilidade" class="form-control" placeholder="Disponível / Indisponível">

    <br>
    <button type="submit" class="btn btn-primary">Enviar</button>

</form>

</body>
</html>
