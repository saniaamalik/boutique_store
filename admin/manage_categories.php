<?php
// Session start - User ki jankari save karne ke liye
session_start();
require("../config/db.php");

// SIRF ADMIN USERS KO ACCESS - Dusre ko bahar nikal do
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

/* CATEGORY KO DELETE KARNE KA KAAM */
//url ma exist kerta ha yani
if (isset($_GET['delete'])) {
    // ID le lo aur secure tarikay se delete karo
    $id = intval($_GET['delete']);
    // Pehlay check karo ke kya yeh category kisi product mein use ho rahi hai
    $check = $conn->query("SELECT id FROM products WHERE category_id=$id");

    if ($check->num_rows > 0) {
        // Agar category products mein use ho rahi hai toh delete nahi kar sakte
        echo "<script>alert('Cannot delete: Category is linked with products!');</script>";
    } else {
        // Category ko database se delete karo
        $conn->query("DELETE FROM categories WHERE id=$id");
    }
}

/* CATEGORY KO SEARCH KARNE KA KAAM */
// Search term ko le lo
$search = $_GET['search'] ?? "";//url se search

/* PREPARED STYLE QUERY - Secure aur safe */
if ($search != "") {
    // Search term ke saath categories find karo
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name LIKE ?");
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $categories = $stmt->get_result();
} else {
    // Agar search nahi toh sab categories dikha do
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
/* Page ka background - Purple color */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#e9d5ff;  /* Light purple */
}

/* Main container - Center mein content */
.container{
    width:95%;  /* Mobile ke liye side margins */
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

/* Search form box - Categories dhundne ke liye */
.search-box{
    margin:20px auto;
    display:flex;  /* Flex layout */
    justify-content:center;  /* Center mein */
    gap:10px;  /* Items ke beech gap */
    flex-wrap:wrap;  /* Mobile par wrap ho jaye */
}

/* Search input field */
.search-box input{
    width: 250px;  /* Normal width */
    max-width: 100%;  /* Mobile par 100% */
    padding: 10px;
    border-radius: 10px;  /* Round corners */
    border: 1px solid #ddd;  /* Light border */
    box-sizing: border-box;
}

/* Search button */
.search-box button{
    padding: 10px 20px;  /* Button ka size */
    border: none;
    background: #6a0dad;  /* Purple button */
    color: white;  /* White text */
    border-radius: 10px;  /* Round button */
    cursor: pointer;  /* Cursor change */
    transition: 0.3s;  /* Smooth animation */
}

/* Button hover effect */
.search-box button:hover{
    background: #4b0082;  /* Dark purple */
}

/* Table ko responsive banao - Mobile mein scroll kare */
.table-wrapper{
    width:100%;
    overflow-x:auto;  /* Horizontal scroll agar zaroori ho */
    background: white;  /* White background */
    border-radius: 10px;  /* Round corners */
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);  /* Shadow effect */
}

/* Table styling - Data likha ho */
table{
    width:100%;  /* Puri width */
    min-width:500px;  /* Minimum width - readable rahe */
    border-collapse:collapse;  /* Borders ko milao */
}

/* Table ke headings - Upar wali row */
th{
    background:#6a0dad;  /* Purple heading */
    color:white;  /* White text */
    padding:15px 12px;  /* Inner spacing */
    text-align: left;  /* Left align */
}

/* Table ke cells - Data */
td{
    padding:12px;  /* Inner spacing */
    border-bottom:1px solid #eee;  /* Neeche line */
    text-align: left;  /* Left align */
}

/* ACTION LINKS - Edit aur Delete */
.edit{ color:blue; font-weight:bold; text-decoration:none; margin-right: 5px; }  /* Edit link */
.delete{ color:red; font-weight:bold; text-decoration:none; margin-left: 5px; }  /* Delete link */

/* Mobile screens ke liye responsive design */
@media (max-width: 600px) {
    h2 { font-size: 1.5rem; }  /* Heading ko chhota banao */
    
    .search-box{
        flex-direction:column;  /* Column layout */
        align-items:center;  /* Center align */
        width: 100%;
    }

    .search-box input{
        width:100%;  /* Full width */
    }
    
    .search-box button {
        width: 100%;  /* Full width */
    }

    table{
        font-size:14px;  /* Chhote font */
    }
    
    th, td {
        padding: 10px 8px;  /* Kam padding */
    }
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

    <!-- Categories manage karne ka heading -->
    <h2>Manage Categories</h2>

    <!-- Search form - Categories dhundne ke liye -->
    <form method="GET" class="search-box">
        <!-- Search input field -->
        <input type="text" name="search" placeholder="Search category..." value="<?= htmlspecialchars($search) ?>">
        <!-- Search button -->
        <button type="submit">Search</button>
    </form>

    <!-- Table ka container -->
    <div class="table-wrapper">
        <table>
            <!-- Table ke headings -->
            <thead>
                <tr>
                    <th>ID</th>  <!-- Category ka ID -->
                    <th>Category Name</th>  <!-- Category ka naam -->
                    <th>Action</th>  <!-- Edit/Delete buttons -->
                </tr>
            </thead>
            <!-- Table ka data - Database se -->
            <tbody>
                <!-- Har category ki row -->
                <?php while($row = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td>
                        <!-- Edit link - Category ko edit karne ke liye -->
                        <a class="edit" href="edit_category.php?id=<?= $row['id']; ?>">Edit</a> |
                        <!-- Delete link - Category ko delete karne ke liye -->
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