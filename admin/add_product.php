<?php
// Session start - User ki jankari save karne ke liye
session_start();
require("../config/db.php");

// SIRF ADMIN KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

// Error aur success messages ke liye variables
$error = "";
$success = "";

/* SABB CATEGORIES DATABASE SE LE LO */
$categories = $conn->query("SELECT * FROM categories");

/* NAI PRODUCT ADD KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Form se data uthao aur spaces hatao
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $stock = trim($_POST['stock']); // Stock quantity

    // Image ka naam aur temp location
    $image = $_FILES['image']['name'] ?? "";
    $tmp = $_FILES['image']['tmp_name'] ?? "";

    // Uploads folder ka path
    $folder = "../uploads/";

    // Agar folder exist nahi karta toh banao
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    // CHECK KARO - SAB FIELDS BHARAY HUAY HAIN YA NAHI
    if ($name == "" || $price == "" || $category == "" || $image == "" || $stock == "") {
        $error = "Please fill all required fields!";
    }
    // Price ka validation - number hona chahiye aur positive
    elseif (!is_numeric($price) || $price <= 0) {
        $error = "Invalid price!";
    }
    // Stock ka validation - number hona chahiye aur zero ya upar
    elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Invalid stock quantity!";
    }
    else {

        // IMAGE SECURITY CHECK - Sirf allowed formats accept karo
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        //path extension nikalta ha or strtolower se small letter ma kerta ha
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
          // Check karo ke file allowed list me hai ya nahi
        if (!in_array($ext, $allowed)) {
            $error = "Only JPG, JPEG, PNG, WEBP allowed!";
        }
        else {

            // Unique name image ka - Time aur random number ka combination

        // ===============================
        // UNIQUE IMAGE NAME CREATE
        // ===============================
        // time() = current timestamp
        // rand() = random number
        // dono mila ke unique filename ban raha hai
            $newImageName = time() . "_" . rand(1000,9999) . "." . $ext;
            $imagePath = $folder . $newImageName;

            // Image ko upload karo
            if (move_uploaded_file($tmp, $imagePath)) {

                // Database mein naya product add karo - Stock ke saath
                $stmt = $conn->prepare("
                    INSERT INTO products (name, price, category_id, description, image, stock)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param("sdissi", $name, $price, $category, $description, $newImageName, $stock);

                // Execute karo aur check karo
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
/* Page ka background - Purple color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

/* Main container - Center mein content */
.container{
    width:95%;
    max-width:1200px;
    margin:auto;
    text-align:center;
}

/* Heading - Bade title */
h2{
    color:#6a0dad;  /* Purple color */
    margin-top:25px;
    font-size:1.8rem;
}

/* Form ka box - Product add karne ke liye */
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

/* Input, textarea, select fields styling */
input, textarea, select{
    width:100%;
    box-sizing:border-box;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ddd;
    font-size:14px;
}

/* Textarea ko bada banao */
textarea{
    height:100px;
    resize:vertical;
}

/* Submit button */
button{
    width:100%;
    padding:12px;
    background:#6a0dad;  /* Purple button */
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    font-size:16px;
}

/* Jab mouse pe hoover karo */
button:hover{
    background:#4b0082;  /* Dark purple */
}

/* Mobile screens ke liye */
@media (max-width:480px){
    h2{font-size:1.5rem;}
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <!-- NAI PRODUCT ADD KARNE KA HEADING -->
    <h2>Add New Product</h2>

    <div class="form-box">

        <!-- Error message dikhao -->
        <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
        <!-- Success message dikhao -->
        <?php if($success) echo "<p style='color:green'>$success</p>"; ?>

        <!-- Product form - POST method se data bhejenge -->
        <form method="POST" enctype="multipart/form-data">

            <!-- Product ka naam -->
            <input type="text" name="name" placeholder="Product Name" required>

            <!-- Product ki price step decimal ma ker raha ha --> 
            <input type="number" name="price" placeholder="Price" step="0.01" required>

            <!-- Stock quantity - Kitna product available hai -->
            <input type="number" name="stock" placeholder="Stock Quantity" min="0" required>

            <!-- Category select dropdown -->
            <select name="category" required>
                <option value="">Select Category</option>
                <!-- Database se sab categories dikha do -->
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <!--id se value store kero-->
                    <option value="<?= $cat['id']; ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Product ki description - Poori jankari -->
            <textarea name="description" placeholder="Product Description"></textarea>

            <!-- Product ki image -->
            <input type="file" name="image" accept="image/*" required>

            <!-- Submit button -->
            <button type="submit">Add Product</button>

        </form>

    </div>

</div>

</body>
</html>