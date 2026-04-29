<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* TOTAL SALES SUMMARY */
// Delivered orders ka total count aur total revenue nikal rahe hain
$totalSales = $conn->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as revenue
    FROM orders
    WHERE status='Delivered'
")->fetch_assoc();

/* ALL DELIVERED ORDERS WITH USER NAME */
// Delivered orders fetch kar rahe hain with user name (JOIN users table)
$sales = $conn->query("
    SELECT orders.*, users.name AS username
    FROM orders
    JOIN users ON orders.user_id = users.id
    WHERE orders.status='Delivered'
    ORDER BY orders.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Report</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#eda7a2;
}

.container{
    width:95%;
    max-width: 1200px;
    margin:auto;
    text-align:center;
    padding-bottom: 50px;
}

h2{
    color:#800000;
    margin-top:25px;
    font-size: 1.8rem;
}

/* ⭐ RESPONSIVE SUMMARY CARDS */
.cards{
    display:flex;
    justify-content:center;
    gap:20px;
    flex-wrap:wrap;
    margin-top:20px;
}

.card{
    background:#ffe6f2;
    padding:25px;
    border-radius:12px;
    width: 250px; /* Thora bara kiya behtar visibility ke liye */
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
    box-sizing: border-box;
}

.card h3{
    color:#800000;
    margin:0;
    font-size: 1.1rem;
}

.card p{
    font-size: 22px;
    font-weight:bold;
    margin-top: 10px;
    color: #333;
}

/* ⭐ TABLE RESPONSIVE WRAPPER */
.table-wrapper {
    width: 100%;
    overflow-x: auto; /* Table scroll karegi choti screen par */
    background: #ffe6f2;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-top: 30px;
}

table{
    width:100%;
    min-width: 600px; /* Data overlap se bachne ke liye */
    border-collapse:collapse;
}

th{
    background:#800000;
    color:white;
    padding:15px 12px;
    text-align: left;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align: left;
}

.status{
    background:#55efc4;
    padding:5px 12px;
    border-radius:8px;
    font-weight:bold;
    font-size: 13px;
    color: #000;
}

/* 📱 MEDIA QUERIES */
@media (max-width: 600px) {
    h2 { font-size: 1.5rem; }
    
    .card {
        width: 100%; /* Mobile par cards full width ho jayenge */
    }

    table {
        font-size: 14px;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Sales Report</h2>

    <div class="cards">
        <div class="card">
            <h3>Total Orders</h3>
            <p><?= $totalSales['total_orders'] ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Total Revenue</h3>
            <p>PKR <?= number_format($totalSales['revenue'] ?? 0); ?></p>
        </div>
    </div>

    <h2 style="margin-top:40px;">Delivered Orders</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- agar orders exist kerty hain -->
                <?php if ($sales->num_rows > 0): ?>
                    <?php while($row = $sales->fetch_assoc()): ?><!-- loop lagayi ha -->
                    <tr>
                        <td>#<?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td>PKR <?= number_format($row['total_amount']); ?></td>
                        <td><span class="status"><?= $row['status']; ?></span></td>
                        <td><?= date("d M Y, h:i A", strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No sales data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>