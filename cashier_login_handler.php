<?php
session_start();
include 'config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM cashiers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $cashier = $result->fetch_assoc();
        if (password_verify($password, $cashier['password'])) {
            $_SESSION['cashier_id'] = $cashier['cashier_id'];
            $_SESSION['cashier_username'] = $cashier['username'];
            header("Location: cashier_dashboard.php");
            exit();
        } else {
            header("Location: cashier_login.php?error=Incorrect password");
            exit();
        }
    } else {
        header("Location: cashier_login.php?error=User not found");
        exit();
    }
}
?>
