<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

/* CATEGORIES */
$categories = $conn->query("SELECT * FROM categories");

/* ADD PRODUCT */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $stock = trim($_POST['stock']); // ✅ NEW STOCK FIELD

    $image = $_FILES['image']['name'] ?? "";
    $tmp = $_FILES['image']['tmp_name'] ?? "";

    $folder = "../uploads/";

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    // 🔴 VALIDATION
    if ($name == "" || $price == "" || $category == "" || $image == "" || $stock == "") {
        $error = "Please fill all required fields!";
    }
    elseif (!is_numeric($price) || $price <= 0) {
        $error = "Invalid price!";
    }
    elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Invalid stock quantity!";
    }
    else {

        // 🔵 IMAGE SECURITY CHECK
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Only JPG, JPEG, PNG, WEBP allowed!";
        }
        else {

            // unique image name
            $newImageName = time() . "_" . rand(1000,9999) . "." . $ext;
            $imagePath = $folder . $newImageName;

            if (move_uploaded_file($tmp, $imagePath)) {

                // ✅ INSERT WITH STOCK
                $stmt = $conn->prepare("
                    INSERT INTO products (name, price, category_id, description, image, stock)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param("sdissi", $name, $price, $category, $description, $newImageName, $stock);

                if ($stmt->execute()) {
                    $success = "Product Added Successfully!";
                } else {
                    $error = "Database insert failed!";
                }

            } else {
                $error = "Image upload failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product</title>

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

@media (max-width:480px){
    h2{font-size:1.5rem;}
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Add New Product</h2>

    <div class="form-box">

        <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
        <?php if($success) echo "<p style='color:green'>$success</p>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="name" placeholder="Product Name" required>

            <input type="number" name="price" placeholder="Price" step="0.01" required>

            <!-- STOCK FIELD -->
            <input type="number" name="stock" placeholder="Stock Quantity" min="0" required>

            <select name="category" required>
                <option value="">Select Category</option>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id']; ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <textarea name="description" placeholder="Product Description"></textarea>

            <input type="file" name="image" accept="image/*" required>

            <button type="submit">Add Product</button>

        </form>

    </div>

</div>

</body>
</html>