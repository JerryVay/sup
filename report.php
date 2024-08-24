<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config.php'; // Include your database connection file
include 'adminheader.php';

// Initialize variables
$start_date = $end_date = '';
$results = [];
$error = '';

// Handle date range form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Ensure dates are in the correct format (YYYY-MM-DD)
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));
    
    // Adjust end_date to include the whole day
    $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));

    // Prepare SQL query with date range
    $sql = "SELECT * FROM transaction_profits WHERE date_added >= ? AND date_added < ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);

    if ($stmt->execute()) {
        $results = $stmt->get_result();
        if ($results->num_rows === 0) {
            $error = "No records found for the selected date range.";
        }
    } else {
        $error = "Error fetching data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Profits Report</title>
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

        .form-container {
            margin-bottom: 20px;
        }

        .form-container form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .form-container input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #4cae4c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .table-container {
            max-height: 600px; /* Adjust height as needed */
            overflow-y: auto;
            margin-top: 20px;
        }

        p.error {
            color: #d9534f; /* Red color for errors */
            text-align: center;
        }

        /* Style for the title */
        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaction Profits Report</h1>
        <div class="form-container">
            <form method="post" action="report.php">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
                <button type="submit">Generate Report</button>
            </form>
        </div>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (!empty($results)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction ID</th>
                            <th>Product ID</th>
                            <th>Quantity</th>
                            <th>Selling Price</th>
                            <th>Buying Price</th>
                            <th>Profit/Loss</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo number_format($row['selling_price'], 2); ?></td>
                                <td><?php echo number_format($row['buying_price'], 2); ?></td>
                                <td><?php echo number_format($row['profit_loss'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <p class="error">No records found for the selected date range.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
