<?php
session_start();
include "../config/db.php";

/* CHECK LOGIN */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* GET ORDERS */
$stmt = $conn->prepare("
    SELECT id, total_amount, status, payment_method, created_at 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

html, body {
    height: 100%;
    margin: 0;
}

body{
    font-family:Arial;
    background:#e9d5ff;
    margin:0;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

/* NAVBAR WRAPPER */
.navbar-wrapper{
    flex-shrink:0;
}

/* CONTAINER */
.container{
    width:95%;
    max-width:1100px;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    flex:1;
}

/* TITLE */
h2{
    color:#6a0dad;
    text-align:center;
    margin-bottom:20px;
}

/* GRID */
.orders-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:20px;
}

/* ORDER CARD */
.order-box{
    background:#fff;
    border:1px solid #eee;
    border-radius:12px;
    padding:15px;
    transition:0.3s;
}

.order-box:hover{
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* HEADER */
.order-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
}

.order-id{
    font-weight:bold;
    color:#333;
}

/* STATUS */
.status{
    padding:5px 10px;
    border-radius:6px;
    color:white;
    font-size:12px;
}

/* STATUS COLORS */
.Pending{ background:orange; }
.Processing{ background:#007bff; }
.Shipped{ background:purple; }
.Delivered{ background:green; }
.Cancelled{ background:red; }

/* DETAILS */
.details p{
    margin:5px 0;
    font-size:14px;
}

/* TOTAL */
.total{
    margin-top:10px;
    font-weight:bold;
    color:#6a0dad;
    font-size:15px;
}

/* EMPTY */
.empty{
    text-align:center;
    padding:30px;
    color:#666;
}

/* FOOTER WRAPPER */
.footer-wrapper{
    flex-shrink:0;
    margin-top:auto;
}

/* RESPONSIVE */
@media (max-width:768px){
    .container{
        padding:15px;
    }
}

@media (max-width:480px){
    .order-header{
        flex-direction:column;
        align-items:flex-start;
        gap:6px;
    }
}

</style>
</head>

<body>

<div class="navbar-wrapper">
<?php include "navbar.php"; ?>
</div>

<div class="container">

    <h2>📦 My Orders</h2>

    <?php if ($orders->num_rows > 0): ?>

        <div class="orders-grid">

        <?php while($order = $orders->fetch_assoc()): ?>

        <div class="order-box">

            <div class="order-header">
                <div class="order-id">Order #<?= $order['id'] ?></div>

                <div class="status <?= $order['status'] ?>">
                    <?= $order['status'] ?>
                </div>
            </div>

            <div class="details">
                <p><b>Payment:</b> <?= $order['payment_method'] ?></p>
                <p><b>Date:</b> <?= $order['created_at'] ?></p>
            </div>

            <div class="total">
                Total: Rs <?= $order['total_amount'] ?>
            </div>

        </div>

        <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty">📭 No orders found</div>

    <?php endif; ?>

</div>

<div class="footer-wrapper">
<?php include "footer.php"; ?>
</div>

</body>
</html>