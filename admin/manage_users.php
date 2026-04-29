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

/* NAI CUSTOMER ADD KARNE KA KAAM */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {

    // Form se data uthao aur spaces hatao
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Check karo ke sab fields bharay huay hain ya nahi
    if ($name == "" || $email == "" || $phone == "" || $password == "") {
        $error = "All fields required!";
    }
    else {
        // Email check karo - kya pehlay se exist karta hai?
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            // Agar email pehlay se hai toh error
            $error = "Email already exists!";
        } else {
            // Password ko secure banao - hashing se
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Database mein naya customer add karo
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, 'customer')
            ");

            $stmt->bind_param("ssss", $name, $email, $hashed, $phone);
            $stmt->execute();

            // Success message dikha do
            $success = "Customer Added!";
        }
    }
}

/* CUSTOMER KO DELETE KARO */
if (isset($_GET['delete'])) {
    // ID le lo aur secure tarikay se delete karo
    //id ko int ma convert kerty hain
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='customer'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* CUSTOMER KO SEARCH KARO */
$search = "";
if (isset($_GET['search']) && $_GET['search'] != "") {
    // Search term le lo
    $search = "%".$_GET['search']."%";
    // Naam, email ya phone se search karo
    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE role='customer' 
        AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)
    ");
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $customers = $stmt->get_result();
} else {
    // Agar search nahi toh sab customers dikha do
    $customers = $conn->query("SELECT * FROM users WHERE role='customer'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>

<style>
/* Page ka background - Purple color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

/* Main container - Poori width mein center mein */
.container{
    width:95%;
    max-width: 1200px;
    margin:auto;
    text-align:center;
    padding-bottom: 50px;
}

/* Headings - Bade likha hua title */
h2{
    color:#6a0dad;  /* Purple color */
    text-align:center;
    margin-top:25px;
    font-size: 1.8rem;
}

/* Form ka box - Customer add karne ke liye */
.form-box{
    width: 90%;
    max-width: 500px;
    margin: 40px auto;
    background: #f3e8ff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    box-sizing: border-box;
}

/* Input fields - Jahan likha jaye data */
input{
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: 1px solid #ddd;
    outline: none;
    box-sizing: border-box;
}

/* Button - Click karne ke liye */
button{
    background: #6a0dad;  /* Purple button */
    color: white;  /* White text */
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    width: 100%;
    transition: 0.3s;
}

/* Jab mouse pe hoover karo */
button:hover{
    background: #4b0082;  /* Dark purple */
}

/* Search ka box - Customers dhundne ke liye */
.search-box{
    margin: 20px auto;
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.search-box input{
    width: 300px;
    max-width: 100%;
    margin: 0;
}

.search-box button {
    width: auto;
    padding: 10px 20px;
}

/* Table ko responsive banao - Mobile mein scroll kare */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Table styling - Data likha ho */
table{
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
}

/* Table ke headings - Upar wali row */
th{
    background: #6a0dad;  /* Purple heading */
    color: white;
    padding: 15px 12px;
    text-align: left;
}

/* Table ke cells - Data */
td{
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

/* Alternate rows ka color - Padhne mein aasan ho */
tr:nth-child(even){
    background: #f3e8ff;
}

/* Edit aur Delete links */
.edit{color: blue; font-weight: bold; text-decoration: none;}  /* Edit ka link */
.delete{color: red; font-weight: bold; text-decoration: none;}  /* Delete ka link */

/* Chhoti screens ke liye - Mobile responsive */
@media (max-width: 600px) {
    h2 { font-size: 1.5rem; }
    
    .search-box {
        flex-direction: column;
        align-items: center;
    }
    
    .search-box input, .search-box button {
        width: 100%;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<!-- NAI CUSTOMER ADD KARNE KA FORM -->
<h2>Add New Customer</h2>

<div class="form-box">
    <!-- Error message dikhao -->
    <?php if($error) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
    <!-- Success message dikhao -->
    <?php if($success) echo "<p style='color:green; font-size:14px;'>$success</p>"; ?>

    <form method="POST">
        <!-- Customer ka naam -->
        <input type="text" name="name" placeholder="Customer Name" required>
        <!-- Customer ka email -->
        <input type="email" name="email" placeholder="Email" required>
        <!-- Customer ka phone number -->
        <input type="text" name="phone" placeholder="Phone" required>
        <!-- Customer ka password -->
        <input type="password" name="password" placeholder="Password" required>

        <!-- Add button -->
        <button name="add">Add Customer</button>
    </form>
</div>

<!-- CUSTOMER KI DETAILED TABLE -->
<h2>Customer Details</h2>

<!-- Search form - Customer dhundne ke liye -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search customer..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button>Search</button>
</form>

<!-- Table ka container -->
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <!-- Table ke headings -->
                <th>ID</th>  <!-- Customer ka ID -->
                <th>Name</th>  <!-- Customer ka naam -->
                <th>Email</th>  <!-- Customer ka email -->
                <th>Phone</th>  <!-- Customer ka phone -->
                <th>Action</th>  <!-- Edit/Delete buttons -->
            </tr>
        </thead>
        <tbody>
            <!-- Database se har customer ki row -->
            <?php while($row = $customers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['phone']); ?></td>
                <td>
                    <!-- Edit link - Customer ko edit karne ke liye -->
                    <a href="edit_user.php?id=<?= $row['id']; ?>" class="edit">Edit</a> |
                    <!-- Delete link - Customer ko delete karne ke liye -->
                    <a href="?delete=<?= $row['id']; ?>" class="delete" onclick="return confirm('Delete user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>

</body>
</html>