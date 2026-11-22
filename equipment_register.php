<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireRole('logistic');

$errors = [];
$old = ['name' => '', 'category' => '', 'serial' => '', 'qty' => '1', 'cond' => 'New', 'location' => '', 'desc' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    if ($old['name'] === '' || mb_strlen($old['name']) > 120) $errors[] = 'Enter the equipment name (up to 120 characters).';
    if (!in_array($old['category'], Equipment::CATEGORIES, true)) $errors[] = 'Choose a category.';
    if ($old['serial'] === '' || mb_strlen($old['serial']) > 80) $errors[] = 'Enter the serial number.';
    if (filter_var($old['qty'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100000]]) === false) $errors[] = 'Quantity must be a whole number of 1 or more.';
    if (!in_array($old['cond'], Equipment::CONDITIONS, true)) $errors[] = 'Choose the condition.';
    if (!$errors && Equipment::serialExists($old['serial'])) $errors[] = 'That serial number is already registered.';

    if (!$errors) {
        Equipment::register($old['name'], $old['category'], $old['serial'], (int) $old['qty'], $old['cond'], $old['location'], $old['desc'], Auth::id());
        Auth::flash('success', '“' . $old['name'] . '” was registered.');
        redirect('equipment_register.php');
    }
}

$search = trim((string) ($_GET['q'] ?? ''));
$pager  = new Paginator(Equipment::count(), 5, (int) ($_GET['page'] ?? 1));
$items  = Equipment::paginate('', $pager->limit(), $pager->offset());

$pageTitle = 'Register equipment';
require __DIR__ . '/includes/header.php';
?>
<section class="panel">
  <?php if ($errors): ?>
    <div class="alert alert-error" role="alert">
      <strong>Fix the following and try again:</strong>
      <ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>
  <form method="post" class="grid-form" novalidate>
    <?= Auth::csrfField() ?>
    <div class="span2"><label for="name">Equipment name</label>
      <input id="name" name="name" value="<?= e($old['name']) ?>" required maxlength="120"></div>
    <div><label for="category">Category</label>
      <select id="category" name="category" required>
        <option value="">Select…</option>
        <?php foreach (Equipment::CATEGORIES as $c): ?>
          <option <?= $old['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label for="serial">Serial number</label>
      <input id="serial" name="serial" value="<?= e($old['serial']) ?>" required maxlength="80"></div>
    <div><label for="qty">Quantity</label>
      <input id="qty" name="qty" type="number" min="1" value="<?= e($old['qty']) ?>" required></div>
    <div><label for="cond">Condition</label>
      <select id="cond" name="cond">
        <?php foreach (Equipment::CONDITIONS as $c): ?>
          <option <?= $old['cond'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="span2"><label for="location">Location</label>
      <input id="location" name="location" value="<?= e($old['location']) ?>" maxlength="100" placeholder="e.g. ICT Lab 1"></div>
    <div class="span2"><label for="desc">Description</label>
      <textarea id="desc" name="desc" rows="3"><?= e($old['desc']) ?></textarea></div>
    <div class="span2"><button class="btn btn-primary" type="submit">Register equipment</button></div>
  </form>
</section>

<section class="panel">
  <h2>Recently registered</h2>
  <?php require __DIR__ . '/includes/equipment_table.php'; ?>
  <?= $pager->render() ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
