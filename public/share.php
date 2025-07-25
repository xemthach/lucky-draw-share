<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ShareController;
use App\Database\Connection;
use App\Repository\SharedDrawRepository;
use App\Service\SharedDrawService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialize database connection
$config = require __DIR__ . '/../config/database.php';
Connection::setConfig($config);

// Initialize services and controller
$repository = new SharedDrawRepository();
$service = new SharedDrawService($repository);
$controller = new ShareController($service);

// Extract share ID from URL or query parameter
$shareId = $_GET['share_id'] ?? '';

if (empty($shareId)) {
    // Try to extract from URL path
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $pathParts = explode('/', trim($requestUri, '/'));

    if (count($pathParts) >= 2 && $pathParts[0] === 'share') {
        $shareId = $pathParts[1];
    }
}

if (empty($shareId)) {
    http_response_code(404);
    echo 'Share ID not found';
    exit;
}

// Validate share ID format (8 characters, alphanumeric)
if (!preg_match('/^[a-z0-9]{8}$/', $shareId)) {
    http_response_code(400);
    echo 'Invalid share ID format';
    exit;
}

// Handle the request
$controller->showShare($shareId);
