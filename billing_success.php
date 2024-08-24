<?php
session_start();
include 'config.php'; // Database connection

if (!isset($_GET['transaction_id']) || empty($_GET['transaction_id'])) {
    // Redirect if transaction_id is not provided
    header("Location: billing.php");
    exit();
}

$transaction_id = $_GET['transaction_id'];

// Fetch transaction details
$stmt = $conn->prepare("SELECT t.*, c.username AS cashier_username FROM transactions t 
                        INNER JOIN cashiers c ON t.cashier_id = c.cashier_id 
                        WHERE t.transaction_id = ?");
$stmt->bind_param("i", $transaction_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $transaction = $result->fetch_assoc();
    // Fetched data successfully
} else {
    // Redirect if transaction not found
    header("Location: index.php");
    exit();
}
$stmt->close();

// Fetch transaction items
$stmt = $conn->prepare("SELECT tp.*, p.product_name 
                        FROM transaction_profits tp 
                        INNER JOIN products p ON tp.product_id = p.product_id 
                        WHERE tp.transaction_id = ?");
$stmt->bind_param("i", $transaction_id);
$stmt->execute();
$items_result = $stmt->get_result();
$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body {
            width: 80mm;
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
        }
        .receipt {
            width: 100%;
            padding: 10px;
            border: 1px solid #000;
        }
        .receipt-header, .receipt-footer {
            text-align: center;
        }
        .receipt-header h2 {
            margin: 5px 0;
        }
        .receipt-header p, .receipt-footer p {
            margin: 2px 0;
        }
        .dotted-line {
            border-bottom: 1px dotted #000;
            margin: 10px 0;
        }
        .receipt-items {
            width: 100%;
        }
        .receipt-items th, .receipt-items td {
            text-align: left;
            padding: 5px 0;
        }
        .receipt-items th {
            border-bottom: 1px solid #000;
        }
        .total {
            text-align: right;
            margin-top: 10px;
        }
        .print-button {
            margin-top: 20px;
            text-align: center;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .print-button button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h2>Dom&Jos Finishes</h2>
            <p>Dealers in Gypsum boards, MDFs, Gutters, Paints, Electricals and etc</p>
            <p>Receipt</p>
            <p>Date: <?php echo date('Y-m-d H:i:s', strtotime($transaction['transaction_date'])); ?></p>
        </div>
        <div class="receipt-body">
            <table class="receipt-items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price (Incl 16% VAT)</th>
                        <th>Discount</th>
                        <th>Total (Incl VAT)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through transaction items -->
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo number_format($item['selling_price'], 2); ?></td>
                        <td><?php echo number_format($item['quantity'] * $item['discount'], 2); ?></td>
                        <td><?php echo number_format(($item['quantity'] * $item['selling_price']) - ($item['quantity'] * $item['discount']), 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                <p>Discount: <?php echo number_format($transaction['discount'], 2); ?></p>
                <p>Total (Incl VAT): <?php echo number_format($transaction['final_total'], 2); ?></p>
            </div>
        </div>
        <div class="receipt-footer">
            <p>You were served by <?php echo htmlspecialchars($transaction['cashier_username']); ?></p>
            <p>Thank you for shopping with us!</p>
        </div>
    </div>
    <div class="print-button">
        <button onclick="window.print()">Print Receipt</button>
    </div>
</body>
</html>
