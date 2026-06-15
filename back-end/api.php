<?php
// ============================================================
//  INVEN — API completa em um único arquivo
//  Coloque em: C:\xampp\htdocs\inven\api.php
//  Acesso:     http://localhost/inven/api.php
// ============================================================

ini_set('display_errors', 0);
error_reporting(0);

// ---- BANCO DE DADOS — ajuste aqui ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'inven_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // senha do MySQL (vazio no XAMPP padrão)
define('JWT_KEY', 'inven2024'); // chave do token

// ---- CORS — libera o frontend ----
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// ================================================================
//  FUNÇÕES AUXILIARES
// ================================================================

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            resposta(['erro' => 'Banco de dados indisponível: ' . $e->getMessage()], 500);
        }
    }
    return $pdo;
}

function resposta(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function body(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

// ---- JWT simples ----
function jwt_criar(int $id, string $email): string {
    $header  = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode(['id' => $id, 'email' => $email, 'exp' => time() + 604800]));
    $sig     = base64_encode(hash_hmac('sha256', "$header.$payload", JWT_KEY, true));
    return "$header.$payload.$sig";
}

function jwt_verificar(): array {
    // O XAMPP às vezes não repassa o header Authorization ao PHP.
    // Tentamos todas as formas possíveis de receber o token:
    $header = $_SERVER['HTTP_AUTHORIZATION']
           ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
           ?? getallheaders()['Authorization']
           ?? getallheaders()['authorization']
           ?? '';

    // Fallback: token enviado como query param ?token=...
    if (empty($header) && !empty($_GET['token'])) {
        $header = 'Bearer ' . $_GET['token'];
    }

    if (!str_starts_with($header, 'Bearer ')) resposta(['erro' => 'Token não enviado.'], 401);
    $token = substr($header, 7);
    $partes = explode('.', $token);
    if (count($partes) !== 3) resposta(['erro' => 'Token inválido.'], 401);
    $sig_esperada = base64_encode(hash_hmac('sha256', "$partes[0].$partes[1]", JWT_KEY, true));
    if (!hash_equals($sig_esperada, $partes[2])) resposta(['erro' => 'Token inválido.'], 401);
    $payload = json_decode(base64_decode($partes[1]), true);
    if ($payload['exp'] < time()) resposta(['erro' => 'Token expirado.'], 401);
    return $payload;
}

// ================================================================
//  ROTEADOR — pega a rota da query string (?rota=...)
// ================================================================

$method = $_SERVER['REQUEST_METHOD'];

// Detecta a rota de duas formas:
// 1. Via query string: api.php?rota=usuarios/login
// 2. Via REQUEST_URI direto
$rota = $_GET['rota'] ?? '';
if ($rota === '') {
    $uri  = strtok($_SERVER['REQUEST_URI'], '?');
    $base = dirname($_SERVER['SCRIPT_NAME']);
    $rota = ltrim(str_replace($base, '', $uri), '/');
    $rota = ltrim(str_replace('api.php', '', $rota), '/');
}
$rota = trim($rota, '/');

// ================================================================
//  ROTAS
// ================================================================

// ---- Health check ----
if ($rota === '' || $rota === 'status') {
    resposta(['app' => 'Inven API', 'status' => 'online']);
}

// ================================================================
//  USUÁRIOS
// ================================================================

// POST api.php?rota=cadastro
if ($method === 'POST' && $rota === 'cadastro') {
    $b = body();
    $nome  = trim($b['nome']  ?? '');
    $email = trim($b['email'] ?? '');
    $senha =      $b['senha'] ?? '';

    if (!$nome || !$email || !$senha)
        resposta(['erro' => 'Preencha nome, email e senha.'], 422);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        resposta(['erro' => 'E-mail inválido.'], 422);
    if (strlen($senha) < 6)
        resposta(['erro' => 'Senha precisa ter mínimo 6 caracteres.'], 422);

    $st = db()->prepare('SELECT id FROM usuarios WHERE email = ?');
    $st->execute([$email]);
    if ($st->fetch()) resposta(['erro' => 'E-mail já cadastrado.'], 409);

    $hash = password_hash($senha, PASSWORD_BCRYPT);
    $st = db()->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?,?,?)');
    $st->execute([$nome, $email, $hash]);
    $id = (int) db()->lastInsertId();

    resposta([
        'mensagem' => 'Usuário cadastrado com sucesso',
        'id'    => $id,
        'nome'  => $nome,
        'email' => $email,
        'token' => jwt_criar($id, $email),
    ], 201);
}

