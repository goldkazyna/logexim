<?php

// Временный файл для запуска artisan команд через браузер
// УДАЛИТЬ ПОСЛЕ НАСТРОЙКИ!
$secret = 'logexim2026setup';

if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('Access denied');
}

$command = $_GET['cmd'] ?? '';
$allowed = ['migrate', 'config:cache', 'config:clear', 'route:cache', 'route:clear', 'view:cache', 'view:clear', 'storage:link'];

if (!in_array($command, $allowed)) {
    die('Allowed commands: ' . implode(', ', $allowed));
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Artisan::call($command);
echo '<pre>' . Artisan::output() . '</pre>';
echo '<br>Done: ' . $command;
