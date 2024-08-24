<?php
// Display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the header file
include 'adminheader.php';

// Include database configuration
include 'config.php'; 

// Handle form submission for managing cashiers
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove_cashier'])) {
        $cashier_id = $_POST['cashier_id'];

        $stmt = $conn->prepare("DELETE FROM cashiers WHERE cashier_id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $cashier_id);

        if ($stmt->execute()) {
            echo "<p class='success'>Cashier removed successfully!</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } elseif (isset($_POST['disable_cashier'])) {
        $cashier_id = $_POST['cashier_id'];

        $stmt = $conn->prepare("UPDATE cashiers SET status = 'disabled' WHERE cashier_id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $cashier_id);

        if ($stmt->execute()) {
            echo "<p class='success'>Cashier disabled successfully!</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } elseif (isset($_POST['enable_cashier'])) {
        $cashier_id = $_POST['cashier_id'];

        $stmt = $conn->prepare("UPDATE cashiers SET status = 'active' WHERE cashier_id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $cashier_id);

        if ($stmt->execute()) {
            echo "<p class='success'>Cashier enabled successfully!</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Fetch all cashiers
$sql = "SELECT cashier_id, username, status FROM cashiers";
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
    <title>Manage Cashiers</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Style for the form container */
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        /* Style for form labels */
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        /* Style for input fields */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Style for submit button */
        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 2px; /* Small margin between buttons */
        }

        /* Style for submit button hover effect */
        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Style for feedback messages */
        p.success {
            font-size: 16px;
            color: #28a745; /* Green color for success messages */
            text-align: center;
        }

        p.error {
            font-size: 16px;
            color: #d9534f; /* Red color for errors */
            text-align: center;
        }

        /* Style for the list of cashiers */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Style for action buttons */
        td.actions {
            white-space: nowrap; /* Prevent text wrapping */
            text-align: center;
        }
        button.remove {
            background-color: #dc3545;
        }
        button.remove:hover {
            background-color: #c82333;
        }
        button.enable {
            background-color: #28a745;
        }
        button.enable:hover {
            background-color: #218838;
        }
        button.disable {
            background-color: #ffc107;
        }
        button.disable:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <!-- Form for managing cashiers -->
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Cashier ID</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cashier_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td class="actions">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="cashier_id" value="<?php echo htmlspecialchars($row['cashier_id']); ?>">
                                <?php if ($row['status'] == 'active'): ?>
                                    <button type="submit" name="disable_cashier" class="disable">Disable</button>
                                <?php else: ?>
                                    <button type="submit" name="enable_cashier" class="enable">Enable</button>
                                <?php endif; ?>
                                <button type="submit" name="remove_cashier" class="remove">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
</body>
</html>

<?php
$conn->close();
?>
