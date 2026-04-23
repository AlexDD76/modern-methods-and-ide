<?php require 'db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');

$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
    foreach ($products as $p) {
        $total += $p['price'] * $cart[$p['id']];
    }
}

// Подтверждение заказа
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    $orderData = [];
    foreach ($products as $p) {
        $orderData[] = [
            'name' => $p['name'],
            'price' => $p['price'],
            'quantity' => $cart[$p['id']],
            'subtotal' => $p['price'] * $cart[$p['id']]
        ];
    }

    $jsonOrder = json_encode($orderData, JSON_UNESCAPED_UNICODE);
    $userId = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, order_data) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $total, $jsonOrder]);

    // Формируем письмо
    $userEmail = $_SESSION['user']['email'];
    $userName = $_SESSION['user']['name'];
    $subject = "Ваш заказ в обувном магазине";
    $message = "Здравствуйте, $userName!\n\nСпасибо за заказ! Состав заказа:\n";
    foreach ($orderData as $item) {
        $message .= "{$item['name']} - {$item['quantity']} шт. x {$item['price']} руб. = {$item['subtotal']} руб.\n";
    }
    $message .= "\nИтого: $total руб.\n\nС уважением, Администрация.";

    mail($userEmail, $subject, $message, "Content-Type: text/plain; charset=utf-8");

    // Очищаем корзину
    unset($_SESSION['cart']);

    // Показываем ответ с поздравлением и содержимым заказа
    $orderHtml = "<h2>Поздравляем с покупкой!</h2><p>Ваш заказ:</p><ul>";
    foreach ($orderData as $item) {
        $orderHtml .= "<li>{$item['name']} x {$item['quantity']} = {$item['subtotal']} руб.</li>";
    }
    $orderHtml .= "</ul><p><strong>Общая сумма: $total руб.</strong></p>";
    $orderHtml .= "<p>Детали отправлены на ваш email: $userEmail</p><a href='index.php'>На главную</a>";

    echo $orderHtml;
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Корзина</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Корзина</h2>
    <?php if (empty($products)): ?>
        <p>Корзина пуста. <a href="index.php">В магазин</a></p>
    <?php else: ?>
        <form method="post">
            <table border="1">
                <tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th></tr>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= $p['price'] ?> руб.</td>
                        <td><?= $cart[$p['id']] ?></td>
                        <td><?= $p['price'] * $cart[$p['id']] ?> руб.</td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <h3>Итого: <?= $total ?> руб.</h3>
            <button type="submit" name="confirm">Подтвердить заказ</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>