<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SharedDrawService;

class ShareController
{
    public function __construct(
        private SharedDrawService $sharedDrawService
    ) {}

    public function showShare(string $shareId): void
    {
        try {
            $sharedDraw = $this->sharedDrawService->getSharedDraw($shareId);

            if (!$sharedDraw) {
                $this->renderError('Draw not found', 'The requested draw could not be found.');
                return;
            }

            if ($sharedDraw->isExpired()) {
                $this->renderError('Link expired', 'This link has expired (older than 30 days).');
                return;
            }

            $this->renderSharePage($sharedDraw);
        } catch (\Exception $e) {
            $this->renderError('Server error', 'An error occurred while loading the draw.');
        }
    }

    private function renderSharePage($sharedDraw): void
    {
        $winners = $sharedDraw->winners;
        $createdAt = $sharedDraw->createdAt;
        $ageInDays = $sharedDraw->getAgeInDays();

        // Simple translations
        $translations = [
            'en' => [
                'luckyDrawResults' => 'Lucky Draw Results',
                'congratulations' => 'Congratulations to all the winners!',
                'totalWinners' => 'Total Winners',
                'daysAgo' => 'Days Ago',
                'winnersList' => 'Winners List',
                'drawDate' => 'Draw Date',
                'shareId' => 'Share ID',
                'daysRemaining' => 'Days Remaining',
                'createYourOwnDraw' => 'Create Your Own Draw'
            ],
            'vi' => [
                'luckyDrawResults' => 'Kết quả quay số may mắn',
                'congratulations' => 'Chúc mừng tất cả người thắng!',
                'totalWinners' => 'Tổng số người thắng',
                'daysAgo' => 'Ngày trước',
                'winnersList' => 'Danh sách người thắng',
                'drawDate' => 'Ngày quay số',
                'shareId' => 'Mã chia sẻ',
                'daysRemaining' => 'Ngày còn lại',
                'createYourOwnDraw' => 'Tạo quay số của riêng bạn'
            ]
        ];

        // Detect language from Accept-Language header or default to English
        $lang = 'en';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if (strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'vi') !== false) {
                $lang = 'vi';
            }
        }

        $t = $translations[$lang];

