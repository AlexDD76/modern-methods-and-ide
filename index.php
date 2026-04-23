<?php require 'db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// Простая корзина в сессии
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Магазин обуви</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <h1>Обувной магазин</h1>
    <div>
        Привет, <?= htmlspecialchars($_SESSION['user']['name']) ?> (<?= $_SESSION['user']['role'] ?>)
        <a href="cart.php">Корзина (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
        <a href="logout.php">Выход</a>
        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="admin.php">Управление товарами</a>
        <?php endif; ?>
    </div>
</div>
<div class="products">
    <?php foreach ($products as $p): ?>
        <div class="product">
            <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= $p['name'] ?>" width="150">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p><?= $p['price'] ?> руб.</p>
            <form method="post">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <button type="submit" name="add_to_cart">В корзину</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>