<?php
/** Authenticated layout. Set $pageTitle and $active before including. */
$me    = Auth::user();
$role  = $me['role'];
$flash = Auth::pullFlash();

$nav = ['dashboard.php' => 'Dashboard'];
if ($role === 'logistic')    { $nav['equipment_register.php'] = 'Register equipment'; $nav['equipment_list.php'] = 'Equipment list'; }
if ($role === 'coordinator') { $nav['request_new.php'] = 'Request equipment'; $nav['equipment_list.php'] = 'View equipment'; }
if ($role === 'hod')         { $nav['approve_requests.php'] = 'Approve requests'; $nav['equipment_list.php'] = 'Equipment list'; }
$nav['change_password.php'] = 'Change password';
$initial = strtoupper(substr($me['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="only light">
<meta name="color-scheme" content="only light">
<meta name="color-scheme" content="light">
<title><?= e($pageTitle ?? 'Dashboard') ?> · <?= e(APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="shell">
  <aside class="side">
    <div class="brand">
      <span class="brand-mark" aria-hidden="true"></span>
      <div><strong>Ngoma Inventory</strong><small>ICT Department</small></div>
    </div>
    <nav class="menu" aria-label="Main">
      <?php foreach ($nav as $file => $label): ?>
        <a href="<?= e($file) ?>" class="<?= basename($_SERVER['SCRIPT_NAME']) === $file ? 'on' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="who">
      <span class="avatar"><?= e($initial) ?></span>
      <div><strong><?= e($me['name']) ?></strong><small><?= e(User::ROLE_LABELS[$role]) ?></small></div>
      <a class="logout" href="logout.php">Log out</a>
    </div>
  </aside>
  <main class="main">
    <header class="top"><h1><?= e($pageTitle ?? '') ?></h1></header>
    <?php if ($flash): ?>
      <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endif; ?>
