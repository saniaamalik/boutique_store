<?php
session_start();
require("../config/db.php");

// 🔐 ONLY ADMIN ACCESS
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$customers = $conn->query("SELECT * FROM users WHERE role='customer'");

$customers_count = $conn->query("
    SELECT COUNT(*) as total 
    FROM users 
    WHERE role='customer'
")->fetch_assoc();

$products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc();
$categories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc();
$orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc();

$sales = $conn->query("
    SELECT SUM(total_amount) as total 
    FROM orders 
    WHERE status='Delivered'
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

/* ✅ FULL WIDTH CLEAN LAYOUT FIX */
.container{
    width:100%;
    max-width:1300px;
    margin:auto;
    padding:10px 2px ;   /* 🔥 side spacing reduced */
    box-sizing:border-box;
}

h2{
    color:#6a0dad;
    font-size:1.6rem;
    margin:15px 0;
}

/* ⭐ CARDS FIX */
.cards{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap:20px;
    margin-top:15px;
}

.card{
    background:white;
    padding:30px 18px;   /* 🔥 bigger cards */
    border-radius:16px;
    text-align:center;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(106,13,173,0.2);
    background:#f7efff;
}

.card h3{
    color:#6a0dad;
    font-size:1.1rem;
    margin-bottom:8px;
}

.card p{
    font-size:26px;
    font-weight:bold;
    margin:0;
}

/* ⭐ TABLE FIX FULL WIDTH LOOK */
.table-responsive{
    width:100%;
    overflow-x:auto;
    margin-top:25px;
    border-radius:14px;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:700px;
    background:white;
    border-radius:14px;
    overflow:hidden;
}

th{
    background:#6a0dad;
    color:white;
    padding:14px;
    text-align:left;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}

/* 📱 MOBILE */
@media(max-width:600px){
    h2{ font-size:1.3rem; }

    .container{
        padding:10px;
    }

    .card p{
        font-size:22px;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Dashboard Overview</h2>

    <div class="cards">
        <div class="card">
            <h3>Customers</h3>
            <p><?= $customers_count['total'] ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Products</h3>
            <p><?= $products['total'] ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Categories</h3>
            <p><?= $categories['total'] ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Orders</h3>
            <p><?= $orders['total'] ?? 0; ?></p>
        </div>

        <div class="card">
            <h3>Revenue</h3>
            <p>Rs <?= number_format($sales['total'] ?? 0); ?></p>
        </div>
    </div>

    <h2 style="margin-top:35px;">Customers Data</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $customers->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['phone']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>s