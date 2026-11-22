<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (Auth::check()) {
    redirect('dashboard.php');
}

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Enter your username and password.';
    } elseif (Auth::attempt($username, $password)) {
        redirect('dashboard.php');
    } else {
        $error = 'Username or password is incorrect.';
    }
}
$flash = Auth::pullFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="only light">
<meta name="color-scheme" content="only light">
<meta name="color-scheme" content="only light">
<meta name="color-scheme" content="light">
<title>Log in · <?= e(APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
<div class="login">
  <section class="login-intro">
    <div class="brand brand-lg">
      <span class="brand-mark" aria-hidden="true"></span>
      <div><strong>Ngoma Inventory</strong><small>Rwanda Polytechnic · Ngoma College</small></div>
    </div>
    <h1>Know what the department owns, and what it still needs.</h1>
    <ul class="roles">
      <li><strong>Logistic officer</strong> registers new equipment as it arrives.</li>
      <li><strong>Coordinator</strong> checks stock and requests what is missing.</li>
      <li><strong>Head of Department</strong> approves or rejects each request.</li>
    </ul>
  </section>
  <section class="login-card">
    <h2>Log in</h2>
    <p class="muted">Use the account given to you by the department.</p>
    <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <form method="post" autocomplete="on" novalidate>
      <?= Auth::csrfField() ?>
      <label for="username">Username</label>
      <input id="username" name="username" type="text" value="<?= e($username) ?>" required autofocus autocomplete="username">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">
      <button class="btn btn-primary btn-block" type="submit">Log in</button>
    </form>
  </section>
</div>
</body>
</html>
