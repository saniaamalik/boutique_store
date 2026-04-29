<?php
// Session start - User ki jankari save karne ke liye
session_start();
require("../config/db.php");

// SIRF ADMIN USERS KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* PRODUCT ID KO VALIDATE KARO */
// Check karo ke URL me id exist karti hai aur number hai
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID!");
}

// ID ko integer mein convert karo
$id = intval($_GET['id']);

/* DATABASE SE PRODUCT KI data LE LO */
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Agar product nahi mila toh error
if (!$product) {
    die("Product not found!");
}

/* SABB CATEGORIES DATABASE SE LE LO */
$categories = $conn->query("SELECT * FROM categories");

// Error aur success messages ke liye variables
$error = "";
$success = "";

/* PRODUCT KO UPDATE KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Form se data le lo aur spaces hatao
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $stock = trim($_POST['stock']); // Stock quantity

    // Naya image ka naam agar upload hua ho
    $newImage = $_FILES['image']['name'];

    /* VALIDATION - Check karo sab fields bharay huay hain ya nahi */
    if ($name == "" || $price == "" || $category == "" || $stock == "") {
        $error = "Required fields missing!";
    }
    // Price ka validation
    elseif (!is_numeric($price) || $price <= 0) {
        $error = "Invalid price!";
    }
    // Stock ka validation
    elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Invalid stock quantity!";
    }
    else {

        $path = "../uploads/";

        // Uploads folder banao agar exist nahi karta
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        /* NAI IMAGE UPLOAD KARNE KA KAAM */
        if ($newImage != "") {

            $tmp = $_FILES['image']['tmp_name'];

            // Puraani image ko safely delete karo
            if (!empty($product['image']) && file_exists($path . $product['image'])) {
                unlink($path . $product['image']);
            }

            // Unique name image ka
            $newImageName = time() . "_" . rand(1000,9999) . "_" . $newImage;

            // Image ko upload folder mein move karo
            move_uploaded_file($tmp, $path . $newImageName);

            // Database update karo - Image ke saath
            $update = $conn->prepare("
                UPDATE products 
                SET name=?, price=?, category_id=?, description=?, image=?, stock=?
                WHERE id=?
            ");

            $update->bind_param("sdissii", $name, $price, $category, $description, $newImageName, $stock, $id);

        } else {

            // Database update karo - Image ke bina (sirf product data)
            $update = $conn->prepare("
                UPDATE products 
                SET name=?, price=?, category_id=?, description=?, stock=?
                WHERE id=?
            ");

            $update->bind_param("sdisii", $name, $price, $category, $description, $stock, $id);
        }

        // Query execute karo
        $update->execute();
        $success = "Product Updated Successfully!";

        // Updated product data ko dobara le lo
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
/* Page ka background - Purple color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;  /* Light purple */
}

/* Main container - Center mein content */
.container{
    width:95%;
    max-width:1200px;
    margin:auto;
    text-align:center;
}

/* Heading - Bada title */
h2{
    color:#6a0dad;  /* Purple color */
    margin-top:25px;
    font-size:1.8rem;
}

/* Form ka box - Product edit karne ke liye */
.form-box{
    width:90%;  /* Mobile screens par */
    max-width:450px;  /* Desktop par */
    margin:30px auto;
    background:#f3e8ff;  /* Light purple background */
    padding:25px;  /* Inner spacing */
    border-radius:15px;  /* Round corners */
    box-shadow:0 4px 15px rgba(0,0,0,0.1);  /* Shadow effect */
    box-sizing:border-box;  /* Padding ko width ke andar rakhne ke liye */
}

/* Input, textarea, select fields styling */
input, textarea, select{
    width:100%;  /* Puri width */
    box-sizing:border-box;
    padding:12px;  /* Inner spacing */
    margin:10px 0;  /* Outer spacing */
    border-radius:10px;  /* Round corners */
    border:1px solid #ddd;  /* Light border */
    font-size:14px;
}

/* Textarea ko bada banao */
textarea{
    height:100px;
    resize:vertical;  /* Sirf vertical resize */
}

/* Submit button */
button{
    width:100%;
    padding:12px;  /* Button ko bada banao */
    background:#6a0dad;  /* Purple button */
    color:white;  /* White text */
    border:none;
    border-radius:10px;  /* Round button */
    cursor:pointer;  /* Cursor change */
    font-weight:bold;  /* Bold text */
    font-size:16px;
}

/* Jab mouse pe hoover karo */
button:hover{
    background:#4b0082;  /* Dark purple */
}

/* Current image preview */
img{
    width:120px;  /* Image size */
    border-radius:10px;  /* Round corners */
    margin-top:10px;  /* Top spacing */
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <!-- Product edit karne ka heading -->
    <h2>Edit Product</h2>

    <div class="form-box">

        <!-- Error message dikhao agar ho -->
        <?php if($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <!-- Success message dikhao agar ho -->
        <?php if($success): ?>
            <p style="color:green;"><?= $success ?></p>
        <?php endif; ?>

        <!-- Product update form -->
        <form method="POST" enctype="multipart/form-data">

            <!-- Product ka naam -->
            <input type="text" name="name" 
                   value="<?= htmlspecialchars($product['name']); ?>" required>

            <!-- Product ki price -->
            <input type="number" name="price" 
                   value="<?= htmlspecialchars($product['price']); ?>" required>

            <!-- Stock quantity - Kitna product available hai -->
            <input type="number" name="stock" 
                   value="<?= htmlspecialchars($product['stock']); ?>" 
                   min="0" required>

            <!-- Category select dropdown -->
            <select name="category" required>
                <!-- Database se sab categories dikha do -->
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <!-- Agar yeh product ka category hai toh selected mark karo -->
                    <option value="<?= $cat['id']; ?>"
                        <?= ($cat['id'] == $product['category_id']) ? "selected" : "" ?>>
                        <?= htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Product ki description - Poori jankari -->
            <textarea name="description"><?= htmlspecialchars($product['description']); ?></textarea>

            <!-- Current image preview -->
            <p>Current Image:</p>
            <?php if(!empty($product['image'])): ?>
                <!-- Pehly se upload ki hoi image -->
                <img src="../uploads/<?= htmlspecialchars($product['image']); ?>">
            <?php endif; ?>

            <!-- Nai image upload karne ke liye -->
            <input type="file" name="image" accept="image/*">

            <!-- Update button -->
            <button type="submit">Update Product</button>

        </form>

    </div>

</div>

</body>
</html>