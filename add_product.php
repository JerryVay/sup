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
    die("Access denied. Please log in as an admin.");
}

// Function to add a new product
function addProduct($productName, $price, $buyingPrice, $quantity) {
    global $conn;

    // Check if product with the same name already exists
    $checkQuery = "SELECT * FROM products WHERE product_name = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "A product with the same name already exists.";
    }

    // Insert new product into the table
    $insertQuery = "INSERT INTO products (product_name, price, buying_price, quantity) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sdii", $productName, $price, $buyingPrice, $quantity);

    if ($stmt->execute()) {
        return "Product added successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $buyingPrice = $_POST['buying_price'];
    $quantity = $_POST['quantity'];

    $message = addProduct($productName, $price, $buyingPrice, $quantity);
    if ($message === "A product with the same name already exists.") {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { alert('A product with the same name already exists.'); });</script>";
    } else {
        echo "<p class='success'>$message</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Style for the title */
        h2 {
            text-align: center;
            color: #333;
            font-family: Arial, sans-serif;
            margin-top: 20px;
        }

        /* Style for the form container */
        form {
            max-width: 500px;
            margin: 20px auto;
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
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Style for submit button */
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        /* Style for submit button hover effect */
        input[type="submit"]:hover {
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
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const message = '<?php echo $message ?? ''; ?>';
            if (message === 'A product with the same name already exists.') {
                alert(message);
            }
        });
    </script>
</head>
<body>
    <h2>Add New Product</h2>
    <form method="post" action="">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>
        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required>
        <label for="buying_price">Buying Price:</label>
        <input type="number" step="0.01" id="buying_price" name="buying_price" required>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>
        <input type="submit" value="Add Product">
    </form>
</body>
</html>
