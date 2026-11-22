<?php
require_once __DIR__ . '/includes/bootstrap.php';
redirect(Auth::check() ? 'dashboard.php' : 'login.php');