// POST api.php?rota=login
if ($method === 'POST' && $rota === 'login') {
    $b     = body();
    $email = trim($b['email'] ?? '');
    $senha =      $b['senha'] ?? '';

    if (!$email || !$senha) resposta(['erro' => 'Informe email e senha.'], 422);

    $st = db()->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
    $st->execute([$email]);
    $user = $st->fetch();

    if (!$user || !password_verify($senha, $user['senha']))
        resposta(['erro' => 'Email ou senha incorretos.'], 401);

    resposta([
        'id'    => $user['id'],
        'nome'  => $user['nome'],
        'email' => $user['email'],
        'token' => jwt_criar($user['id'], $user['email']),
    ]);
}

// ================================================================
//  MATERIAIS  (todas protegidas — precisam de token)
// ================================================================

// GET api.php?rota=materiais
if ($method === 'GET' && $rota === 'materiais') {
    $u = jwt_verificar();

    $sql    = 'SELECT * FROM materiais WHERE usuario_id = ? ORDER BY criado_em DESC';
    $params = [$u['id']];

    // Filtros opcionais
    if (!empty($_GET['busca'])) {
        $sql    = 'SELECT * FROM materiais WHERE usuario_id = ? AND (descricao LIKE ? OR fonte LIKE ?) ORDER BY criado_em DESC';
        $params = [$u['id'], '%'.$_GET['busca'].'%', '%'.$_GET['busca'].'%'];
    }

    $st = db()->prepare($sql);
    $st->execute($params);
    $lista = $st->fetchAll();

    // Adiciona palavras-chave em cada material
    foreach ($lista as &$m) {
        $st2 = db()->prepare('SELECT palavra FROM palavras_chave WHERE material_id = ?');
        $st2->execute([$m['id']]);
        $m['palavras_chave'] = $st2->fetchAll(PDO::FETCH_COLUMN);
    }

    resposta(['dados' => $lista]);
}

// POST api.php?rota=materiais
if ($method === 'POST' && $rota === 'materiais') {
    $u = jwt_verificar();
    $b = body();

    $descricao = trim($b['descricao'] ?? '');
    $preco     = $b['preco']     ?? null;
    $fonte     = trim($b['fonte']     ?? '');
    $telefone  = trim($b['telefone']  ?? '');
    $email     = trim($b['email']     ?? '');
    $palavras  = $b['palavras_chave'] ?? [];

    if (!$descricao || $preco === null || !$fonte)
        resposta(['erro' => 'Campos obrigatórios: descricao, preco, fonte.'], 422);

    $st = db()->prepare(
        'INSERT INTO materiais (usuario_id, descricao, preco, fonte, telefone, email, estoque)
         VALUES (?, ?, ?, ?, ?, ?, 0)'
    );
    $st->execute([$u['id'], $descricao, (float)$preco, $fonte, $telefone, $email]);
    $id = (int) db()->lastInsertId();

    // Salva palavras-chave
    if ($palavras) {
        $st2 = db()->prepare('INSERT INTO palavras_chave (material_id, palavra) VALUES (?, ?)');
        foreach ($palavras as $p) {
            if (trim($p)) $st2->execute([$id, trim($p)]);
        }
    }

    $st = db()->prepare('SELECT * FROM materiais WHERE id = ?');
    $st->execute([$id]);
    resposta(['mensagem' => 'Material cadastrado com sucesso', 'dados' => $st->fetch()], 201);
}

