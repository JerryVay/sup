<?php
session_start();
if (!isset($_SESSION['cashier_id'])) {
    header("Location: cashier_login.php");
    exit();
}

include 'config.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .header {
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .container {
            display: flex;
            flex-direction: row;
            width: 100%;
            padding: 20px;
        }
        .product-list {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            max-height: 600px;
        }
        .billing-container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product-list table, .billing-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .product-list th, .product-list td, .billing-container th, .billing-container td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .product-list th, .billing-container th {
            background-color: #f2f2f2;
        }
        .product-list tr:hover {
            cursor: pointer;
            background-color: #f9f9f9;
        }
        .highlight {
            background-color: #ff0;
        }
        .letter-nav {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .letter-nav button {
            margin: 2px;
            padding: 5px 10px;
            background-color: #ddd;
            border: none;
            cursor: pointer;
        }
        .letter-nav button:hover {
            background-color: #bbb;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'cashierheader.php'; ?>
    <div class="container">
        <div class="product-list">
            <h2>Product List</h2>
            <div class="letter-nav">
                <?php
                foreach (range('A', 'Z') as $letter) {
                    echo "<button class='letter-btn' data-letter='$letter'>$letter</button>";
                }
                ?>
            </div>
            <table id="products-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Available Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT product_id, product_name, price, quantity FROM products";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr data-product-id='{$row['product_id']}' data-product-name='{$row['product_name']}' data-price='{$row['price']}' data-quantity='{$row['quantity']}'>";
                            echo "<td>{$row['product_name']}</td>";
                            echo "<td>{$row['price']}</td>";
                            echo "<td>{$row['quantity']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No products available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="billing-container">
            <h2>Bill</h2>
            <table id="billing-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Discount</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div>
                <strong>Total: </strong> <span id="total-price">0.00</span>
            </div>
            <form id="billing-form" method="post" action="process_billing.php">
                <input type="hidden" name="products" id="products-input">
                <button type="submit">Complete Purchase</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let totalPrice = 0;
            const productsInBilling = {};

            $("#products-table tbody").on("click", "tr", function() {
                const productId = $(this).data("product-id");
                const productName = $(this).data("product-name");
                const price = parseFloat($(this).data("price"));
                let quantity = parseInt($(this).data("quantity"));

                if (quantity <= 0) {
                    alert("This product is out of stock.");
                    return;
                }

                if (productsInBilling[productId]) {
                    alert("This product is already in the billing list.");
                    return;
                }

                const billingRow = `
                    <tr data-product-id="${productId}">
                        <td>${productName}</td>
                        <td>${price.toFixed(2)}</td>
                        <td><input type="number" min="1" max="${quantity}" value="1" class="billing-quantity"></td>
                        <td><input type="number" min="0" value="0" class="billing-discount"></td>
                        <td class="total-price">${price.toFixed(2)}</td>
                    </tr>
                `;

                $("#billing-table tbody").append(billingRow);

                productsInBilling[productId] = { productId, productName, price, discount: 0, quantity: 1 };
                totalPrice += price;
                updateTotalPrice();
            });

            $("#billing-table").on("input", ".billing-quantity, .billing-discount", function() {
                const row = $(this).closest("tr");
                const productId = row.data("product-id");
                const price = parseFloat(row.find("td:nth-child(2)").text());
                const discount = parseFloat(row.find(".billing-discount").val()) || 0;
                let newQuantity = parseInt(row.find(".billing-quantity").val());
                const maxQuantity = parseInt(row.find(".billing-quantity").attr("max"));

                if (isNaN(newQuantity) || newQuantity < 1) {
                    newQuantity = 0; // Treat empty or invalid input as zero
                }

                if (newQuantity > maxQuantity) {
                    alert("Quantity exceeds available stock.");
                    row.find(".billing-quantity").val(productsInBilling[productId].quantity);
                    return;
                }

                const oldQuantity = productsInBilling[productId].quantity;
                const difference = newQuantity - oldQuantity;
                productsInBilling[productId].quantity = newQuantity;
                productsInBilling[productId].discount = discount;

                totalPrice += difference * (price - discount);

                const total = newQuantity * (price - discount);
                row.find(".total-price").text(total.toFixed(2));
                updateTotalPrice();
            });

            $("#billing-form").submit(function(event) {
                const billingTable = $("#billing-table tbody");
                const hasZeroQuantity = $("#billing-table .billing-quantity").toArray().some(input => parseInt($(input).val()) <= 0);

                if (billingTable.children("tr").length === 0 || hasZeroQuantity) {
                    alert("The cart is empty or contains items with zero quantity.");
                    event.preventDefault();
                    window.location.href = "billing.php";
                } else {
                    const productsArray = Object.values(productsInBilling);
                    $("#products-input").val(JSON.stringify(productsArray));
                }
            });

            function updateTotalPrice() {
                $("#total-price").text(totalPrice.toFixed(2));
            }

            $(".letter-btn").click(function() {
                const letter = $(this).data("letter");
                highlightProducts(letter);
            });

            $(document).on("keypress", function(event) {
                const letter = String.fromCharCode(event.which).toUpperCase();
                if (letter >= 'A' && letter <= 'Z') {
                    highlightProducts(letter);
                }
            });

            function highlightProducts(letter) {
                $("#products-table tbody tr").each(function() {
                    const productName = $(this).data("product-name");
                    if (productName.startsWith(letter)) {
                        $(this).addClass("highlight");
                    } else {
                        $(this).removeClass("highlight");
                    }
                });
            }
        });
    </script>
</body>
</html>
