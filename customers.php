<?php
require 'includes/db.php';
$msg = '';

// INSERT
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='insert') {
    $name    = $conn->real_escape_string(trim($_POST['name']));
    $phone   = $conn->real_escape_string(trim($_POST['phone']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    if ($conn->query("INSERT INTO Customer(name,phone,address) VALUES('$name','$phone','$address')"))
        $msg = '<div class="alert alert-success">✅ Customer added successfully!</div>';
    else
        $msg = '<div class="alert alert-danger">❌ Error: '.htmlspecialchars($conn->error).'</div>';
}

// UPDATE
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='update') {
    $id      = (int)$_POST['customer_id'];
    $name    = $conn->real_escape_string(trim($_POST['name']));
    $phone   = $conn->real_escape_string(trim($_POST['phone']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    if ($conn->query("UPDATE Customer SET name='$name',phone='$phone',address='$address' WHERE customer_id=$id"))
        $msg = '<div class="alert alert-success">✅ Customer updated successfully!</div>';
    else
        $msg = '<div class="alert alert-danger">❌ Error: '.htmlspecialchars($conn->error).'</div>';
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM Customer WHERE customer_id=$id"))
        $msg = '<div class="alert alert-success">✅ Customer deleted.</div>';
    else
        $msg = '<div class="alert alert-danger">❌ Cannot delete: customer has linked vehicles.</div>';
}

// Fetch for edit
$edit = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $res  = $conn->query("SELECT * FROM Customer WHERE customer_id=$id");
    $edit = $res->fetch_assoc();
}

$customers = $conn->query("SELECT * FROM Customer ORDER BY customer_id DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>VSMS - Customers</title><link rel="stylesheet" href="css/style.css">
</head><body>
<?php require 'includes/nav.php'; ?>
<div class="container">
<div class="page-title"><span class="icon">👤</span> Customer Management</div>
<?=$msg?>

<div class="card">
<div class="card-header"><?=$edit ? '✏️ Edit Customer' : '➕ Add New Customer'?></div>
<form method="POST">
<input type="hidden" name="action" value="<?=$edit ? 'update' : 'insert'?>">
<?php if($edit): ?><input type="hidden" name="customer_id" value="<?=$edit['customer_id']?>"><?php endif; ?>
<div class="form-grid">
  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="name" required placeholder="e.g. Arjun Menon" value="<?=htmlspecialchars($edit['name'] ?? '')?>">
  </div>
  <div class="form-group">
    <label>Phone Number</label>
    <input type="text" name="phone" required placeholder="10-digit number" value="<?=htmlspecialchars($edit['phone'] ?? '')?>">
  </div>
  <div class="form-group full">
    <label>Address</label>
    <input type="text" name="address" placeholder="Full address" value="<?=htmlspecialchars($edit['address'] ?? '')?>">
  </div>
</div>
<br>
<button class="btn btn-<?=$edit?'warning':'primary'?>" type="submit">
  <?=$edit ? '💾 Update Customer' : '➕ Add Customer'?>
</button>
<?php if($edit): ?><a href="customers.php" class="btn btn-danger" style="margin-left:10px">✖ Cancel</a><?php endif; ?>
</form>
</div>

<div class="card">
<div class="card-header">📋 All Customers</div>
<div class="table-wrap"><table>
<thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Address</th><th>Actions</th></tr></thead>
<tbody>
<?php while($row=$customers->fetch_assoc()): ?>
<tr>
  <td><?=$row['customer_id']?></td>
  <td><?=htmlspecialchars($row['name'])?></td>
  <td><?=htmlspecialchars($row['phone'])?></td>
  <td><?=htmlspecialchars($row['address'])?></td>
  <td>
    <a href="?edit=<?=$row['customer_id']?>" class="btn btn-warning btn-sm">✏️ Edit</a>
    <a href="?delete=<?=$row['customer_id']?>" class="btn btn-danger btn-sm"
       onclick="return confirm('Delete this customer?')">🗑 Delete</a>
  </td>
</tr>
<?php endwhile; ?>
</tbody></table></div>
</div>
</div></body></html>
