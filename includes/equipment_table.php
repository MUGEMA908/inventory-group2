<?php /** Expects $items (array). */ ?>
<?php if (!$items): ?>
  <p class="empty">No equipment found.</p>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>Equipment</th><th>Category</th><th>Serial no.</th><th>Qty</th><th>Condition</th><th>Location</th><th>Registered</th></tr></thead>
  <tbody>
  <?php foreach ($items as $it): ?>
    <tr>
      <td><strong><?= e($it['name']) ?></strong><?php if ($it['description']): ?><br><small class="muted"><?= e($it['description']) ?></small><?php endif; ?></td>
      <td><?= e($it['category']) ?></td>
      <td><code><?= e($it['serial_number']) ?></code></td>
      <td><?= (int) $it['quantity'] ?></td>
      <td><?= badge($it['condition_status']) ?></td>
      <td><?= e($it['location'] ?: '—') ?></td>
      <td><?= e(substr((string) $it['created_at'], 0, 10)) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
