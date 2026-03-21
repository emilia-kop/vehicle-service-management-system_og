<?php
require 'includes/db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action    = $_POST['action'];
    $cid       = (int)$_POST['customer_id'];
    $vnum      = $conn->real_escape_string(trim($_POST['vehicle_number']));
    $model     = $conn->real_escape_string(trim($_POST['model']));
    $brand     = $conn->real_escape_string(trim($_POST['brand']));

    if ($action==='insert') {
        if ($conn->query("INSERT INTO Vehicle(customer_id,vehicle_number,model,brand) VALUES($cid,'$vnum','$model','$brand')"))
            $msg='<div class="alert alert-success">✅ Vehicle registered!</div>';
        else
            $msg='<div class="alert alert-danger">❌ '.htmlspecialchars($conn->error).'</div>';
    } else {
        $vid = (int)$_POST['vehicle_id'];
        if ($conn->query("UPDATE Vehicle SET customer_id=$cid,vehicle_number='$vnum',model='$model',brand='$brand' WHERE vehicle_id=$vid"))
            $msg='<div class="alert alert-success">✅ Vehicle updated!</div>';
        else
            $msg='<div class="alert alert-danger">❌ '.htmlspecialchars($conn->error).'</div>';
    }
}
if (isset($_GET['delete'])) {
    $vid=(int)$_GET['delete'];
    $conn->query("DELETE FROM Vehicle WHERE vehicle_id=$vid")
        ? $msg='<div class="alert alert-success">✅ Vehicle deleted.</div>'
        : $msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
}
$edit=null;
if (isset($_GET['edit'])) {
    $res=$conn->query("SELECT * FROM Vehicle WHERE vehicle_id=".(int)$_GET['edit']);
    $edit=$res->fetch_assoc();
}
$vehicles = $conn->query("SELECT v.*,c.name AS owner FROM Vehicle v JOIN Customer c ON v.customer_id=c.customer_id ORDER BY v.vehicle_id DESC");
$customers= $conn->query("SELECT customer_id,name FROM Customer ORDER BY name");
$clist=[]; while($r=$customers->fetch_assoc()) $clist[]=$r;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Vehicles</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php'; ?>
<div class="container">
<div class="page-title"><span class="icon">🚗</span> Vehicle Management</div>
<?=$msg?>
<div class="card">
<div class="card-header"><?=$edit?'✏️ Edit Vehicle':'➕ Register Vehicle'?></div>
<form method="POST">
<input type="hidden" name="action" value="<?=$edit?'update':'insert'?>">
<?php if($edit):?><input type="hidden" name="vehicle_id" value="<?=$edit['vehicle_id']?>"><?php endif;?>
<div class="form-grid">
  <div class="form-group">
    <label>Owner (Customer)</label>
    <select name="customer_id" required>
      <option value="">-- Select Customer --</option>
      <?php foreach($clist as $c): ?>
      <option value="<?=$c['customer_id']?>" <?=($edit&&$edit['customer_id']==$c['customer_id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
      <?php endforeach;?>
    </select>
  </div>
  <div class="form-group">
    <label>Vehicle Number</label>
    <input type="text" name="vehicle_number" required placeholder="e.g. KL01AB1234" value="<?=htmlspecialchars($edit['vehicle_number']??'')?>">
  </div>
  <div class="form-group">
    <label>Brand</label>
    <input type="text" name="brand" required placeholder="e.g. Maruti Suzuki" value="<?=htmlspecialchars($edit['brand']??'')?>">
  </div>
  <div class="form-group">
    <label>Model</label>
    <input type="text" name="model" required placeholder="e.g. Swift Dzire" value="<?=htmlspecialchars($edit['model']??'')?>">
  </div>
</div><br>
<button class="btn btn-<?=$edit?'warning':'primary'?>" type="submit"><?=$edit?'💾 Update':'➕ Register'?></button>
<?php if($edit):?><a href="vehicles.php" class="btn btn-danger" style="margin-left:10px">✖ Cancel</a><?php endif;?>
</form>
</div>
<div class="card">
<div class="card-header">🚗 All Vehicles</div>
<div class="table-wrap"><table>
<thead><tr><th>ID</th><th>Owner</th><th>Vehicle No.</th><th>Brand</th><th>Model</th><th>Actions</th></tr></thead>
<tbody>
<?php while($row=$vehicles->fetch_assoc()):?>
<tr>
  <td><?=$row['vehicle_id']?></td>
  <td><?=htmlspecialchars($row['owner'])?></td>
  <td><span class="badge"><?=htmlspecialchars($row['vehicle_number'])?></span></td>
  <td><?=htmlspecialchars($row['brand'])?></td>
  <td><?=htmlspecialchars($row['model'])?></td>
  <td>
    <a href="?edit=<?=$row['vehicle_id']?>" class="btn btn-warning btn-sm">✏️ Edit</a>
    <a href="?delete=<?=$row['vehicle_id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">🗑 Delete</a>
  </td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>
</div></body></html>
