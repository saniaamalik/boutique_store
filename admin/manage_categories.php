<?php
session_start();
require("../config/db.php");

// ONLY ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* DELETE CATEGORY (SAFE) */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // optional safety: check if category used in products
    $check = $conn->query("SELECT id FROM products WHERE category_id=$id");

    if ($check->num_rows > 0) {
        echo "<script>alert('Cannot delete: Category is linked with products!');</script>";
    } else {
        $conn->query("DELETE FROM categories WHERE id=$id");
    }
}

/* SEARCH (SAFE) */
$search = $_GET['search'] ?? "";

/* PREPARED STYLE QUERY (safer) */
if ($search != "") {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name LIKE ?");
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $categories = $stmt->get_result();
} else {
    $categories = $conn->query("SELECT * FROM categories");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Categories</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;
}

.container{
    width:95%; /* Mobile side margins improve karne ke liye */
    max-width:1200px;
    margin:auto;
    text-align:center;
}

h2{
    color:#6a0dad;
    margin-top:25px;
    font-size: 1.8rem;
}

/* SEARCH BOX RESPONSIVE */
.search-box{
    margin:20px auto;
    display:flex;
    justify-content:center;
    gap:10px;
    flex-wrap:wrap;
}

.search-box input{
    width: 250px;
    max-width: 100%;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-sizing: border-box;
}

.search-box button{
    padding: 10px 20px;
    border: none;
    background: #6a0dad;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
}

.search-box button:hover{
    background: #4b0082;
}

/* ⭐ TABLE RESPONSIVE WRAPPER */
.table-wrapper{
    width:100%;
    overflow-x:auto; /* Table agar screen se bari ho toh scroll bar aa jaye */
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

table{
    width:100%;
    min-width:500px; /* Table ko bohat chota hone se rokta hai taake data readable rahe */
    border-collapse:collapse;
}

th{
    background:#6a0dad;
    color:white;
    padding:15px 12px;
    text-align: left;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align: left;
}

/* ACTION LINKS */
.edit{ color:blue; font-weight:bold; text-decoration:none; margin-right: 5px; }
.delete{ color:red; font-weight:bold; text-decoration:none; margin-left: 5px; }

/* 📱 MEDIA QUERIES */
@media (max-width: 600px) {
    h2 { font-size: 1.5rem; }
    
    .search-box{
        flex-direction:column;
        align-items:center;
        width: 100%;
    }

    .search-box input{
        width:100%;
    }
    
    .search-box button {
        width: 100%;
    }

    table{
        font-size:14px;
    }
    
    th, td {
        padding: 10px 8px;
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <h2>Manage Categories</h2>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search category..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td>
                        <a class="edit" href="edit_category.php?id=<?= $row['id']; ?>">Edit</a> |
                        <a class="delete" href="?delete=<?= $row['id']; ?>" onclick="return confirm('Delete category?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>