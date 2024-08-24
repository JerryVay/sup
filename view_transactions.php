<?php
session_start();
if (!isset($_SESSION['cashier_id'])) {
    header("Location: cashier_login.php");
    exit();
}

// Start output buffering
ob_start();

include 'config.php'; // Include your database connection file
include 'cashierheader.php';

// Get the logged-in cashier's ID
$cashier_id = $_SESSION['cashier_id'];

$cashier_name = '';
$cashier_query = "SELECT username FROM cashiers WHERE cashier_id = ?";
$cashier_stmt = $conn->prepare($cashier_query);
$cashier_stmt->bind_param("i", $cashier_id);
$cashier_stmt->execute();
$cashier_result = $cashier_stmt->get_result();

if ($cashier_result->num_rows > 0) {
    $cashier_row = $cashier_result->fetch_assoc();
    $cashier_name = $cashier_row['username'];
}

// Fetch sales transactions for the logged-in cashier
$sql = "SELECT * FROM transactions WHERE cashier_id = ? ORDER BY transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cashier_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

// Check if download is requested
if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    // Include Composer's autoload file
    require_once 'vendor/autoload.php';

    $pdf = new \TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Add business information with styles
    $html = '
    <style>
        h1 {
            color: #333;
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            color: #666;
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        h3 {
            color: #333;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
    <h1>Dom&Jos Finishes</h1>
    <h2>Dealers in Gypsum boards, MDFs, Gutters, Paints, Electricals, and more</h2>
    <h3>Cashier ' . htmlspecialchars($cashier_name) . ' Sales Report</h3>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Total</th>
                <th>Discount</th>
                <th>VAT</th>
                <th>Final Total</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>';

    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['transaction_id']) . '</td>
                    <td>' . number_format($row['total'], 2) . '</td>
                    <td>' . number_format($row['discount'], 2) . '</td>
                    <td>' . number_format($row['vat'], 2) . '</td>
                    <td>' . number_format($row['final_total'], 2) . '</td>
                    <td>' . htmlspecialchars($row['transaction_date']) . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';

    // Output the PDF
    $pdf->writeHTML($html);

    // Clear output buffer and end it
    ob_end_clean();

    $pdf->Output('sales_report.pdf', 'D'); // 'D' forces download
    exit();
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

        .btn-download {
            display: inline-block;
            padding: 10px 20px;
            background-color: #5cb85c;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }

        .btn-download:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Sales Report</h1>
        <a href="view_transactions.php?download=pdf" class="btn-download">Download PDF Report</a>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
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
$stmt->close();
$conn->close();
?>
