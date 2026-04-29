<?php
session_start();
require("../config/db.php");

/* ADMIN CHECK */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* UPDATE STATUS */
if (isset($_POST['update_status'])) {
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

/* DELETE ORDER */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* SEARCH + SORT */
$search = $_GET['search'] ?? "";
$sort   = $_GET['sort'] ?? "";

/* ORDER BY */
$orderBy = "ORDER BY o.id DESC";
if ($sort == "low") {
    $orderBy = "ORDER BY o.total_amount ASC";
} elseif ($sort == "high") {
    $orderBy = "ORDER BY o.total_amount DESC";
}

/* BASE QUERY */
$sql = "
SELECT 
    o.id,
    o.total_amount,
    o.status,
    o.created_at,
    u.name AS customer_name
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE u.role = 'customer'
";

if (!empty($search)) {

    $like = "%$search%";

    $sql .= " AND (o.id LIKE ? OR u.name LIKE ?) $orderBy";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $orders = $stmt->get_result();

} else {

    $sql .= " $orderBy";
    $orders = $conn->query($sql);
}

/* CLEAN STATUS CLASS */
function cleanStatus($status){
    return preg_replace('/[^a-zA-Z]/', '', $status);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Orders</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

.container{
    width:95%;
    max-width:1200px;
    margin:auto;
    text-align:center;
    padding-bottom:40px;
}

h2{
    color:#6a0dad;
    margin-top:25px;
}

/* SEARCH */
.search-box{
    margin:20px auto;
    display:flex;
    justify-content:center;
    gap:10px;
    flex-wrap:wrap;
}

.search-box input,
.search-box select{
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
}

.search-box button{
    padding:10px 20px;
    background:#6a0dad;
    color:white;
    border:none;
    border-radius:10px;
}

/* TABLE */
.table-wrapper{
    width:100%;
    overflow-x:auto;
    background:white;
    border-radius:10px;
}

table{
    width:100%;
    min-width:800px;
    border-collapse:collapse;
}

th{
    background:#6a0dad;
    color:white;
    padding:15px;
    text-align:left;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
}

/* STATUS COLORS */
.status{
    padding:5px 10px;
    border-radius:8px;
    font-size:13px;
    display:inline-block;
}

.Pending{background:#ffeaa7;}
.Processing{background:#74b9ff;}
.Shipped{background:#81ecec;}
.Delivered{background:#55efc4;}
.Cancelled{background:#ff7675;}

table select{
    padding:5px;
    border-radius:5px;
}

table button{
    background:#6a0dad;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:8px;
}

.delete{
    color:red;
    font-weight:bold;
    text-decoration:none;
}

.locked{
    color:green;
    font-weight:bold;
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<h2>Manage Orders</h2>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search order or user..." value="<?= htmlspecialchars($search) ?>">

    <select name="sort">
        <option value="">Sort by Price</option>
        <option value="low" <?= $sort=="low"?'selected':'' ?>>Low → High</option>
        <option value="high" <?= $sort=="high"?'selected':'' ?>>High → Low</option>
    </select>

    <button type="submit">Apply</button>
</form>

<div class="table-wrapper">

<table>
<tr>
    <th>Order ID</th>
    <th>Customer Name</th>
    <th>Total</th>
    <th>Status</th>
    <th>Change Status</th>
    <th>Action</th>
</tr>

<?php while($row = $orders->fetch_assoc()): ?>

<tr>
    <td>#<?= $row['id']; ?></td>
    <td><?= htmlspecialchars($row['customer_name']); ?></td>
    <td>Rs <?= number_format($row['total_amount']); ?></td>

    <td>
        <span class="status <?= cleanStatus($row['status']); ?>">
            <?= $row['status']; ?>
        </span>
    </td>

    <!-- ✅ FIXED PART -->
    <td>
        <?php if ($row['status'] != "Delivered" && $row['status'] != "Cancelled"): ?>
        
        <form method="POST" style="display:flex;gap:5px;align-items:center;">
            <input type="hidden" name="order_id" value="<?= $row['id']; ?>">

            <select name="status">
                <option <?= $row['status']=="Pending"?'selected':'' ?>>Pending</option>
                <option <?= $row['status']=="Processing"?'selected':'' ?>>Processing</option>
                <option <?= $row['status']=="Shipped"?'selected':'' ?>>Shipped</option>
                <option <?= $row['status']=="Delivered"?'selected':'' ?>>Delivered</option>
                <option <?= $row['status']=="Cancelled"?'selected':'' ?>>Cancelled</option>
            </select>

            <button name="update_status">Update</button>
        </form>

        <?php else: ?>
            <span class="locked">✔ <?= $row['status']; ?></span>
        <?php endif; ?>
    </td>

    <td>
        <a class="delete" href="?delete=<?= $row['id']; ?>" onclick="return confirm('Delete order?')">Delete</a>
    </td>
</tr>

<?php endwhile; ?>

</table>

</div>

</div>

</body>
</html>