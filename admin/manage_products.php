<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* DELETE PRODUCT */
// Agar URL me delete parameter aaya hai (?delete=5)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);//id ko int ma convert kero..

     // product ka imd db se nikala
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $img = $stmt->get_result()->fetch_assoc();

    //agar image exist kerti ha todel kerdo
    if ($img && !empty($img['image'])) {
        $file = "../uploads/" . $img['image'];
        if (file_exists($file)) unlink($file);
    }
//db se delete
    $del = $conn->prepare("DELETE FROM products WHERE id=?");
    $del->bind_param("i", $id);
    $del->execute();
}

/* INPUTS */
$search = $_GET['search'] ?? "";
$category = $_GET['category'] ?? "";
$sort = $_GET['sort'] ?? "";

/* BASE QUERY */
$sql = "
SELECT products.*, categories.name AS category_name
FROM products
LEFT JOIN categories ON products.category_id = categories.id
WHERE 1=1
";

$params = []; // sql parameter
$types = ""; //parameters types

/* SEARCH */
if (!empty($search)) {
    $sql .= " AND products.name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

/* CATEGORY */
if (!empty($category)) {
    $sql .= " AND categories.name = ?";//category mtch kerna
    $params[] = $category;
    $types .= "s";
}

/* SORT */
if ($sort == "low") {
    $sql .= " ORDER BY products.price ASC";
} elseif ($sort == "high") {
    $sql .= " ORDER BY products.price DESC";
} else {
    $sql .= " ORDER BY products.id DESC";
}

/* EXECUTE */
$stmt = $conn->prepare($sql);
if (!empty($params)) {//if not empty then bind and fetch result
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

/* CATEGORIES */
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Products</title>

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
    padding-bottom:50px;
}

h2{
    color:#6a0dad;
    margin-top:25px;
    font-size:1.8rem;
}

/* SEARCH */
.search-bar form{
    width:90%;
    max-width:500px;
    margin:20px auto;
    display:flex;
    gap:10px;
    background:white;
    padding:10px;
    border-radius:10px;
}

.search-bar input{
    flex:1;
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
}

.search-bar button{
    width:100px;
    background:#6a0dad;
    color:white;
    border:none;
    border-radius:8px;
}

/* FILTERS */
.filters{
    display:flex;
    justify-content:center;
    gap:10px;
    flex-wrap:wrap;
    margin:20px 0;
}

.filters select{
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
}

.filters button{
    padding:10px 20px;
    background:#6a0dad;
    color:white;
    border:none;
    border-radius:10px;
}

/* TABLE */
.table-wrapper{
    overflow-x:auto;
    background:white;
    border-radius:10px;
}

table{
    width:100%;
    min-width:800px;
    border-collapse:collapse;
}

th{
    background:#6a0dad;
    color:white;
    padding:15px;
    text-align:left;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
}

td img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
}

.edit{color:blue;font-weight:bold;text-decoration:none;}
.delete{color:red;font-weight:bold;text-decoration:none;}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<h2>Manage Products</h2>

<!-- SEARCH -->
<div class="search-bar">
<form method="GET">
    <input type="text" name="search" placeholder="Search product..."
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>
</div>

<!-- FILTER -->
<form method="GET" class="filters">
    <select name="category">
        <option value="">All Categories</option>
        <?php while($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['name']; ?>"
                <?= ($category == $cat['name']) ? "selected" : "" ?>>
                <?= htmlspecialchars($cat['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="sort">
        <option value="">Sort by Price</option>
        <option value="low" <?= ($sort=="low")?'selected':'' ?>>Low → High</option>
        <option value="high" <?= ($sort=="high")?'selected':'' ?>>High → Low</option>
    </select>

    <button type="submit">Apply</button>
</form>

<!-- TABLE -->
<div class="table-wrapper">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Name</th>
    <th>Price</th>
    <th>Category</th>
    <th>Stock</th> <!-- ✅ NEW -->
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row = $products->fetch_assoc()): ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><img src="../uploads/<?= htmlspecialchars($row['image']); ?>"></td>
    <td><?= htmlspecialchars($row['name']); ?></td>
    <td>Rs <?= number_format($row['price'], 2); ?></td>
    <td><?= htmlspecialchars($row['category_name']); ?></td>

    <!-- STOCK -->
    <td><?= htmlspecialchars($row['stock']); ?></td>

    <td>
        <a class="edit" href="edit_product.php?id=<?= $row['id']; ?>">Edit</a> |
        <a class="delete" href="?delete=<?= $row['id']; ?>" onclick="return confirm('Delete product?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>
</div>

</div>

</body>
</html>