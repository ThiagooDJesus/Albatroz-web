<?php
// api.php
// API RESTful Procedural para gerenciamento de filmes com roteamento baseado em Query String.

// 1. Cabeçalhos e Configuração
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Inclui o arquivo de conexão (que usa PDO e PostgreSQL)
include_once 'dbconfig.php';

// Trata o preflight OPTIONS (necessário para CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inicializa a conexão
$pdo = getDbConnection();

// Obtém o método HTTP e os dados de entrada
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true); // Para POST/PUT

// --- Lógica de Roteamento Baseada na Query String (Modelo: ?resource=filmes&id=...) ---

// Obtém o recurso da query string (ex: 'filmes')
$resource = $_GET['resource'] ?? '';

// Obtém o ID da query string (ex: '2')
$id = $_GET['id'] ?? null;

// Sanitiza o ID, caso exista
$id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
if (empty($id)) $id = null; // Garante que $id seja null se vazio ou inválido

// Funções CRUD para o recurso 'filmes'

/**
 * Função CREATE (POST /api.php?resource=filmes)
 */
function createProduto($pdo, $data) {
    if (empty($data['nome']) || empty($data['preco'])) {
        http_response_code(400);
        return array("message" => "Dados incompletos: nome e preco são obrigatórios.");
    }

    // MySQL NÃO aceita RETURNING, então removemos
    $sql = "INSERT INTO produtos (nome, preco, imagem, disponibilidade)
            VALUES (?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nome'],
            $data['preco'],
            $data['imagem'] ?? null,
            $data['disponibilidade'] ?? 'Disponível'
        ]);

        // MySQL usa lastInsertId()
        $new_id = $pdo->lastInsertId();

        http_response_code(201);
        return array(
            "message" => "Produto criado com sucesso.",
            "id" => $new_id
        );

    } catch (PDOException $e) {
        http_response_code(503);
        return array("message" => "Erro ao criar produto: " . $e->getMessage());
    }
}

/**
 * Função READ (GET /api.php?resource=filmes ou GET /api.php?resource=filmes&id=2)
 */
function readProdutos($pdo, $id) {
    if ($id) {
        // READ ONE
        $sql = "SELECT id, nome, preco, imagem, disponibilidade FROM produtos WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            http_response_code(200);
            return $produto;
        } else {
            http_response_code(404);
            return array("message" => "Produto não encontrado.");
        }
    } else {
        // READ ALL
$sql = "SELECT id, nome, preco, imagem, disponibilidade FROM produtos ORDER BY id DESC";
        $stmt = $pdo->query($sql);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($produtos) {
            http_response_code(200);
            return $produtos;
        } else {
            http_response_code(200); // Retorna 200 e um array vazio se não houver filmes
            return [];
        }
    }
}

/**
 * Função UPDATE (PUT /api.php?resource=filmes&id=2)
 */
function updateProduto($pdo, $id, $data) {
    if (!$id || empty($data['nome']) || empty($data['preco'])) {
        http_response_code(400);
        return array("message" => "Dados incompletos ou ID ausente.");
    }

    $sql = "UPDATE produtos SET nome = ?, preco = ?, imagem = ? WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nome'],
            $data['preco'],
            $data['imagem'] ?? null,
            $id
        ]);
        
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            return array("message" => "Produto atualizado com sucesso.");
        } else {
            http_response_code(404);
            return array("message" => "Produto não encontrado ou nenhum dado para atualizar.");
        }
    } catch (PDOException $e) {
        http_response_code(503);
        return array("message" => "Erro ao atualizar produto: " . $e->getMessage());
    }
}

/**
 * Função DELETE (DELETE /api.php?resource=filmes&id=2)
 */
function deleteProduto($pdo, $id) {
    if (!$id) {
        http_response_code(400);
        return array("message" => "ID do produto é obrigatório para exclusão.");
    }

    $sql = "DELETE FROM produtos WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            return array("message" => "Produto excluído com sucesso.");
        } else {
            http_response_code(404);
            return array("message" => "Produto não encontrado.");
        }
    } catch (PDOException $e) {
        http_response_code(503);
        return array("message" => "Erro ao excluir produto: " . $e->getMessage());
    }
}

// --- Roteamento Principal Modificado ---

$response = array();

// 1. Verifica se o recurso é 'filmes'
if ($resource !== 'produtos') {
    http_response_code(404);
    $response = array("message" => "Recurso não encontrado ou ausente. Use ?resource=produtos");
} else {
    // 2. Roteia com base no método HTTP e na presença do ID
    switch ($method) {
        case 'GET':
            // Rota: GET api.php?resource=filmes ou GET api.php?resource=filmes&id=2
            $response = readProdutos($pdo, $id);
            break;
            
        case 'POST':
            // Rota: POST api.php?resource=filmes (ID não deve estar na query string para criação)
            if ($id) {
                http_response_code(405); // Método POST não deve ter ID no caminho/query
                $response = array("message" => "Método não permitido para esta rota. Use POST api.php?resource=produtos.");
            } else {
                $response = createProduto($pdo, $data);
            }
            break;

        case 'PUT':
            // Rota: PUT api.php?resource=filmes&id=2 (ID é obrigatório na query string)
            if ($id) {
                $response = updateProduto($pdo, $id, $data);
            } else {
                http_response_code(400);
                $response = array("message" => "ID do produto é obrigatório na query string para o método PUT (ex: ?resource=produtos&id=123).");
            }
            break;
            
        case 'DELETE':
            // Rota: DELETE api.php?resource=filmes&id=2 (ID é obrigatório na query string)
            if ($id) {
                $response = deleteProduto($pdo, $id);
            } else {
                http_response_code(400);
                $response = array("message" => "ID do produto é obrigatório na query string para o método DELETE (ex: ?resource=produto&id=123).");
            }
            break;

        default:
            http_response_code(405); // Method Not Allowed
            $response = array("message" => "Método não permitido para este recurso.");
            break;
    }
}

// 3. Retorna a Resposta como JSON
echo json_encode($response);
?>