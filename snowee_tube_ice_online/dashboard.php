<?php
require_once 'config.php'; require_login();
$page_title = 'Dashboard';
$pdo = db();
$agents = (int)$pdo->query("SELECT COUNT(*) FROM agents WHERE status='Active'")->fetchColumn();
$released = (float)$pdo->query("SELECT COALESCE(SUM(package_kg*quantity),0) FROM inventory_releases")->fetchColumn();
$deliveredKg = (float)$pdo->query("SELECT COALESCE(SUM(package_kg*quantity),0) FROM deliveries WHERE status <> 'Cancelled'")->fetchColumn();
$kg = $released - $deliveredKg;
$sales = (float)$pdo->query("SELECT COALESCE(SUM(package_kg*quantity*price_per_kg),0) FROM deliveries WHERE status='Delivered'")->fetchColumn();
$remit = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM remittances")->fetchColumn();
$recent = $pdo->query("SELECT d.*, a.name AS agent_name FROM deliveries d JOIN agents a ON a.id=d.agent_id ORDER BY d.delivery_date DESC,d.id DESC LIMIT 6")->fetchAll();
require 'includes/header.php';
?><div class="shell"><?php require 'includes/sidebar.php'; ?><main class="main"><?php require 'includes/topbar.php'; ?><div class="content">
<div class="page-heading"><div><div class="eyebrow">OVERVIEW</div><h1>Good day, <?= e(current_user()) ?> 👋</h1><p>Here is what's happening at your ice plant today.</p></div><a class="btn" href="deliveries.php">＋ New delivery</a></div>
<div class="cards">
<div class="stat"><div class="stat-top">Active agents <span class="stat-icon">♙</span></div><strong><?= $agents ?></strong><small>Registered active agents</small></div>
<div class="stat"><div class="stat-top">Net tracked stock <span class="stat-icon">▤</span></div><strong><?= number_format($kg,2) ?> KG</strong><small>Released minus recorded deliveries</small></div>
<div class="stat"><div class="stat-top">Recorded sales <span class="stat-icon">₱</span></div><strong><?= money($sales) ?></strong><small>Delivered orders only</small></div>
<div class="stat"><div class="stat-top">Remittances received <span class="stat-icon">⇄</span></div><strong><?= money($remit) ?></strong><small>Total payments recorded</small></div>
</div>
<div class="panel"><div class="panel-head"><h2>Recent deliveries</h2><a class="btn btn-secondary" href="deliveries.php">View deliveries →</a></div><div class="table-wrap"><table><thead><tr><th>Date</th><th>Customer</th><th>Agent</th><th>Quantity</th><th>Total</th><th>Status</th></tr></thead><tbody>
<?php if (!$recent): ?><tr><td colspan="6" class="empty">No deliveries yet. Add an agent, record inventory, then enter a delivery.</td></tr><?php endif; ?>
<?php foreach ($recent as $r): ?><tr><td><?= e($r['delivery_date']) ?></td><td><?= e($r['customer_name']) ?></td><td><?= e($r['agent_name']) ?></td><td><?= e($r['quantity']) ?> × <?= e($r['package_kg']) ?> KG</td><td><?= money($r['package_kg']*$r['quantity']*$r['price_per_kg']) ?></td><td><span class="badge <?= $r['status']==='Pending'?'gold':($r['status']==='Cancelled'?'red':'') ?>"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<div class="quick-links"><a class="quick-link" href="agents.php">＋ Add agent<span>Manage your delivery agents</span></a><a class="quick-link" href="inventory.php">＋ Release stock<span>Track stock released to agents</span></a><a class="quick-link" href="remittance.php">＋ Record remittance<span>Track agent payments</span></a></div>
</div><?php require 'includes/footer.php'; ?></main></div>
