<?php
require_once __DIR__ . '/core/AuthMiddleware.php';
require_once __DIR__ . '/api/MahasiswaController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header("Content-Type: application/json");

// GET /
if ($uri === "/") {
    echo json_encode([
        "message" => "Koneksi success",
        "db_host" => getenv("DB_HOST"),
        "db_type" => getenv("DB_TYPE")
    ]);
    exit;
}

// Mahasiswa routes
$auth = new AuthMiddleware();

switch (true) {

    case $uri === '/mahasiswa' && $method === 'GET':
        $auth->verify();
        (new MahasiswaController())->index();
        break;

    case preg_match('#^/mahasiswa/(\d+)$#', $uri, $matches) && $method === 'GET':
        $auth->verify();
        (new MahasiswaController())->show($matches[1]);
        break;

    case $uri === '/mahasiswa' && $method === 'POST':
        $auth->verify();
        (new MahasiswaController())->store();
        break;

    case preg_match('#^/mahasiswa/(\d+)$#', $uri, $matches) && $method === 'PUT':
        $auth->verify();
        (new MahasiswaController())->update($matches[1]);
        break;

    case preg_match('#^/mahasiswa/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        $auth->verify();
        (new MahasiswaController())->destroy($matches[1]);
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Route tidak ditemukan"]);
        break;
}
