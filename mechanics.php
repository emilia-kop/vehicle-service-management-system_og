<?php
require 'includes/db.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action'];
    $name=$conn->real_escape_string(trim($_POST['mechanic_name']));
    $phone=$conn->real_escape_string(trim($_POST['phone']));
    $spec=$conn->real_escape_string(trim($_POST['specialization']));
    if($action==='insert'){
        $conn->query("INSERT INTO Mechanic(mechanic_name,phone,specialization) VALUES('$name','$phone','$spec')")
            ?$msg='<div class="alert alert-success">✅ Mechanic added!</div>'
            :$msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
    } else {
        $id=(int)$_POST['mechanic_id'];
        $conn->query("UPDATE Mechanic SET mechanic_name='$name',phone='$phone',specialization='$spec' WHERE mechanic_id=$id")
            ?$msg='<div class="alert alert-success">✅ Mechanic updated!</div>'
            :$msg='<div class="alert alert-danger">❌ '.$conn->error.'</div>';
    }
}
if(isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    $conn->query("DELETE FROM Mechanic WHERE mechanic_id=$id")
        ?$msg='<div class="alert alert-success">✅ Deleted.</div>'
        :$msg='<div class="alert alert-danger">❌ Cannot delete mechanic with service records.</div>';
}
$edit=null;
if(isset($_GET['edit'])){
    $res=$conn->query("SELECT * FROM Mechanic WHERE mechanic_id=".(int)$_GET['edit']);
    $edit=$res->fetch_assoc();
}
$mechanics=$conn->query("SELECT * FROM Mechanic ORDER BY mechanic_id DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Mechanics</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php';?>
<div class="container">
<div class="page-title"><span class="icon">🛠</span> Mechanic Management</div>
<?=$msg?>
<div class="card">
<div class="card-header"><?=$edit?'✏️ Edit Mechanic':'➕ Add Mechanic'?></div>
<form method="POST">
<input type="hidden" name="action" value="<?=$edit?'update':'insert'?>">
<?php if($edit):?><input type="hidden" name="mechanic_id" value="<?=$edit['mechanic_id']?>"><?php endif;?>
<div class="form-grid">
  <div class="form-group">
    <label>Mechanic Name</label>
    <input type="text" name="mechanic_name" required placeholder="Full Name" value="<?=htmlspecialchars($edit['mechanic_name']??'')?>">
  </div>
  <div class="form-group">
    <label>Phone</label>
    <input type="text" name="phone" required placeholder="10-digit number" value="<?=htmlspecialchars($edit['phone']??'')?>">
  </div>
  <div class="form-group full">
    <label>Specialization</label>
    <input type="text" name="specialization" placeholder="e.g. Engine Repair, AC Service" value="<?=htmlspecialchars($edit['specialization']??'')?>">
  </div>
</div><br>
<button class="btn btn-<?=$edit?'warning':'primary'?>" type="submit"><?=$edit?'💾 Update':'➕ Add Mechanic'?></button>
<?php if($edit):?><a href="mechanics.php" class="btn btn-danger" style="margin-left:10px">✖ Cancel</a><?php endif;?>
</form>
</div>
<div class="card">
<div class="card-header">🛠 All Mechanics</div>
<div class="table-wrap"><table>
<thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Specialization</th><th>Actions</th></tr></thead>
<tbody>
<?php while($row=$mechanics->fetch_assoc()):?>
<tr>
  <td><?=$row['mechanic_id']?></td>
  <td><?=htmlspecialchars($row['mechanic_name'])?></td>
  <td><?=htmlspecialchars($row['phone'])?></td>
  <td><span class="badge"><?=htmlspecialchars($row['specialization'])?></span></td>
  <td>
    <a href="?edit=<?=$row['mechanic_id']?>" class="btn btn-warning btn-sm">✏️ Edit</a>
    <a href="?delete=<?=$row['mechanic_id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">🗑 Delete</a>
  </td>
</tr>
<?php endwhile;?>
</tbody></table></div>
</div>
</div></body></html>
