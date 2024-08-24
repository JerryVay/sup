<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cashier_id'])) {
    header("Location: cashier_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #5cb85c;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            padding: 0;
        }
        .options {
            margin-top: 10px;
        }
        .options a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px;
            background-color: #fff;
            color: #5cb85c;
            text-decoration: none;
            border-radius: 4px;
            border: 1px solid #5cb85c;
        }
        .options a:hover {
            background-color: #4cae4c;
            color: #fff;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".options a").not("#logout-link").click(function(e){
                e.preventDefault();
                var url = $(this).attr("href");
                window.location.href = url; // Redirect to the selected page
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['cashier_username']); ?>!</h1>
        <div class="options">
            <a href="billing.php">Billing System</a>
            <a href="view_transactions.php">View Transactions</a>
            <a href="profile.php">Profile Management</a>
            <a href="change_password.php">Change Password</a>
            <a id="logout-link" href="cashier_logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
