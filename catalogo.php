<?php
// Inclui o arquivo de biblioteca que contém as funções getBaseUrl() e fetchFilmesFromApi()
include_once 'backend/lib.php';

// 1. Obtem a URL base e construir a URL completa para a API
$base_url = getBaseUrl();
$api_url = $base_url . '/backend/api.php?resource=produtos';

// 2. Busca os dados da API
$result = fetchProdutosFromApi($api_url);
$produtos = $result['produtos'];
$error = $result['error'];
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"  href="./bootstrap-5.3.8-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="./CSS/style.css">
        

    <title>Document</title>
    
</head>
<body>

     
    <header> 
        <h1> Albatroz impressoras</h1>     </header>

     <?php include 'nav.php'; ?>
     
     <main>
       <h2><img src="./data/imagens/logo-albatroz.jpg" class="logo-index" alt="" width="100"> <br> 

     <p>
        Nosso catálogo de produtos e serviços
     </p>


     <a href="add-produto.php" class="btn btn-success">Adicionar Produto</a>


        <table border="1" id="tabela-catalogo">

         <thead>

            <tr>

               <th>Descrição visual</th>

               <th>Nome do produto</th>

               <th>Preço</th>

               <th>Disponibilidade</th>

            </tr>

         </thead>

         <tbody id="corpo-tabela-produtos">

            <?php if ($error): ?>
                    <!-- Exibe mensagem de erro -->
                    <tr style="color: red; border: 1px solid red; padding: 10px;">
                        <td colspan="3">Erro</td>
                        <td colspan="3"><p><?php echo htmlspecialchars($error); ?></p></td>
                    </tr>
                <?php elseif (!empty($produtos)): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td>
                                <?php
                                    $url = htmlspecialchars($produto['imagem'] ?? '');
                                    if (!empty($url)):
                                ?>
                                    <!-- CORREÇÃO 3: Exibe a URL como uma imagem <img> -->
                                    <img src="<?php echo $url; ?>" 
                                        alt="<?php echo htmlspecialchars($produto['nome'] ?? 'imagem'); ?>"
                                    />
                                    <!-- Fallback para caso a imagem não carregue -->
                                    <span style="display: none; color: gray;">Imagem não carregada.</span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($produto['nome'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($produto['preco'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($produto['disponibilidade'] ?? ''); ?></td>          
                            <td><?php echo htmlspecialchars($produto['id'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Não foi possível carregar o catálogo de produtos. Verifique a URL da API.</td>
                    </tr>
                <?php endif; ?>

         </tbody>

      </table>


 </main>
    


   <footer>
      
      <p>Siga-nos em nossa rede social!! <br>
      
      <br> <img src="./data/imagens/instagran-icon.jfif" 
      alt="ícone instagran" width = "20" >
        Instagram: <a href="https://www.instagram.com/albatroz.impressoras/#" target="_blank" rel="noopener noreferrer">
            @albatroz.impressora </p>
         </a>

        <p>
    
         Este projeto é uma ferramenta educacional e não comercial. Às informações são reais, porém são meramente para demonstração 
        </p>
   </footer>

  <script src="./Js/catalogo.js"></script>
  <script src="./Js/tema.js"> </script>

</body>

</html>