<?php
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireRole('hod');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::verifyCsrf();
    $id     = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $status = $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : '');
    if ($id > 0 && $status !== '' && EquipmentRequest::decide($id, $status, Auth::id())) {
        Auth::flash('success', 'Request ' . $status . '.');
    } else {
        Auth::flash('error', 'That request could not be updated. It may already have been decided.');
    }
    redirect('approve_requests.php?' . http_build_query(array_filter([
        'status' => $_POST['status'] ?? '', 'page' => $_POST['page'] ?? '',
    ])));
}

$filters = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
$current = (string) ($_GET['status'] ?? 'pending');
if (!isset($filters[$current])) $current = 'pending';
$dbStatus = $current === 'all' ? '' : $current;

$pager = new Paginator(EquipmentRequest::count($dbStatus), PER_PAGE, (int) ($_GET['page'] ?? 1));
$rows  = EquipmentRequest::paginate($dbStatus, $pager->limit(), $pager->offset());

$pageTitle = 'Approve requests';
require __DIR__ . '/includes/header.php';
?>
<section class="panel">
  <div class="tabs" role="tablist">
    <?php foreach ($filters as $key => $label): ?>
      <a href="?status=<?= e($key) ?>" class="<?= $current === $key ? 'on' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>
  <?php if (!$rows): ?>
    <p class="empty"><?= $current === 'pending' ? 'Nothing is waiting for your decision.' : 'No requests in this list.' ?></p>
  <?php else: ?>
  <div class="table-wrap"><table>
    <thead><tr><th>Equipment</th><th>Requested by</th><th>Qty</th><th>Priority</th><th>Status</th><th>Decision</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><strong><?= e($r['equipment_name']) ?></strong> <small class="muted">· <?= e($r['category']) ?></small><br><small class="muted"><?= e($r['justification']) ?></small></td>
        <td><?= e($r['requester']) ?><br><small class="muted"><?= e(substr((string) $r['created_at'], 0, 10)) ?></small></td>
        <td><?= (int) $r['quantity'] ?></td>
        <td><?= badge($r['priority']) ?></td>
        <td><?= badge($r['status']) ?></td>
        <td>
          <?php if ($r['status'] === 'pending'): ?>
          <form method="post" class="inline">
            <?= Auth::csrfField() ?>
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <input type="hidden" name="status" value="<?= e($current) ?>">
            <input type="hidden" name="page" value="<?= (int) $pager->page ?>">
            <button class="btn btn-ok btn-sm" name="action" value="approve" type="submit">Approve</button>
            <button class="btn btn-danger btn-sm" name="action" value="reject" type="submit">Reject</button>
          </form>
          <?php else: ?>
            <small class="muted">By <?= e($r['decider'] ?? '—') ?><br><?= e(substr((string) $r['decided_at'], 0, 10)) ?></small>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
  <?= $pager->render() ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
