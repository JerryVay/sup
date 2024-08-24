<!DOCTYPE html>
<html>
<head>
    <title>Welcome to DOMJOS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .welcome-text {
            font-size: 24px;
            margin-bottom: 20px;
            overflow: hidden;
            white-space: nowrap;
            box-sizing: border-box;
            animation: marquee 10s linear infinite;
        }
        @keyframes marquee {
            from { transform: translateX(100%); }
            to { transform: translateX(-100%); }
        }
        .options {
            margin-top: 30px;
        }
        .options a {
            display: inline-block;
            width: 150px;
            padding: 10px;
            margin: 10px;
            text-align: center;
            color: #fff;
            background-color: #5cb85c;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .options a:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-text">Welcome to DOMJOS!</div>
        <div class="options">
            <a href="cashier_login.php">Cashier Login</a>
            <a href="admin_login.php">Admin Login</a>
        </div>
    </div>
</body>
</html>
