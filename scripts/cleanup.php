<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Connection;
use App\Repository\SharedDrawRepository;
use App\Service\SharedDrawService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialize database connection
$config = require __DIR__ . '/../config/database.php';
Connection::setConfig($config);

// Initialize services
$repository = new SharedDrawRepository();
$service = new SharedDrawService($repository);

try {
    // Count expired records before cleanup
    $expiredCount = $repository->countExpired();

    if ($expiredCount === 0) {
        echo "No expired records found.\n";
        exit(0);
    }

    // Delete expired records
    $deletedCount = $service->cleanupExpired();

    echo "Cleanup completed successfully!\n";
    echo "Deleted {$deletedCount} expired records.\n";
    echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";

    exit(0);
} catch (Exception $e) {
    echo "Error during cleanup: " . $e->getMessage() . "\n";
    echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    exit(1);
}
