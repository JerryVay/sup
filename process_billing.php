<?php
session_start();
if (!isset($_SESSION['cashier_id'])) {
    header("Location: cashier_login.php");
    exit();
}

include 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cashier_id = $_SESSION['cashier_id'];
    $products = json_decode($_POST['products'], true);
    $total = 0;
    $discount = 0;
    $final_total = 0;

    foreach ($products as $product) {
        $price = $product['price'];
        $quantity = $product['quantity'];
        $discount_per_item = isset($product['discount']) ? $product['discount'] : 0;

        $total += $price * $quantity;
        $discount += $discount_per_item * $quantity;
        $final_total += ($price - $discount_per_item) * $quantity;
    }

    // Insert into transactions table
    $stmt = $conn->prepare("INSERT INTO transactions (cashier_id, total, discount, final_total, transaction_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iddd", $cashier_id, $total, $discount, $final_total);
    $stmt->execute();
    $transaction_id = $stmt->insert_id;
    $stmt->close();

    // Insert into transaction_profits table and update product quantities
    foreach ($products as $product) {
        $product_id = $product['productId'];
        $quantity = $product['quantity'];
        $selling_price = $product['price'];
        $discount_per_item = isset($product['discount']) ? $product['discount'] : 0;
        $final_selling_price = $selling_price - $discount_per_item;

        // Get buying price from products table
        $stmt = $conn->prepare("SELECT buying_price, quantity FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($buying_price, $current_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($current_quantity < $quantity) {
            // Handle insufficient stock (you can modify this part as needed)
            header("Location: billing.php?error=insufficient_stock");
            exit();
        }

        $profit_loss = ($final_selling_price - $buying_price) * $quantity;

        // Insert into transaction_profits table
        $stmt = $conn->prepare("INSERT INTO transaction_profits (transaction_id, product_id, quantity, selling_price, buying_price, profit_loss, date_added) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiddd", $transaction_id, $product_id, $quantity, $selling_price, $buying_price, $profit_loss);
        $stmt->execute();
        $stmt->close();

        // Update product quantity
        $new_quantity = $current_quantity - $quantity;
        $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $new_quantity, $product_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: billing_success.php?transaction_id=" . $transaction_id);
    exit();
} else {
    header("Location: billing.php");
    exit();
}
?>
