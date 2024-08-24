<?php
include 'config.php'; // Database connection
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo "Admin user created successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
</head>
<body>
    <h2>Create Admin</h2>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Create Admin">
    </form>
</body>
</html>
