<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    $error = User::changePassword(
        Auth::id(),
        (string) ($_POST['current'] ?? ''),
        (string) ($_POST['new'] ?? ''),
        (string) ($_POST['confirm'] ?? '')
    ) ?? '';
    if ($error === '') {
        Auth::flash('success', 'Password changed. Use the new password next time you log in.');
        redirect('change_password.php');
    }
}

$pageTitle = 'Change password';
require __DIR__ . '/includes/header.php';
?>
<section class="panel narrow">
  <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" novalidate>
    <?= Auth::csrfField() ?>
    <label for="current">Current password</label>
    <input id="current" name="current" type="password" required autocomplete="current-password">
    <label for="new">New password</label>
    <input id="new" name="new" type="password" required minlength="8" autocomplete="new-password">
    <small class="hint">At least 8 characters, with a letter and a number.</small>
    <label for="confirm">Confirm new password</label>
    <input id="confirm" name="confirm" type="password" required autocomplete="new-password">
    <button class="btn btn-primary" type="submit">Save password</button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
