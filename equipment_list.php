<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();

$search = trim((string) ($_GET['q'] ?? ''));
$pager  = new Paginator(Equipment::count($search), PER_PAGE, (int) ($_GET['page'] ?? 1));
$items  = Equipment::paginate($search, $pager->limit(), $pager->offset());

$pageTitle = Auth::role() === 'coordinator' ? 'View equipment' : 'Equipment list';
require __DIR__ . '/includes/header.php';
?>
<section class="panel">
  <form method="get" class="search">
    <label for="q" class="sr">Search equipment</label>
    <input id="q" name="q" type="search" value="<?= e($search) ?>" placeholder="Search by name, category or serial number">
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($search !== ''): ?><a class="btn" href="equipment_list.php">Clear</a><?php endif; ?>
  </form>
  <?php require __DIR__ . '/includes/equipment_table.php'; ?>
  <?= $pager->render() ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
