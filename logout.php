<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::logout();
Auth::start();
Auth::flash('success', 'You have been logged out.');
redirect('login.php');