// PUT api.php?rota=materiais/5
if ($method === 'PUT' && preg_match('#^materiais/(\d+)$#', $rota, $m)) {
    $u  = jwt_verificar();
    $id = (int) $m[1];
    $b  = body();

    // Verifica se o material pertence ao usuário
    $st = db()->prepare('SELECT id FROM materiais WHERE id = ? AND usuario_id = ?');
    $st->execute([$id, $u['id']]);
    if (!$st->fetch()) resposta(['erro' => 'Material não encontrado.'], 404);

    $campos  = [];
    $valores = [];

    if (isset($b['descricao'])) { $campos[] = 'descricao = ?'; $valores[] = $b['descricao']; }
    if (isset($b['preco']))     { $campos[] = 'preco = ?';     $valores[] = (float)$b['preco']; }
    if (isset($b['fonte']))     { $campos[] = 'fonte = ?';     $valores[] = $b['fonte']; }
    if (isset($b['telefone']))  { $campos[] = 'telefone = ?';  $valores[] = $b['telefone']; }
    if (isset($b['email']))     { $campos[] = 'email = ?';     $valores[] = $b['email']; }

    if ($campos) {
        $valores[] = $id;
        db()->prepare('UPDATE materiais SET ' . implode(', ', $campos) . ' WHERE id = ?')
             ->execute($valores);
    }

    // Atualiza palavras-chave
    if (isset($b['palavras_chave'])) {
        db()->prepare('DELETE FROM palavras_chave WHERE material_id = ?')->execute([$id]);
        $st2 = db()->prepare('INSERT INTO palavras_chave (material_id, palavra) VALUES (?, ?)');
        foreach ($b['palavras_chave'] as $p) {
            if (trim($p)) $st2->execute([$id, trim($p)]);
        }
    }

    $st = db()->prepare('SELECT * FROM materiais WHERE id = ?');
    $st->execute([$id]);
    resposta(['mensagem' => 'Material atualizado com sucesso', 'dados' => $st->fetch()]);
}

// DELETE api.php?rota=materiais/5
if ($method === 'DELETE' && preg_match('#^materiais/(\d+)$#', $rota, $m)) {
    $u  = jwt_verificar();
    $id = (int) $m[1];

    $st = db()->prepare('DELETE FROM materiais WHERE id = ? AND usuario_id = ?');
    $st->execute([$id, $u['id']]);

    if ($st->rowCount() === 0) resposta(['erro' => 'Material não encontrado.'], 404);
    resposta(['mensagem' => 'Material excluído com sucesso.']);
}

// ================================================================
//  ESTOQUE — entrada / saída
// ================================================================

// POST api.php?rota=estoque/5/movimentar
if ($method === 'POST' && preg_match('#^estoque/(\d+)/movimentar$#', $rota, $m)) {
    $u  = jwt_verificar();
    $id = (int) $m[1];
    $b  = body();

    $tipo       = $b['tipo']       ?? '';
    $quantidade = (float)($b['quantidade'] ?? 0);
    $obs        = $b['observacao'] ?? '';

    if (!in_array($tipo, ['entrada', 'saida', 'ajuste']))
        resposta(['erro' => "Tipo inválido. Use: entrada, saida ou ajuste."], 422);
    if ($quantidade <= 0)
        resposta(['erro' => 'Quantidade deve ser maior que zero.'], 422);

    $st = db()->prepare('SELECT * FROM materiais WHERE id = ? AND usuario_id = ?');
    $st->execute([$id, $u['id']]);
    $mat = $st->fetch();
    if (!$mat) resposta(['erro' => 'Material não encontrado.'], 404);

    if ($tipo === 'saida' && $quantidade > $mat['estoque'])
        resposta(['erro' => 'Quantidade maior que o estoque atual (' . $mat['estoque'] . ').'], 422);

    if ($tipo === 'entrada')      $novo = $mat['estoque'] + $quantidade;
    elseif ($tipo === 'saida')    $novo = $mat['estoque'] - $quantidade;
    else                          $novo = $quantidade; // ajuste manual

    db()->prepare('UPDATE materiais SET estoque = ? WHERE id = ?')->execute([$novo, $id]);
    db()->prepare('INSERT INTO movimentacoes (material_id, usuario_id, tipo, quantidade, observacao) VALUES (?,?,?,?,?)')
        ->execute([$id, $u['id'], $tipo, $quantidade, $obs]);

    resposta(['mensagem' => 'Movimentação registrada.', 'novo_estoque' => $novo]);
}

// GET api.php?rota=movimentacoes
if ($method === 'GET' && $rota === 'movimentacoes') {
    $u = jwt_verificar();
    $st = db()->prepare(
        'SELECT mv.*, mt.descricao AS material
         FROM movimentacoes mv
         JOIN materiais mt ON mt.id = mv.material_id
         WHERE mv.usuario_id = ?
         ORDER BY mv.criado_em DESC
         LIMIT 100'
    );
    $st->execute([$u['id']]);
    resposta(['dados' => $st->fetchAll()]);
}

// ================================================================
//  Rota não encontrada
// ================================================================
resposta(['erro' => "Rota $method /$rota não encontrada.", 'dica' => 'Verifique a URL.'], 404);
