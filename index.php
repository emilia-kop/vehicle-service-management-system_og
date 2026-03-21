<?php
require 'includes/db.php';
$stats = [];
foreach (['Customer','Vehicle','Mechanic','Service_Record'] as $t) {
    $r = $conn->query("SELECT COUNT(*) AS c FROM $t");
    $stats[$t] = $r ? $r->fetch_assoc()['c'] : 0;
}
$recent = $conn->query("
    SELECT sr.record_id, c.name AS customer, v.vehicle_number,
           s.service_name, s.service_cost, m.mechanic_name, sr.service_date
    FROM Service_Record sr
    JOIN Vehicle  v ON sr.vehicle_id  = v.vehicle_id
    JOIN Customer c ON v.customer_id  = c.customer_id
    JOIN Mechanic m ON sr.mechanic_id = m.mechanic_id
    JOIN Service  s ON sr.service_id  = s.service_id
    ORDER BY sr.service_date DESC LIMIT 8");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Dashboard</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php'; ?>
<div class="container">
<div class="page-title"><span class="icon">🏠</span> Dashboard</div>
<div class="stats-row">
  <div class="stat-card"><div class="stat-value"><?=$stats['Customer']?></div><div class="stat-label">👤 Total Customers</div></div>
  <div class="stat-card" style="border-top-color:#ff6d00"><div class="stat-value" style="color:#ff6d00"><?=$stats['Vehicle']?></div><div class="stat-label">🚗 Vehicles</div></div>
  <div class="stat-card" style="border-top-color:#2e7d32"><div class="stat-value" style="color:#2e7d32"><?=$stats['Mechanic']?></div><div class="stat-label">🛠 Mechanics</div></div>
  <div class="stat-card" style="border-top-color:#7b1fa2"><div class="stat-value" style="color:#7b1fa2"><?=$stats['Service_Record']?></div><div class="stat-label">📋 Service Records</div></div>
</div>
<div class="card">
<div class="card-header">📋 Recent Service Records</div>
<div class="table-wrap"><table>
<thead><tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Service</th><th>Cost (Rs)</th><th>Mechanic</th><th>Date</th></tr></thead>
<tbody>
<?php if($recent) while($row=$recent->fetch_assoc()): ?>
<tr>
  <td><?=$row['record_id']?></td>
  <td><?=htmlspecialchars($row['customer'])?></td>
  <td><span class="badge"><?=htmlspecialchars($row['vehicle_number'])?></span></td>
  <td><?=htmlspecialchars($row['service_name'])?></td>
  <td>Rs.<?=number_format($row['service_cost'],2)?></td>
  <td><?=htmlspecialchars($row['mechanic_name'])?></td>
  <td><?=$row['service_date']?></td>
</tr>
<?php endwhile; ?>
</tbody></table></div>
</div>
</div></body></html>
