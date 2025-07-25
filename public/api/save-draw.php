<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controller\ApiController;
use App\Database\Connection;
use App\Repository\SharedDrawRepository;
use App\Service\SharedDrawService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Initialize database connection
$config = require __DIR__ . '/../../config/database.php';
Connection::setConfig($config);

// Initialize services and controller
$repository = new SharedDrawRepository();
$service = new SharedDrawService($repository);
$controller = new ApiController($service);

// Handle the request
$controller->saveDraw();
