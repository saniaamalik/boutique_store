<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= ADD TO CART ================= */
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {

    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity < 1) $quantity = 1;

    $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {

        $row = $res->fetch_assoc();
        $newQty = $row['quantity'] + $quantity;

        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $update->bind_param("ii", $newQty, $row['id']);
        $update->execute();

    } else {

        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $product_id, $quantity);
        $insert->execute();
    }

    header("Location: cart.php");
    exit();
}

/* ================= REMOVE ================= */
if (isset($_GET['remove'])) {

    $id = intval($_GET['remove']);

    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();

    header("Location: cart.php");
    exit();
}

/* ================= UPDATE CART ================= */
if (isset($_POST['update'])) {

    if (isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $cart_id => $qty) {

            $cart_id = intval($cart_id);
            $qty = intval($qty);
            if ($qty < 1) $qty = 1;

            $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
            $stmt->bind_param("iii", $qty, $cart_id, $user_id);
            $stmt->execute();
        }
    }

    header("Location: cart.php");
    exit();
}

/* ================= PLACE ORDER + STOCK UPDATE ================= */
if (isset($_POST['place_order'])) {

    $stmt = $conn->prepare("
        SELECT c.product_id, c.quantity, p.price, p.stock
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id=?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {

        $total = 0;

        while ($row = $res->fetch_assoc()) {

            if ($row['stock'] < $row['quantity']) {
                die("❌ Not enough stock for product ID: " . $row['product_id']);
            }

            $total += $row['price'] * $row['quantity'];
        }

        // ORDER CREATE
        $order = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, status)
            VALUES (?, ?, 'Pending')
        ");
        $order->bind_param("id", $user_id, $total);
        $order->execute();

        $order_id = $conn->insert_id;

        $res->data_seek(0);

        while ($row = $res->fetch_assoc()) {

            // ORDER ITEMS
            $item = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $item->bind_param("iiid", $order_id, $row['product_id'], $row['quantity'], $row['price']);
            $item->execute();

            // ⭐ STOCK UPDATE
            $stock = $conn->prepare("
                UPDATE products 
                SET stock = stock - ? 
                WHERE id = ?
            ");
            $stock->bind_param("ii", $row['quantity'], $row['product_id']);
            $stock->execute();
        }

        // CLEAR CART
        $clear = $conn->prepare("DELETE FROM cart WHERE user_id=?");
        $clear->bind_param("i", $user_id);
        $clear->execute();
    }

    header("Location: cart.php");
    exit();
}

/* ================= GET CART ================= */
$sql = "
SELECT c.id AS cart_id, c.quantity, p.name, p.price, p.image
FROM cart c
JOIN products p ON c.product_id = p.id
WHERE c.user_id=?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
    background:#eda7a2;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

.navbar-wrapper{flex-shrink:0;}

.container{
    width:95%;
    max-width:1100px;
    margin:40px auto;
    background:#ffe6f2;
    padding:20px;
    border-radius:12px;
    flex:1;
}

h2{
    text-align:center;
    color:#800000;
}

.table-wrap{overflow-x:auto;}

table{
    width:100%;
    min-width:700px;
    border-collapse:collapse;
}

th{
    background:#800000;
    color:white;
    padding:12px;
}

td{
    text-align:center;
    padding:12px;
    border-bottom:1px solid #eee;
}

img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
}

input{
    width:60px;
    padding:5px;
    text-align:center;
}

button{
    background:#800000;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:8px;
    cursor:pointer;
}

button[name="place_order"]{background:green;}

a{
    color:red;
    font-weight:bold;
    text-decoration:none;
}

.total{
    text-align:right;
    font-size:18px;
    margin-top:15px;
    font-weight:bold;
    color:#800000;
}

.footer-wrapper{
    flex-shrink:0;
    margin-top:auto;
}
</style>
</head>

<body>

<div class="navbar-wrapper">
<?php include "navbar.php"; ?>
</div>

<div class="container">

<h2>🛒 My Cart</h2>

<form method="POST">

<div class="table-wrap">
<table>

<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Total</th>
    <th>Action</th>
</tr>

<?php
$grand = 0;

if ($result->num_rows > 0):
while($row = $result->fetch_assoc()):

$total = $row['price'] * $row['quantity'];
$grand += $total;
?>

<tr>
    <td><img src="../uploads/<?= $row['image'] ?>"></td>
    <td><?= $row['name'] ?></td>
    <td>Rs <?= $row['price'] ?></td>
    <td>
        <input type="number" name="qty[<?= $row['cart_id'] ?>]" value="<?= $row['quantity'] ?>" min="1">
    </td>
    <td>Rs <?= $total ?></td>
    <td><a href="cart.php?remove=<?= $row['cart_id'] ?>">Remove</a></td>
</tr>

<?php endwhile; else: ?>
<tr><td colspan="6">Cart Empty</td></tr>
<?php endif; ?>

</table>
</div>

<div class="total">
    Grand Total: Rs <?= $grand ?>
</div>

<br>

<button name="update">Update Cart</button>
<button name="place_order">Place Order</button>

</form>

</div>

<div class="footer-wrapper">
<?php include "footer.php"; ?>
</div>

</body>
</html>