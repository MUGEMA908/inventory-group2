<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/../src/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

/** Escape output (XSS protection). */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $to)
{
    header('Location: ' . $to);
    exit;
}

function badge(string $text): string
{
    $key = strtolower(str_replace(' ', '-', $text));
    return '<span class="badge badge-' . e($key) . '">' . e(ucfirst($text)) . '</span>';
}

Auth::start();
