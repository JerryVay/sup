<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config.php'; // Include your database connection file
include 'adminheader.php';

// Fetch product data
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

// Handle price and quantity updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $new_price = $_POST['new_price'];
    $new_buying_price = $_POST['new_buying_price'];
    $new_quantity = $_POST['new_quantity'];

    $update_sql = "UPDATE products SET price = ?, buying_price = ?, quantity = ? WHERE product_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ddii", $new_price, $new_buying_price, $new_quantity, $product_id);
    
    if ($update_stmt->execute()) {
        header("Location: review_prices.php");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Prices</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 6px; /* Reduced padding */
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px; /* Smaller font size */
        }

        th {
            background-color: #333;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        p.error {
            color: #d9534f; /* Red color for errors */
            text-align: center;
        }

        .btn-update {
            padding: 8px 16px; /* Adjusted padding */
            background-color: #5cb85c;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            border: none;
        }

        .btn-update:hover {
            background-color: #4cae4c;
        }

        .btn-form {
            margin: 5px 0; /* Adjusted margin */
        }

        .table-container {
            max-height: 600px; /* Adjust height as needed */
            overflow-y: auto;
            margin-top: 20px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.btn-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!confirm('Are you sure you want to update this product?')) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Review Prices</h1>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th>Available Quantity</th>
                            <th>Update Prices & Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo number_format($row['buying_price'], 2); ?></td>
                                <td><?php echo number_format($row['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td>
                                    <form method="post" action="review_prices.php" class="btn-form">
                                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                        <input type="number" step="0.01" name="new_buying_price" value="<?php echo $row['buying_price']; ?>" placeholder="New Buying Price">
                                        <input type="number" step="0.01" name="new_price" value="<?php echo $row['price']; ?>" placeholder="New Selling Price">
                                        <input type="number" name="new_quantity" value="<?php echo $row['quantity']; ?>" placeholder="New Quantity">
                                        <button type="submit" class="btn-update">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="error">No products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
