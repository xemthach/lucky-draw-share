<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SharedDrawService;

class ApiController
{
    public function __construct(
        private SharedDrawService $sharedDrawService
    ) {}

    public function saveDraw(): void
    {
        // Set JSON response headers
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            return;
        }

        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            // Get JSON input
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON input');
            }

            // Validate input
            if (!isset($data['winners']) || !is_array($data['winners'])) {
                throw new \InvalidArgumentException('Winners array is required');
            }

            if (empty($data['winners'])) {
                throw new \InvalidArgumentException('Winners array cannot be empty');
            }

            // Create shared draw
            $sharedDraw = $this->sharedDrawService->createSharedDraw($data['winners']);

            // Return success response
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'share_id' => $sharedDraw->shareId,
                'share_url' => $this->getShareUrl($sharedDraw->shareId),
                'created_at' => $sharedDraw->createdAt->format('Y-m-d H:i:s'),
                'winners_count' => count($sharedDraw->winners)
            ]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    private function getShareUrl(string $shareId): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}/public/share.php?share_id={$shareId}";
    }
}
