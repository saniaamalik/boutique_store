<?php
// Session start - User ki jankari save karne ke liye
session_start();
require("../config/db.php");

// SIRF ADMIN USERS KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

// Error aur success messages ke liye variables
$error = "";
$success = "";

/* NAI CATEGORY ADD KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Form se category ka naam uthao aur spaces hatao
    $name = trim($_POST['name']);

    // VALIDATION - Category name empty toh nahi hai?
    if ($name == "") {
        $error = "Category name required!";
    }
    else {

        // CHECK DUPLICATE - Kya yeh category pehlay se exist karta hai?
        $check = $conn->prepare("SELECT id FROM categories WHERE LOWER(name)=LOWER(?)");
        $check->bind_param("s", $name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Agar duplicate hai toh error dikhao
            $error = "Category already exists!";
        }
        else {

            // Database mein naya category add karo
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);

            // Execute karo aur check karo
            if ($stmt->execute()) {
                $success = "Category Added Successfully!";
            } else {
                $error = "Failed to add category!";
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
<title>Add Category</title>

<style>
/* Page ka background - Purple color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;  /* Light purple background */
}

/* Main container - Center mein content rakho */
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
    font-size: 1.8rem;
}

/* Form ka box - Category add karne ke liye */
.form-box{
    width: 90%;  /* Mobile screens par 90% width */
    max-width: 450px;  /* Bari screen par 450px se upar nahi */
    margin: 40px auto;
    background: #f3e8ff;  /* Light purple background */
    padding: 25px;  /* Inner spacing */
    border-radius: 15px;  /* Corners ko round karo */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);  /* Shadow effect */
    box-sizing: border-box;  /* Padding ko width ke andar rakhne ke liye */
}

/* Input field styling - Text likha jaye */
input{
    width:100%;
    box-sizing:border-box;
    padding:12px;  /* Inner spacing */
    margin:10px 0;
    border-radius:10px;  /* Rounded borders */
    border:1px solid #ddd;  /* Light gray border */
    font-size:14px;
    outline: none;  /* Focus pe outline nahi dikhega */
}

/* Submit button */
button{
    width:100%;
    padding:12px;  /* Button ko bada banao */
    background:#6a0dad;  /* Purple button */
    color:white;  /* White text */
    border:none;
    border-radius:10px;  /* Rounded button */
    cursor:pointer;  /* Mouse pointer change ho */
    font-weight: bold;  /* Bold text */
    font-size: 16px;
    transition: 0.3s;  /* Smooth animation on hover */
}

/* Jab mouse pe hoover karo */
button:hover{
    background:#4b0082;  /* Dark purple */
}

/* Mobile screens ke liye responsive design */
@media (max-width: 480px) {
    h2 {  /* Heading ko chhota banao */
        font-size: 1.5rem;
    }
    .form-box {  /* Form box ko adjust karo */
        padding: 20px;
        margin: 20px auto;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <!-- NAI CATEGORY ADD KARNE KA HEADING -->
    <h2>Add New Category</h2>

    <div class="form-box">

        <!-- Error message dikhao agar ho -->
        <?php if($error) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
        <!-- Success message dikhao agar ho -->
        <?php if($success) echo "<p style='color:green; font-size:14px;'>$success</p>"; ?>

        <!-- Category form - POST method se data submit hoga -->
        <form method="POST">

            <!-- Category ka naam input field -->
            <input type="text" name="name" placeholder="Category Name" required>

            <!-- Submit button -->
            <button type="submit">Add Category</button>

        </form>

    </div>

</div>

</body>
</html>