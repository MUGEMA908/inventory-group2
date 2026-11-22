<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireLogin();
$role = Auth::role();
$uid  = Auth::id();

$cards = [];
$tableTitle = '';
$rows = [];

if ($role === 'logistic') {
    $cards = [
        ['Equipment types', Equipment::count(), 'registered items'],
        ['Total units', Equipment::totalUnits(), 'across all items'],
        ['Approved requests', EquipmentRequest::count('approved'), 'waiting to be registered'],
    ];
    $tableTitle = 'Approved requests to register when equipment arrives';
    $rows = EquipmentRequest::recentApproved(5);
} elseif ($role === 'coordinator') {
    $cards = [
        ['Pending', EquipmentRequest::count('pending', $uid), 'awaiting the HoD'],
        ['Approved', EquipmentRequest::count('approved', $uid), 'your requests'],
        ['Rejected', EquipmentRequest::count('rejected', $uid), 'your requests'],
        ['Equipment types', Equipment::count(), 'in the inventory'],
    ];
} else {
    $cards = [
        ['Pending', EquipmentRequest::count('pending'), 'need your decision'],
        ['Approved', EquipmentRequest::count('approved'), 'so far'],
        ['Rejected', EquipmentRequest::count('rejected'), 'so far'],
    ];
}

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<p class="lead">Welcome back, <?= e(Auth::user()['name']) ?>.</p>

<section class="stats">
  <?php foreach ($cards as [$label, $value, $hint]): ?>
    <div class="stat">
      <span class="stat-value"><?= (int) $value ?></span>
      <span class="stat-label"><?= e($label) ?></span>
      <span class="stat-hint"><?= e($hint) ?></span>
    </div>
  <?php endforeach; ?>
</section>

<section class="panel">
  <h2>Quick actions</h2>
  <div class="actions">
    <?php if ($role === 'logistic'): ?>
      <a class="btn btn-primary" href="equipment_register.php">Register equipment</a>
      <a class="btn" href="equipment_list.php">View inventory</a>
    <?php elseif ($role === 'coordinator'): ?>
      <a class="btn btn-primary" href="request_new.php">Request equipment</a>
      <a class="btn" href="equipment_list.php">View equipment</a>
    <?php else: ?>
      <a class="btn btn-primary" href="approve_requests.php">Review pending requests</a>
      <a class="btn" href="equipment_list.php">View equipment</a>
    <?php endif; ?>
    <a class="btn" href="change_password.php">Change password</a>
  </div>
</section>

<?php if ($role === 'logistic'): ?>
<section class="panel">
  <h2><?= e($tableTitle) ?></h2>
  <?php if (!$rows): ?>
    <p class="empty">No approved requests yet. They appear here once the HoD approves them.</p>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Equipment</th><th>Category</th><th>Qty</th><th>Approved on</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr><td><?= e($r['equipment_name']) ?></td><td><?= e($r['category']) ?></td><td><?= (int) $r['quantity'] ?></td><td><?= e(substr((string) $r['decided_at'], 0, 10)) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
