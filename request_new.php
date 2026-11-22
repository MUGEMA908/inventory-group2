<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireRole('coordinator');

$errors = [];
$old = ['name' => '', 'category' => '', 'qty' => '1', 'priority' => 'Medium', 'why' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    foreach ($old as $k => $_) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    if ($old['name'] === '' || mb_strlen($old['name']) > 120) $errors[] = 'Enter the equipment you need (up to 120 characters).';
    if (!in_array($old['category'], Equipment::CATEGORIES, true)) $errors[] = 'Choose a category.';
    if (filter_var($old['qty'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100000]]) === false) $errors[] = 'Quantity must be a whole number of 1 or more.';
    if (!in_array($old['priority'], EquipmentRequest::PRIORITIES, true)) $errors[] = 'Choose a priority.';
    if (mb_strlen($old['why']) < 10) $errors[] = 'Explain why it is needed (at least 10 characters).';

    if (!$errors) {
        EquipmentRequest::create($old['name'], $old['category'], (int) $old['qty'], $old['priority'], $old['why'], Auth::id());
        Auth::flash('success', 'Request sent to the Head of Department.');
        redirect('request_new.php');
    }
}

$pager = new Paginator(EquipmentRequest::countByUser(Auth::id()), 5, (int) ($_GET['page'] ?? 1));
$mine  = EquipmentRequest::paginateByUser(Auth::id(), $pager->limit(), $pager->offset());

$pageTitle = 'Request equipment';
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
    <div class="span2"><label for="name">Equipment needed</label>
      <input id="name" name="name" value="<?= e($old['name']) ?>" required maxlength="120"></div>
    <div><label for="category">Category</label>
      <select id="category" name="category" required>
        <option value="">Select…</option>
        <?php foreach (Equipment::CATEGORIES as $c): ?>
          <option <?= $old['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label for="qty">Quantity</label>
      <input id="qty" name="qty" type="number" min="1" value="<?= e($old['qty']) ?>" required></div>
    <div><label for="priority">Priority</label>
      <select id="priority" name="priority">
        <?php foreach (EquipmentRequest::PRIORITIES as $p): ?>
          <option <?= $old['priority'] === $p ? 'selected' : '' ?>><?= e($p) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="span2"><label for="why">Why is it needed?</label>
      <textarea id="why" name="why" rows="3" required><?= e($old['why']) ?></textarea></div>
    <div class="span2"><button class="btn btn-primary" type="submit">Send request</button></div>
  </form>
</section>

<section class="panel">
  <h2>Your requests</h2>
  <?php if (!$mine): ?>
    <p class="empty">You have not requested anything yet. Use the form above to send your first request.</p>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Equipment</th><th>Category</th><th>Qty</th><th>Priority</th><th>Status</th><th>Sent</th></tr></thead>
    <tbody>
    <?php foreach ($mine as $r): ?>
      <tr>
        <td><strong><?= e($r['equipment_name']) ?></strong><br><small class="muted"><?= e($r['justification']) ?></small></td>
        <td><?= e($r['category']) ?></td><td><?= (int) $r['quantity'] ?></td>
        <td><?= badge($r['priority']) ?></td><td><?= badge($r['status']) ?></td>
        <td><?= e(substr((string) $r['created_at'], 0, 10)) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
  <?= $pager->render() ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
