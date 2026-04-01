<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$router = new Router();
$router->get('/', [DashboardController::class, 'index']);

$router->get('/clientes', [ClienteController::class, 'index']);
$router->get('/clientes/novo', [ClienteController::class, 'novo']);
$router->get('/clientes/editar', [ClienteController::class, 'editar']);
$router->post('/clientes/salvar', [ClienteController::class, 'salvar']);
$router->post('/clientes/excluir', [ClienteController::class, 'excluir']);

$router->get('/templates', [TemplateController::class, 'index']);
$router->get('/templates/novo', [TemplateController::class, 'novo']);
$router->get('/templates/editar', [TemplateController::class, 'editar']);
$router->post('/templates/salvar', [TemplateController::class, 'salvar']);
$router->post('/templates/excluir', [TemplateController::class, 'excluir']);

$router->get('/template-bases', [TemplateBaseController::class, 'index']);
$router->get('/template-bases/novo', [TemplateBaseController::class, 'novo']);
$router->get('/template-bases/editar', [TemplateBaseController::class, 'editar']);
$router->post('/template-bases/salvar', [TemplateBaseController::class, 'salvar']);
$router->post('/template-bases/excluir', [TemplateBaseController::class, 'excluir']);

$router->get('/midias', [MediaController::class, 'index']);
$router->post('/midias/upload', [MediaController::class, 'upload']);
$router->post('/midias/excluir', [MediaController::class, 'excluir']);

$router->get('/envio', [EnvioController::class, 'index']);
$router->post('/envio/preview', [EnvioController::class, 'preview']);
$router->post('/envio/enviar', [EnvioController::class, 'enviar']);

$router->get('/historico', [HistoricoController::class, 'index']);
$router->get('/historico/ver', [HistoricoController::class, 'ver']);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$router->dispatch($method, request_path());
