<?php
// Include database configuration file
include 'config.php';
include 'adminheader.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Fetch products from the database
$query = "SELECT product_name, quantity, price FROM products";
$result = $conn->query($query);

if ($result === false) {
    die("Error: " . $conn->error);
}

$grandTotal = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Style for the title */
        h2 {
            text-align: center;
            color: #333;
            font-family: Arial, sans-serif;
            margin-top: 20px;
        }

        /* Style for the container */
        .table-container {
            max-height: 600px; /* Adjust based on desired height */
            overflow-y: auto;
            margin: 20px auto;
            width: 80%;
        }

        /* Style for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Style for the grand total row */
        tfoot tr {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Product List</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Available Quantity</th>
                    <th>Price</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): 
                    $totalValue = $row['quantity'] * $row['price'];
                    $grandTotal += $totalValue;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars(number_format($totalValue, 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Grand Total</td>
                    <td><?php echo htmlspecialchars(number_format($grandTotal, 2)); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>

<?php
// Free result set
$result->free();

// Close connection
$conn->close();
?>
