<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = request_path();

/* API pública */
if (preg_match('#^/api/send/(\d+)$#', $path, $m)) {
    if ($method === 'OPTIONS') {
        (new ApiSendController())->send((int) $m[1]);
        exit;
    }
    if ($method === 'POST') {
        (new ApiSendController())->send((int) $m[1]);
        exit;
    }
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$router = new Router();
$router->get('/', [DashboardController::class, 'index']);

$router->get('/applications', [ApplicationController::class, 'index']);
$router->get('/applications/novo', [ApplicationController::class, 'novo']);
$router->get('/applications/editar', [ApplicationController::class, 'editar']);
$router->post('/applications/salvar', [ApplicationController::class, 'salvar']);
$router->post('/applications/excluir', [ApplicationController::class, 'excluir']);
$router->post('/applications/regenerar-chave', [ApplicationController::class, 'regenerarChave']);

$router->get('/templates', [TemplateController::class, 'index']);
$router->get('/templates/novo', [TemplateController::class, 'novo']);
$router->get('/templates/editar', [TemplateController::class, 'editar']);
$router->post('/templates/salvar', [TemplateController::class, 'salvar']);
$router->post('/templates/excluir', [TemplateController::class, 'excluir']);

$router->get('/media', [MediaController::class, 'index']);
$router->post('/media/upload', [MediaController::class, 'upload']);
$router->post('/media/excluir', [MediaController::class, 'excluir']);

$router->get('/envio', [EnvioController::class, 'index']);
$router->post('/envio/preview', [EnvioController::class, 'preview']);
$router->post('/envio/enviar', [EnvioController::class, 'enviar']);

$router->get('/logs', [LogsController::class, 'index']);
$router->get('/logs/ver', [LogsController::class, 'ver']);

$router->dispatch($method, $path);
