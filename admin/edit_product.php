<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* VALIDATE ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID!");
}

$id = intval($_GET['id']);

/* GET PRODUCT */
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found!");
}

/* CATEGORIES */
$categories = $conn->query("SELECT * FROM categories");

$error = "";
$success = "";

/* UPDATE PRODUCT */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $stock = trim($_POST['stock']); // ✅ NEW

    $newImage = $_FILES['image']['name'];

    /* VALIDATION */
    if ($name == "" || $price == "" || $category == "" || $stock == "") {
        $error = "Required fields missing!";
    }
    elseif (!is_numeric($price) || $price <= 0) {
        $error = "Invalid price!";
    }
    elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Invalid stock quantity!";
    }
    else {

        $path = "../uploads/";

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        /* NEW IMAGE UPLOAD */
        if ($newImage != "") {

            $tmp = $_FILES['image']['tmp_name'];

            // delete old image safely
            if (!empty($product['image']) && file_exists($path . $product['image'])) {
                unlink($path . $product['image']);
            }

            $newImageName = time() . "_" . rand(1000,9999) . "_" . $newImage;

            move_uploaded_file($tmp, $path . $newImageName);

            $update = $conn->prepare("
                UPDATE products 
                SET name=?, price=?, category_id=?, description=?, image=?, stock=?
                WHERE id=?
            ");

            $update->bind_param("sdissii", $name, $price, $category, $description, $newImageName, $stock, $id);

        } else {

            $update = $conn->prepare("
                UPDATE products 
                SET name=?, price=?, category_id=?, description=?, stock=?
                WHERE id=?
            ");

            $update->bind_param("sdisii", $name, $price, $category, $description, $stock, $id);
        }

        $update->execute();
        $success = "Product Updated Successfully!";

        // refresh data
        $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>

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
}

h2{
    color:#6a0dad;
    margin-top:25px;
    font-size:1.8rem;
}

.form-box{
    width:90%;
    max-width:450px;
    margin:30px auto;
    background:#f3e8ff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    box-sizing:border-box;
}

input, textarea, select{
    width:100%;
    box-sizing:border-box;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ddd;
    font-size:14px;
}

textarea{
    height:100px;
    resize:vertical;
}

button{
    width:100%;
    padding:12px;
    background:#6a0dad;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    font-size:16px;
}

button:hover{
    background:#4b0082;
}

img{
    width:120px;
    border-radius:10px;
    margin-top:10px;
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Edit Product</h2>

    <div class="form-box">

        <?php if($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p style="color:green;"><?= $success ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="name" 
                   value="<?= htmlspecialchars($product['name']); ?>" required>

            <input type="number" name="price" 
                   value="<?= htmlspecialchars($product['price']); ?>" required>

            <!-- STOCK FIELD -->
            <input type="number" name="stock" 
                   value="<?= htmlspecialchars($product['stock']); ?>" 
                   min="0" required>

            <select name="category" required>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id']; ?>"
                        <?= ($cat['id'] == $product['category_id']) ? "selected" : "" ?>>
                        <?= htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <textarea name="description"><?= htmlspecialchars($product['description']); ?></textarea>

            <p>Current Image:</p>
            <?php if(!empty($product['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($product['image']); ?>">
            <?php endif; ?>

            <input type="file" name="image" accept="image/*">

            <button type="submit">Update Product</button>

        </form>

    </div>

</div>

</body>
</html>