<?php
// Session shuru karo - User ki jankari rakhne ke liye
session_start();
require("../config/db.php");

// 🔐 SIRF ADMIN USERS KO ACCESS - Dusre users ko login page par bhej do
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

// Database se sab customers ka data lao
$customers = $conn->query("SELECT * FROM users WHERE role='customer'");

// Customers ki total tadadad nikalo
$customers_count = $conn->query("
    SELECT COUNT(*) as total 
    FROM users 
    WHERE role='customer'
")->fetch_assoc();

// Sab products ki tadadad
$products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc();
// Sab categories ki tadadad
$categories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc();
// Sab orders ki tadadad
$orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc();

// Delivered orders se total revenue nikalo
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
/* Body styling - Poora page */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#eda7a2;  /* Light pink background */
}

/* Main container - Poori width mein content */
.container{
    width:100%;
    max-width:1300px;
    margin:auto;
    padding:10px 2px ;   /* 🔥 side spacing reduced */
    box-sizing:border-box;
}

/* Headings - Bade heading */
h2{
    color:#800000;  /* Maroon color */
    font-size:1.6rem;
    margin:15px 0;
}

/* Cards grid - Statistics ke boxes */
.cards{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap:20px;
    margin-top:15px;
}

/* Aik card box - Statistics dikhaane wala box */
.card{
    background:white;
    padding:30px 18px;
    border-radius:16px;
    text-align:center;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition:0.3s;  /* Smooth animation */
}

/* Jab mouse pe hoover karo toh effect */
.card:hover{
    transform:translateY(-5px);  /* Thora upar aao */
    box-shadow:0 10px 25px rgba(128,0,0,0.2);  /* Shadow bade */
    background:#fff0f5;  /* Light pink background */
}

/* Card heading - Card ke liye title */
.card h3{
    color:#800000;
    font-size:1.1rem;
    margin-bottom:8px;
}

/* Card main number - Badi figure (tadadad) */
.card p{
    font-size:26px;
    font-weight:bold;  /* Thora heavy likho */
    margin:0;
}

/* Table ko responsive banao - Chhoti screen mein bhi chalega */
.table-responsive{
    width:100%;
    overflow-x:auto;
    margin-top:25px;
    border-radius:14px;
}

/* Table styling - Database ka data show karne ke liye */
table{
    width:100%;
    border-collapse:collapse;  /* Borders ko milao */
    min-width:700px;
    background:white;
    border-radius:14px;  /* Corners ko round karo */
    overflow:hidden;
}

/* Table ke headings - Upar wali row */
th{
    background:#800000;  /* Maroon heading */
    color:white;  /* White text */
    padding:14px;
    text-align:left;
}

/* Table ke cells - Data likha hota hai */
td{
    padding:12px;  /* Andar se space */
    border-bottom:1px solid #eee;  /* Neeche line */
    font-size:14px;
}

/* Mobile phones ke liye - Chhoti screens */
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

    <!-- Dashboard ki heading -->
    <h2>Dashboard Overview</h2>

    <!-- Statistics ke cards - Poora data ek nazar mein -->
    <div class="cards">
        <!-- Customers ka card -->
        <div class="card">
            <h3>Customers</h3>
            <!-- agar koi customer ni ha to 0 show kery -->
            <p><?= $customers_count['total'] ?? 0; ?></p>
        </div>

        <!-- Products ka card -->
        <div class="card">
            <h3>Products</h3>
            <p><?= $products['total'] ?? 0; ?></p>
        </div>

        <!-- Categories ka card -->
        <div class="card">
            <h3>Categories</h3>
            <p><?= $categories['total'] ?? 0; ?></p>
        </div>

        <!-- Orders ka card -->
        <div class="card">
            <h3>Orders</h3>
            <p><?= $orders['total'] ?? 0; ?></p>
        </div>

        <!-- Revenue (kamai) ka card -->
        <div class="card">
            <h3>Revenue</h3>
            <!-- paisa ko readable format mein kern -->
            <p>Rs <?= number_format($sales['total'] ?? 0); ?></p>
        </div>
    </div>

    <!-- Customers ki detailed table - Sab customer ki jankari -->
    <h2 style="margin-top:35px;">Customers Data</h2>

    <div class="table-responsive">
        <table>
            <!-- Table ke headings -->
            <thead>
                <tr>
                    <th>ID</th>  <!-- Customer ka ID -->
                    <th>Name</th>  <!-- Customer ka naam -->
                    <th>Email</th>  <!-- Customer ka email -->
                    <th>Phone</th>  <!-- Customer ka phone -->
                </tr>
            </thead>
            <!-- Table ka data - Database se -->
            <tbody>
                <!-- loop laga k her customer ka data filter ker rahy hain -->
                <?php while($row = $customers->fetch_assoc()): ?>
                <!-- Aik aik customer ki row -->
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