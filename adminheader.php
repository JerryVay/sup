<?php
// Check if the session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .header {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .nav {
            display: flex;
            justify-content: center;
            background: #444;
        }
        .nav a {
            color: #fff;
            padding: 15px 20px;
            text-decoration: none;
            text-align: center;
        }
        .nav a:hover {
            background: #555;
        }
        .container {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>
    <div class="nav">
        <a href="add_cashier.php">Add Cashier</a>
        <a href="remove_cashier.php">Remove Cashier</a>
        <a href="view_sales.php">Transactions</a>
        <a href="add_product.php">Add New Product</a>
        <a href="view_products.php">View Products</a>
        <a href="review_prices.php">Review Prices</a>
        <a href="report.php">Reports</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <!-- Content will be included here -->
    </div>
</body>
</html>
