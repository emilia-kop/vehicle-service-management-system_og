<?php
require 'includes/db.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action'];
    $name=$conn->real_escape_string(trim($_POST['service_name']));
    $cost=(float)$_POST['service_cost'];
    if($action==='insert'){
        $conn->query("INSERT INTO Service(service_name,service_cost) VALUES('$name',$cost)")
            ?$msg='<div class="alert alert-success">✅ Service added!</div>'
            :$msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
    } else {
        $id=(int)$_POST['service_id'];
        $conn->query("UPDATE Service SET service_name='$name',service_cost=$cost WHERE service_id=$id")
            ?$msg='<div class="alert alert-success">✅ Service updated!</div>'
            :$msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
    }
}
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    $conn->query("DELETE FROM Service WHERE service_id=$id")
        ?$msg='<div class="alert alert-success">✅ Deleted.</div>'
        :$msg='<div class="alert alert-danger">❌ Cannot delete service with records.</div>';
}
$edit=null;
if(isset($_GET['edit'])){
    $res=$conn->query("SELECT * FROM Service WHERE service_id=".(int)$_GET['edit']);
    $edit=$res->fetch_assoc();
}
$services=$conn->query("SELECT * FROM Service ORDER BY service_cost DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Services</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php';?>
<div class="container">
<div class="page-title"><span class="icon">⚙️</span> Service Catalogue</div>
<?=$msg?>
<div class="card">
<div class="card-header"><?=$edit?'✏️ Edit Service':'➕ Add Service'?></div>
<form method="POST">
<input type="hidden" name="action" value="<?=$edit?'update':'insert'?>">
<?php if($edit):?><input type="hidden" name="service_id" value="<?=$edit['service_id']?>"><?php endif;?>
<div class="form-grid">
  <div class="form-group">
    <label>Service Name</label>
    <input type="text" name="service_name" required placeholder="e.g. Full Car Service" value="<?=htmlspecialchars($edit['service_name']??'')?>">
  </div>
  <div class="form-group">
    <label>Service Cost (Rs)</label>
    <input type="number" name="service_cost" step="0.01" min="0" required placeholder="e.g. 2500" value="<?=$edit['service_cost']??''?>">
  </div>
</div><br>
<button class="btn btn-<?=$edit?'warning':'primary'?>" type="submit"><?=$edit?'💾 Update':'➕ Add Service'?></button>
<?php if($edit):?><a href="services.php" class="btn btn-danger" style="margin-left:10px">✖ Cancel</a><?php endif;?>
</form>
</div>
<div class="card">
<div class="card-header">⚙️ Service Catalogue (Ordered by Cost)</div>
<div class="table-wrap"><table>
<thead><tr><th>ID</th><th>Service Name</th><th>Cost (Rs)</th><th>Actions</th></tr></thead>
<tbody>
<?php while($row=$services->fetch_assoc()):?>
<tr>
  <td><?=$row['service_id']?></td>
  <td><?=htmlspecialchars($row['service_name'])?></td>
  <td><strong>Rs.<?=number_format($row['service_cost'],2)?></strong></td>
  <td>
    <a href="?edit=<?=$row['service_id']?>" class="btn btn-warning btn-sm">✏️ Edit</a>
    <a href="?delete=<?=$row['service_id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">🗑 Delete</a>
  </td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>
</div></body></html>
