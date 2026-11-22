<?php
// Optional local override (not committed to GitHub): config/config.local.php
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

defined('APP_NAME')   or define('APP_NAME', 'Ngoma Inventory');
defined('DB_HOST')    or define('DB_HOST', 'localhost');
defined('DB_NAME')    or define('DB_NAME', 'inventory_db');
defined('DB_USER')    or define('DB_USER', 'root');
defined('DB_PASS')    or define('DB_PASS', '');
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4');
defined('PER_PAGE')   or define('PER_PAGE', 8);
