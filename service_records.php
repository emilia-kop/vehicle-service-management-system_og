<?php
require 'includes/db.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action'];
    $vid=(int)$_POST['vehicle_id'];
    $mid=(int)$_POST['mechanic_id'];
    $sid=(int)$_POST['service_id'];
    $date=$conn->real_escape_string($_POST['service_date']);
    if($action==='insert'){
        $conn->query("INSERT INTO Service_Record(vehicle_id,mechanic_id,service_id,service_date) VALUES($vid,$mid,$sid,'$date')")
            ?$msg='<div class="alert alert-success">✅ Service record added!</div>'
            :$msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
    } else {
        $rid=(int)$_POST['record_id'];
        $conn->query("UPDATE Service_Record SET vehicle_id=$vid,mechanic_id=$mid,service_id=$sid,service_date='$date' WHERE record_id=$rid")
            ?$msg='<div class="alert alert-success">✅ Record updated!</div>'
            :$msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
    }
}
if(isset($_GET['delete'])){
    $rid=(int)$_GET['delete'];
    $conn->query("DELETE FROM Service_Record WHERE record_id=$rid")
        ?$msg='<div class="alert alert-success">✅ Record deleted.</div>'
        :$msg='<div class="alert alert-danger">❌ Error.</div>';
}
$edit=null;
if(isset($_GET['edit'])){
    $res=$conn->query("SELECT * FROM Service_Record WHERE record_id=".(int)$_GET['edit']);
    $edit=$res->fetch_assoc();
}

// Dropdown data
$vehicles=$conn->query("SELECT v.vehicle_id,CONCAT(v.vehicle_number,' - ',c.name) AS label FROM Vehicle v JOIN Customer c ON v.customer_id=c.customer_id ORDER BY c.name");
$vlist=[]; while($r=$vehicles->fetch_assoc()) $vlist[]=$r;

$mechanics=$conn->query("SELECT mechanic_id,mechanic_name FROM Mechanic ORDER BY mechanic_name");
$mlist=[]; while($r=$mechanics->fetch_assoc()) $mlist[]=$r;

$services=$conn->query("SELECT service_id,service_name,service_cost FROM Service ORDER BY service_name");
$slist=[]; while($r=$services->fetch_assoc()) $slist[]=$r;

// All records
$records=$conn->query("
    SELECT sr.record_id, c.name AS customer, v.vehicle_number, v.brand,
           m.mechanic_name, s.service_name, s.service_cost, sr.service_date
    FROM Service_Record sr
    JOIN Vehicle  v ON sr.vehicle_id=v.vehicle_id
    JOIN Customer c ON v.customer_id=c.customer_id
    JOIN Mechanic m ON sr.mechanic_id=m.mechanic_id
    JOIN Service  s ON sr.service_id=s.service_id
    ORDER BY sr.service_date DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Service Records</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php';?>
<div class="container">
<div class="page-title"><span class="icon">📋</span> Service Records</div>
<?=$msg?>
<div class="card">
<div class="card-header"><?=$edit?'✏️ Edit Record':'➕ New Service Record'?></div>
<form method="POST">
<input type="hidden" name="action" value="<?=$edit?'update':'insert'?>">
<?php if($edit):?><input type="hidden" name="record_id" value="<?=$edit['record_id']?>"><?php endif;?>
<div class="form-grid">
  <div class="form-group">
    <label>Vehicle</label>
    <select name="vehicle_id" required>
      <option value="">-- Select Vehicle --</option>
      <?php foreach($vlist as $v):?>
      <option value="<?=$v['vehicle_id']?>" <?=($edit&&$edit['vehicle_id']==$v['vehicle_id'])?'selected':''?>><?=htmlspecialchars($v['label'])?></option>
      <?php endforeach;?>
    </select>
  </div>
  <div class="form-group">
    <label>Mechanic</label>
    <select name="mechanic_id" required>
      <option value="">-- Select Mechanic --</option>
      <?php foreach($mlist as $m):?>
      <option value="<?=$m['mechanic_id']?>" <?=($edit&&$edit['mechanic_id']==$m['mechanic_id'])?'selected':''?>><?=htmlspecialchars($m['mechanic_name'])?></option>
      <?php endforeach;?>
    </select>
  </div>
  <div class="form-group">
    <label>Service Type</label>
    <select name="service_id" required>
      <option value="">-- Select Service --</option>
      <?php foreach($slist as $s):?>
      <option value="<?=$s['service_id']?>" <?=($edit&&$edit['service_id']==$s['service_id'])?'selected':''?>>
        <?=htmlspecialchars($s['service_name'])?> (Rs.<?=number_format($s['service_cost'],2)?>)
      </option>
      <?php endforeach;?>
    </select>
  </div>
  <div class="form-group">
    <label>Service Date</label>
    <input type="date" name="service_date" required value="<?=$edit['service_date']??date('Y-m-d')?>">
  </div>
</div><br>
<button class="btn btn-<?=$edit?'warning':'primary'?>" type="submit"><?=$edit?'💾 Update':'➕ Add Record'?></button>
<?php if($edit):?><a href="service_records.php" class="btn btn-danger" style="margin-left:10px">✖ Cancel</a><?php endif;?>
</form>
</div>

<div class="card">
<div class="card-header">📋 All Service Records (Latest First)</div>
<div class="table-wrap"><table>
<thead><tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Service</th><th>Cost</th><th>Mechanic</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
<?php while($row=$records->fetch_assoc()):?>
<tr>
  <td><?=$row['record_id']?></td>
  <td><?=htmlspecialchars($row['customer'])?></td>
  <td><span class="badge"><?=htmlspecialchars($row['vehicle_number'])?></span> <?=htmlspecialchars($row['brand'])?></td>
  <td><?=htmlspecialchars($row['service_name'])?></td>
  <td>Rs.<?=number_format($row['service_cost'],2)?></td>
  <td><?=htmlspecialchars($row['mechanic_name'])?></td>
  <td><?=$row['service_date']?></td>
  <td>
    <a href="?edit=<?=$row['record_id']?>" class="btn btn-warning btn-sm">✏️</a>
    <a href="?delete=<?=$row['record_id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete record?')">🗑</a>
  </td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>
</div></body></html>
