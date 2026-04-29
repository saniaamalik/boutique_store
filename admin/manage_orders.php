<?php
// Session start 
session_start();
require("../config/db.php");

/* ADMIN CHECK - Sirf admin users ko access */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* ORDER KI STATUS UPDATE KARNE KA KAAM */
if (isset($_POST['update_status'])) {
    // Order ID le lo
    $id = intval($_POST['order_id']);
    // new status form se lo
    $status = $_POST['status'];

    // Database mein status update karo
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

/* ORDER KO DELETE KARNE KA KAAM */
if (isset($_GET['delete'])) {
    // Order ID le lo
    $id = intval($_GET['delete']);

    // Database se order delete karo
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* SEARCH AUR SORT KA LOGIC */
// Search term le lo (agar ho)
$search = $_GET['search'] ?? "";
// Sort option le lo (agar ho)
$sort   = $_GET['sort'] ?? "";

/* ORDER BY - Sorting ke liye */
// Default mein newest first (DESC)
$orderBy = "ORDER BY o.id DESC";
if ($sort == "low") {
    // Low to high price
    $orderBy = "ORDER BY o.total_amount ASC";
} elseif ($sort == "high") {
    // High to low price
    $orderBy = "ORDER BY o.total_amount DESC";
}

/* BASE QUERY - Database se orders le lo */
$sql = "
SELECT 
    o.id,  /* Order ID */
    o.total_amount,  /* Order ka total amount */
    o.status,  /* Order ki status */
    o.created_at,  /* Kab create hua */
    u.name AS customer_name  /* Customer ka naam */
FROM orders o
JOIN users u ON o.user_id = u.id  /* Orders aur users ko join karo */
WHERE u.role = 'customer'  /* Sirf customers ka */
";

if (!empty($search)) {
    // Search term ke saath query
    $like = "%$search%";
//Original query me add kar rahe hain
    $sql .= " AND (o.id LIKE ? OR u.name LIKE ?) $orderBy";

    $stmt = $conn->prepare($sql); //search
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $orders = $stmt->get_result();

} else {
    // Agar search nahi toh sab orders dikha do
    $sql .= " $orderBy";
    $orders = $conn->query($sql);
}

/* STATUS CLEAN FUNCTION - Status ko safe format mein convert karo */
function cleanStatus($status){//agarletters k ilawa kuch to hata do
    return preg_replace('/[^a-zA-Z]/', '', $status);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Orders</title>

<style>
/* Page ka background - Maroon color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#eda7a2;  /* Light pink */
}

/* Main container - Center mein content */
.container{
    width:95%;  /* Mobile ke liye */
    max-width:1200px;
    margin:auto;
    text-align:center;
    padding-bottom:40px;  /* Neeche space */
}

/* Heading - Bada title */
h2{
    color:#800000;  /* Maroon color */
    margin-top:25px;
}

/* Search aur sort form */
.search-box{
    margin:20px auto;
    display:flex;  /* Flex layout */
    justify-content:center;  /* Center mein */
    gap:10px;  /* Items ke beech gap */
    flex-wrap:wrap;  /* Mobile par wrap ho jaye */
}

/* Search input aur select fields */
.search-box input,
.search-box select{
    padding:10px;  /* Inner spacing */
    border-radius:10px;  /* Round corners */
    border:1px solid #ddd;  /* Light border */
}

/* Search button */
.search-box button{
    padding:10px 20px;  /* Button ka size */
    background:#800000;  /* Maroon button */
    color:white;  /* White text */
    border:none;
    border-radius:10px;  /* Round button */
}

/* Table ko responsive banao */
.table-wrapper{
    width:100%;  /* Puri width */
    overflow-x:auto;  /* Horizontal scroll agar zaroori ho */
    background:#ffe6f2;  /* White background */
    border-radius:10px;  /* Round corners */
}

/* Table styling */
table{
    width:100%;  /* Puri width */
    min-width:800px;  /* Minimum width - readable rahe */
    border-collapse:collapse;  /* Borders ko milao */
}

/* Table ke headings - Upar wali row */
th{
    background:#800000;  /* Maroon heading */
    color:white;  /* White text */
    padding:15px;  /* Inner spacing */
    text-align:left;  /* Left align */
}

/* Table ke cells - Data */
td{
    padding:12px;  /* Inner spacing */
    border-bottom:1px solid #eee;  /* Neeche line */
    text-align:left;  /* Left align */
}

/* STATUS BADGE - Status ka display */
.status{
    padding:5px 10px;  /* Badge size */
    border-radius:8px;  /* Round corners */
    font-size:13px;  /* Chhota font */
    display:inline-block;  /* Block styling */
}

/* Status colors - Different status ke liye different colors */
.Pending{background:#ffeaa7;}  /* Yellow - Pending */
.Processing{background:#74b9ff;}  /* Blue - Processing */
.Shipped{background:#81ecec;}  /* Cyan - Shipped */
.Delivered{background:#55efc4;}  /* Green - Delivered */
.Cancelled{background:#ff7675;}  /* Red - Cancelled */

/* Table mein select aur button */
table select{
    padding:5px;  /* Inner spacing */
    border-radius:5px;  /* Round corners */
}

table button{
    background:#800000;  /* Maroon button */
    color:white;  /* White text */
    border:none;
    padding:6px 10px;  /* Button size */
    border-radius:8px;  /* Round button */
}

/* Delete link styling */
.delete{
    color:red;  /* Red text */
    font-weight:bold;  /* Bold */
    text-decoration:none;  /* No underline */
}

/* Locked status styling */
.locked{
    color:green;  /* Green text */
    font-weight:bold;  /* Bold */
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<!-- Orders manage karne ka heading -->
<h2>Manage Orders</h2>

<!-- Search aur sort form -->
<form method="GET" class="search-box">
    <!-- Search input - Order ID ya customer name se search karo -->
    <input type="text" name="search" placeholder="Search order or user..." value="<?= htmlspecialchars($search) ?>">

    <!-- Sort dropdown - Price se sort karne ke liye -->
    <select name="sort">
        <option value="">Sort by Price</option>
        <option value="low" <?= $sort=="low"?'selected':'' ?>>Low → High</option>
        <option value="high" <?= $sort=="high"?'selected':'' ?>>High → Low</option>
    </select>

    <!-- Apply button -->
    <button type="submit">Apply</button>
</form>

<!-- Table ka container -->
<div class="table-wrapper">

<table>
<!-- Table ke headings -->
<tr>
    <th>Order ID</th>  <!-- Order ka unique ID -->
    <th>Customer Name</th>  <!-- Jo customer ne order kiya -->
    <th>Total</th>  <!-- Order ka total amount -->
    <th>Status</th>  <!-- Current status -->
    <th>Change Status</th>  <!-- Status change karne ke liye -->
    <th>Action</th>  <!-- Delete option -->
</tr>

<!-- Database se har order ki row -->
<?php while($row = $orders->fetch_assoc()): ?>

<tr>
    <!-- Order ID -->
    <td>#<?= $row['id']; ?></td>
    <!-- Customer ka naam -->
    <td><?= htmlspecialchars($row['customer_name']); ?></td>
    <!-- Order ka total amount -->
    <td>Rs <?= number_format($row['total_amount']); ?></td>

    <!-- Current status with color badge -->
    <td>
        <span class="status <?= cleanStatus($row['status']); ?>">
            <?= $row['status']; ?>
        </span>
    </td>

    <!-- Status update form - Sirf pending/processing/shipped orders ke liye -->
    <td>
        <?php if ($row['status'] != "Delivered" && $row['status'] != "Cancelled"): ?>
        
        <!-- Status change form -->
        <form method="POST" style="display:flex;gap:5px;align-items:center;">
            <!-- Hidden order ID -->
            <input type="hidden" name="order_id" value="<?= $row['id']; ?>">

            <!-- Status options dropdown -->
            <select name="status">
                <option <?= $row['status']=="Pending"?'selected':'' ?>>Pending</option>
                <option <?= $row['status']=="Processing"?'selected':'' ?>>Processing</option>
                <option <?= $row['status']=="Shipped"?'selected':'' ?>>Shipped</option>
                <option <?= $row['status']=="Delivered"?'selected':'' ?>>Delivered</option>
                <option <?= $row['status']=="Cancelled"?'selected':'' ?>>Cancelled</option>
            </select>

            <!-- Update button -->
            <button name="update_status">Update</button>
        </form>

        <?php else: ?>
            <!-- Agar delivered ya cancelled hai toh locked status dikhao -->
            <span class="locked">✔ <?= $row['status']; ?></span>
        <?php endif; ?>
    </td>

    <!-- Delete link -->
    <td>
        <!-- Order ko delete karne ke liye -->
        <a class="delete" href="?delete=<?= $row['id']; ?>" onclick="return confirm('Delete order?')">Delete</a>
    </td>
</tr>

<?php endwhile; ?>

</table>

</div>

</div>

</body>
</html>