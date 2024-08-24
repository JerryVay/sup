<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config.php'; // Database connection
include 'adminheader.php'; // Include header

// Fetch sales transactions
$sql = "SELECT * FROM transactions ORDER BY transaction_date DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sales</title>
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
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Sales Transactions</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Cashier ID</th>
                        <th>Total</th>
                        <th>Discount</th>
                        <th>VAT</th>
                        <th>Final Total</th>
                        <th>Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['cashier_id']); ?></td>
                            <td><?php echo number_format($row['total'], 2); ?></td>
                            <td><?php echo number_format($row['discount'], 2); ?></td>
                            <td><?php echo number_format($row['vat'], 2); ?></td>
                            <td><?php echo number_format($row['final_total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error">No sales transactions found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
