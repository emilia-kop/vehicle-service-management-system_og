<?php
require 'includes/db.php';

// GROUP BY: Revenue per mechanic
$mechanic_revenue = $conn->query("
    SELECT m.mechanic_name, m.specialization,
           COUNT(sr.record_id) AS total_jobs,
           SUM(s.service_cost) AS total_revenue
    FROM Service_Record sr
    JOIN Mechanic m ON sr.mechanic_id=m.mechanic_id
    JOIN Service  s ON sr.service_id=s.service_id
    GROUP BY m.mechanic_id, m.mechanic_name, m.specialization
    ORDER BY total_revenue DESC");

// GROUP BY: Spending per vehicle
$vehicle_spending = $conn->query("
    SELECT v.vehicle_number, v.brand, c.name AS owner,
           COUNT(sr.record_id) AS service_count,
           SUM(s.service_cost) AS total_spent
    FROM Service_Record sr
    JOIN Vehicle  v ON sr.vehicle_id=v.vehicle_id
    JOIN Customer c ON v.customer_id=c.customer_id
    JOIN Service  s ON sr.service_id=s.service_id
    GROUP BY v.vehicle_id, v.vehicle_number, v.brand, c.name
    ORDER BY total_spent DESC");

// ORDER BY: Services by cost
$service_list = $conn->query("SELECT service_name, service_cost FROM Service ORDER BY service_cost DESC");

// Total revenue
$tot = $conn->query("SELECT SUM(s.service_cost) AS total FROM Service_Record sr JOIN Service s ON sr.service_id=s.service_id")->fetch_assoc()['total'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Reports</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php';?>
<div class="container">
<div class="page-title"><span class="icon">📊</span> Reports & Analytics</div>

<div class="stats-row" style="grid-template-columns:repeat(2,1fr)">
  <div class="stat-card" style="border-top-color:#2e7d32">
    <div class="stat-value" style="color:#2e7d32">Rs.<?=number_format($tot??0,2)?></div>
    <div class="stat-label">💰 Total Revenue Collected</div>
  </div>
</div>

<!-- Report 1: GROUP BY Mechanic -->
<div class="card">
<div class="card-header">👨‍🔧 Revenue by Mechanic <small style="color:#999;font-size:.8rem">(GROUP BY mechanic — ORDER BY revenue DESC)</small></div>
<div class="table-wrap"><table>
<thead><tr><th>Mechanic</th><th>Specialization</th><th>Total Jobs</th><th>Total Revenue (Rs)</th></tr></thead>
<tbody>
<?php while($row=$mechanic_revenue->fetch_assoc()):?>
<tr>
  <td><?=htmlspecialchars($row['mechanic_name'])?></td>
  <td><span class="badge"><?=htmlspecialchars($row['specialization'])?></span></td>
  <td><?=$row['total_jobs']?></td>
  <td><strong>Rs.<?=number_format($row['total_revenue'],2)?></strong></td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>

<!-- Report 2: GROUP BY Vehicle -->
<div class="card">
<div class="card-header">🚗 Service History per Vehicle <small style="color:#999;font-size:.8rem">(GROUP BY vehicle — ORDER BY spending DESC)</small></div>
<div class="table-wrap"><table>
<thead><tr><th>Vehicle No.</th><th>Brand</th><th>Owner</th><th>Services Done</th><th>Total Spent (Rs)</th></tr></thead>
<tbody>
<?php while($row=$vehicle_spending->fetch_assoc()):?>
<tr>
  <td><span class="badge"><?=htmlspecialchars($row['vehicle_number'])?></span></td>
  <td><?=htmlspecialchars($row['brand'])?></td>
  <td><?=htmlspecialchars($row['owner'])?></td>
  <td><?=$row['service_count']?></td>
  <td><strong>Rs.<?=number_format($row['total_spent'],2)?></strong></td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>

<!-- Report 3: ORDER BY Cost -->
<div class="card">
<div class="card-header">⚙️ Services by Cost <small style="color:#999;font-size:.8rem">(ORDER BY service_cost DESC)</small></div>
<div class="table-wrap"><table>
<thead><tr><th>Rank</th><th>Service Name</th><th>Cost (Rs)</th></tr></thead>
<tbody>
<?php $rank=1; while($row=$service_list->fetch_assoc()):?>
<tr>
  <td><?=$rank++?></td>
  <td><?=htmlspecialchars($row['service_name'])?></td>
  <td>Rs.<?=number_format($row['service_cost'],2)?></td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>
</div></body></html>
