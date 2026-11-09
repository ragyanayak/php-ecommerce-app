<?php

session_start();

$products = [
    1 => ["name" => "Wireless Headphones", "price" => 1499],
    2 => ["name" => "Smart Watch", "price" => 2299],
    3 => ["name" => "Running Shoes", "price" => 1899],
    4 => ["name" => "Laptop Backpack", "price" => 999]
];

if (isset($_POST["clear"])) {
    unset($_SESSION["cart"]);
    header("Location: /cart.php");
    exit;
}

$cart = $_SESSION["cart"] ?? [];
$total = 0;

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h2>ShopLite</h2>
    <a href="/">Continue Shopping</a>
</header>

<div class="container">

    <h1>My Shopping Cart</h1>

    <?php if (empty($cart)): ?>

        <p>Your cart is empty.</p>

    <?php else: ?>

        <?php foreach ($cart as $id => $quantity): ?>

            <?php if (!isset($products[$id])) continue; ?>

            <?php
                $price = $products[$id]["price"];
                $subtotal = $price * $quantity;
                $total += $subtotal;
            ?>

            <div class="cart-item">

                <h3>
                    <?= htmlspecialchars($products[$id]["name"]) ?>
                </h3>

                <p>Quantity: <?= (int)$quantity ?></p>

                <p>
                    Price: ₹<?= number_format($subtotal) ?>
                </p>

            </div>

        <?php endforeach; ?>

        <h2>Total: ₹<?= number_format($total) ?></h2>

        <form method="POST">
            <button name="clear" value="1">
                Clear Cart
            </button>
        </form>

        <p>Demo only: No real checkout or payment processing.</p>

    <?php endif; ?>

</div>

</body>
</html>
