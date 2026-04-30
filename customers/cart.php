<?php
session_start();
include "../config/db.php";

// User login hai to ID, warna session_id (temporary)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : session_id();

/* ================= ADD TO CART ================= */
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity < 1) $quantity = 1;

    $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?");
    $check->bind_param("si", $user_id, $product_id);
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
        $insert->bind_param("sii", $user_id, $product_id, $quantity);
        $insert->execute();
    }
    header("Location: cart.php");
    exit();
}

/* ================= REMOVE & UPDATE ================= */
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("is", $id, $user_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

if (isset($_POST['update'])) {
    if (isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $cart_id => $qty) {
            $cart_id = intval($cart_id);
            $qty = intval($qty);
            if ($qty < 1) $qty = 1;
            $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
            $stmt->bind_param("iis", $qty, $cart_id, $user_id);
            $stmt->execute();
        }
    }
    header("Location: cart.php");
    exit();
}

/* ================= PLACE ORDER ================= */
$showLoginPopup = false;
if (isset($_POST['place_order'])) {
    if (!isset($_SESSION['user_id'])) {
        $showLoginPopup = true;
    } else {
        $db_user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id=?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $total = 0;
            $items = [];
            while ($row = $res->fetch_assoc()) {
                if ($row['stock'] < $row['quantity']) {
                    die("❌ Not enough stock for product ID: " . $row['product_id']);
                }
                $total += $row['price'] * $row['quantity'];
                $items[] = $row;
            }

            $order = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Pending')");
            $order->bind_param("id", $db_user_id, $total);
            $order->execute();
            $order_id = $conn->insert_id;

            foreach ($items as $item) {
                $order_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $order_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $order_item->execute();

                $stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stock->bind_param("ii", $item['quantity'], $item['product_id']);
                $stock->execute();
            }

            $clear = $conn->prepare("DELETE FROM cart WHERE user_id=?");
            $clear->bind_param("s", $user_id);
            $clear->execute();
            
            echo "<script>alert('Order Placed Successfully!'); window.location.href='cart.php';</script>";
            exit();
        }
    }
}

/* ================= GET CART ================= */
$sql = "SELECT c.id AS cart_id, c.quantity, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart</title>
<style>
    /* Global Reset */
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body { 
        font-family: Arial, sans-serif; 
        background: #eda7a2; 
        min-height: 100vh; 
        display: flex; 
        flex-direction: column; 
    }

    /* Navbar Container Adjustment */
    header { width: 100%; flex-shrink: 0; }

    .container { 
        width: 95%; 
        max-width: 1100px; 
        margin: 20px auto; 
        background: #ffe6f2; 
        padding: 25px; 
        border-radius: 12px; 
        flex-grow: 1; /* Page content fills space */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 { text-align: center; color: #800000; margin-bottom: 20px; }

    /* Table Styling */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 600px; background: white; border-radius: 8px; overflow: hidden; }
    th { background: #800000; color: white; padding: 15px; text-transform: uppercase; font-size: 14px; }
    td { text-align: center; padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
    
    input[type="number"] { width: 60px; padding: 8px; text-align: center; border: 1px solid #ccc; border-radius: 5px; }

    .total { text-align: right; font-size: 20px; margin-top: 20px; font-weight: bold; color: #800000; padding-right: 10px; }

    /* Buttons */
    .btn-group { margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end; }
    button { padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; color: white; transition: 0.3s; }
    button[name="update"] { background: #800000; }
    button[name="update"]:hover { background: #a00000; }
    button[name="place_order"] { background: #28a745; }
    button[name="place_order"]:hover { background: #218838; }

    /* Login Popup */
    #loginPopup {
        display: <?= $showLoginPopup ? 'flex' : 'none' ?>;
        position: fixed; inset: 0; background: rgba(0,0,0,0.7);
        justify-content: center; align-items: center; z-index: 1000;
    }
    .popup-content { background: white; padding: 40px; border-radius: 15px; text-align: center; max-width: 400px; width: 90%; }
    .popup-content h3 { color: #800000; margin-bottom: 10px; }
    .popup-btn { background: #800000; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; display: inline-block; margin-top: 20px; font-weight: bold; }
    .cancel-btn { background: #ccc; color: #333; margin-left: 10px; padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; }

    footer { flex-shrink: 0; }
</style>
</head>
<body>

<header>
    <?php include "navbar.php"; ?>
</header>

<div id="loginPopup">
    <div class="popup-content">
        <h3>🔐 Login Required</h3>
        <p>please login to place an order</p>
        <a href="../login.php" class="popup-btn">Login Now</a>
        <button class="cancel-btn" onclick="document.getElementById('loginPopup').style.display='none'">Cancel</button>
    </div>
</div>

<main class="container">
    <h2>🛒 My Shopping Cart</h2>
    
    <form method="POST">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand = 0;
                    if ($result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $total = $row['price'] * $row['quantity'];
                            $grand += $total;
                    ?>
                    <tr>
                        <td><img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="product"></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td>Rs <?= number_format($row['price']) ?></td>
                        <td>
                            <input type="number" name="qty[<?= $row['cart_id'] ?>]" value="<?= $row['quantity'] ?>" min="1">
                        </td>
                        <td>Rs <?= number_format($total) ?></td>
                        <td>
                            <a href="cart.php?remove=<?= $row['cart_id'] ?>" style="color: #d9534f; text-decoration: none; font-weight: bold;">Remove</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6" style="padding: 40px; color: #666;">card empty.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($grand > 0): ?>
            <div class="total">Grand Total: Rs <?= number_format($grand) ?></div>
            <div class="btn-group">
                <button type="submit" name="update">🔄 Update Cart</button>
                <button type="submit" name="place_order">✅ Place Order</button>
            </div>
        <?php endif; ?>
    </form>
</main>

<footer>
    <?php include "footer.php"; ?>
</footer>

</body>
</html>