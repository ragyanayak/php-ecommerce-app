<?php
session_start();

$products = [
    [
        "id" => 1,
        "name" => "Wireless Headphones",
        "price" => 1499,
        "image" => "🎧"
    ],
    [
        "id" => 2,
        "name" => "Smart Watch",
        "price" => 2299,
        "image" => "⌚"
    ],
    [
        "id" => 3,
        "name" => "Running Shoes",
        "price" => 1899,
        "image" => "👟"
    ],
    [
        "id" => 4,
        "name" => "Laptop Backpack",
        "price" => 999,
        "image" => "🎒"
    ]
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $productId = filter_input(
        INPUT_POST,
        "product_id",
        FILTER_VALIDATE_INT
    );

    foreach ($products as $product) {
        if ($productId === $product["id"]) {
            $_SESSION["cart"][$productId] =
                ($_SESSION["cart"][$productId] ?? 0) + 1;
            break;
        }
    }

    header("Location: /");
    exit;
}

$cartCount = array_sum($_SESSION["cart"] ?? []);

function escapeHtml($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShopLite - Online Shopping</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h2>ShopLite</h2>

    <nav>
        <a href="/">Home</a>
        <a href="/cart.php">
            My Cart (<?= (int)$cartCount ?>)
        </a>
    </nav>
</header>

<section class="banner">
    <h1>Welcome to ShopLite</h1>
    <p>Discover amazing products at great prices!</p>
</section>

<div class="container">

    <h2>Our Products</h2>

    <div class="products">

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <div class="product-image">
                    <?= escapeHtml($product["image"]) ?>
                </div>

                <h3>
                    <?= escapeHtml($product["name"]) ?>
                </h3>

                <h4>
                    ₹<?= number_format($product["price"]) ?>
                </h4>

                <form method="POST">

                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= (int)$product["id"] ?>"
                    >

                    <button type="submit">
                        Add to Cart
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<footer>
    <p>ShopLite E-commerce Demo | DevOps Project</p>
</footer>

</body>
</html>