?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>🎯 <?= $t['luckyDrawResults'] ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }

                .container {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                    padding: 40px;
                    max-width: 600px;
                    width: 100%;
                    text-align: center;
                }

                .header {
                    margin-bottom: 30px;
                }

                .header h1 {
                    font-size: 2.5rem;
                    color: #4f46e5;
                    margin-bottom: 10px;
                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
                }

                .header p {
                    color: #64748b;
                    font-size: 1.1rem;
                }

                .stats {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 20px;
                    margin: 30px 0;
                }

                .stat-card {
                    background: #f8fafc;
                    padding: 20px;
                    border-radius: 12px;
                    border: 1px solid #e2e8f0;
                }

                .stat-number {
                    font-size: 2rem;
                    font-weight: bold;
                    color: #4f46e5;
                }

                .stat-label {
                    color: #64748b;
                    margin-top: 5px;
                }

                .winners-section {
                    margin: 30px 0;
                }

                .winners-title {
                    font-size: 1.5rem;
                    color: #1e293b;
                    margin-bottom: 20px;
                }

                .winners-list {
                    display: grid;
                    gap: 15px;
                }

                .winner-item {
                    background: linear-gradient(135deg, #4f46e5, #7c3aed);
                    color: white;
                    padding: 20px;
                    border-radius: 12px;
                    font-size: 1.2rem;
                    font-weight: 600;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    animation: fadeInUp 0.6s ease-out;
                }

                .winner-item:nth-child(1) {
                    background: linear-gradient(135deg, #f59e0b, #f97316);
                    font-size: 1.4rem;
                    transform: scale(1.05);
                }

                .winner-item:nth-child(2) {
                    background: linear-gradient(135deg, #10b981, #059669);
                }

                .winner-item:nth-child(3) {
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                }

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .meta-info {
                    margin-top: 30px;
                    padding: 20px;
                    background: #f8fafc;
                    border-radius: 12px;
                    border: 1px solid #e2e8f0;
                }

                .meta-info p {
                    color: #64748b;
                    margin: 5px 0;
                }

                .back-link {
                    margin-top: 30px;
                }

                .back-link a {
                    display: inline-block;
                    padding: 12px 24px;
                    background: #4f46e5;
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }

                .back-link a:hover {
                    background: #7c3aed;
                    transform: translateY(-2px);
                }

                @media (max-width: 480px) {
                    .container {
                        padding: 20px;
                    }

                    .header h1 {
                        font-size: 2rem;
                    }

                    .winner-item {
                        font-size: 1rem;
                    }

                    .winner-item:nth-child(1) {
                        font-size: 1.2rem;
                    }
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="header">
                    <h1>🎯 <?= $t['luckyDrawResults'] ?></h1>
                    <p><?= $t['congratulations'] ?></p>
                </div>

                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($winners) ?></div>
                        <div class="stat-label"><?= $t['totalWinners'] ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $ageInDays ?></div>
                        <div class="stat-label"><?= $t['daysAgo'] ?></div>
                    </div>
                </div>

                <div class="winners-section">
                    <h2 class="winners-title">🏆 <?= $t['winnersList'] ?></h2>
                    <div class="winners-list">
                        <?php foreach ($winners as $index => $winner): ?>
                            <div class="winner-item" style="animation-delay: <?= $index * 0.1 ?>s">
                                <?php if ($index === 0): ?>
                                    🥇 <?= htmlspecialchars($winner) ?>
                                <?php elseif ($index === 1): ?>
                                    🥈 <?= htmlspecialchars($winner) ?>
                                <?php elseif ($index === 2): ?>
                                    🥉 <?= htmlspecialchars($winner) ?>
                                <?php else: ?>
                                    🎉 <?= htmlspecialchars($winner) ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="meta-info">
                    <p><strong><?= $t['drawDate'] ?>:</strong> <?= $createdAt->format('F j, Y \a\t g:i A') ?></p>
                    <p><strong><?= $t['shareId'] ?>:</strong> <?= htmlspecialchars($sharedDraw->shareId) ?></p>
                    <p><strong><?= $t['daysRemaining'] ?>:</strong> <?= 30 - $ageInDays ?> days</p>
                </div>

                <div class="back-link">
                    <a href="/">🎲 <?= $t['createYourOwnDraw'] ?></a>
                </div>
            </div>
        </body>

        </html>
    <?php
    }

    private function renderError(string $title, string $message): void
    {
        // Simple translations for error page
        $translations = [
            'en' => [
                'errorTitle' => 'Error - Lucky Draw',
                'createYourOwnDraw' => 'Create Your Own Draw'
            ],
            'vi' => [
                'errorTitle' => 'Lỗi - Quay số may mắn',
                'createYourOwnDraw' => 'Tạo quay số của riêng bạn'
            ]
        ];

        // Detect language from Accept-Language header or default to English
        $lang = 'en';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if (strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'vi') !== false) {
                $lang = 'vi';
            }
        }

        $t = $translations[$lang];
    ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $t['errorTitle'] ?></title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }

                .error-container {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                    padding: 40px;
                    max-width: 500px;
                    width: 100%;
                    text-align: center;
                }

                .error-icon {
                    font-size: 4rem;
                    margin-bottom: 20px;
                }

                .error-title {
                    font-size: 1.8rem;
                    color: #ef4444;
                    margin-bottom: 15px;
                }

                .error-message {
                    color: #64748b;
                    margin-bottom: 30px;
                    line-height: 1.6;
                }

                .back-link a {
                    display: inline-block;
                    padding: 12px 24px;
                    background: #4f46e5;
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }

                .back-link a:hover {
                    background: #7c3aed;
                    transform: translateY(-2px);
                }
            </style>
        </head>

        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1 class="error-title"><?= htmlspecialchars($title) ?></h1>
                <p class="error-message"><?= htmlspecialchars($message) ?></p>
                <div class="back-link">
                    <a href="/">🎲 <?= $t['createYourOwnDraw'] ?></a>
                </div>
            </div>
        </body>

        </html>
<?php
    }
}
