<?php
// Include the database configuration and header
include 'config.php'; // Database connection
include 'adminheader.php'; // Admin header

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO cashiers (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "<p class='success'>Cashier added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cashier</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Style for the form container */
        form {
            max-width: 400px;
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
        input[type="text"],
        input[type="password"] {
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
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
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
    </style>
</head>
<body>
    <!-- Form for adding a cashier -->
    <form method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Add Cashier</button>
    </form>
</body>
</html>
