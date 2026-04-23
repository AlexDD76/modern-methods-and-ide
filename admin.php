<?php require 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Удаление товара
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: admin.php');
    exit;
}

// Добавление товара
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = 'default.jpg';

    if (!empty($_FILES['image']['name'])) {
        $target = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image = $_FILES['image']['name'];
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
    $stmt->execute([$name, $price, $image]);
    header('Location: admin.php');
    exit;
}

$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Управление товарами</h2>
    <a href="index.php">На главную</a>
    <hr>

    <h3>Добавить товар</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Название" required>
        <input type="number" step="0.01" name="price" placeholder="Цена" required>
        <input type="file" name="image">
        <button type="submit" name="add">Добавить</button>
    </form>

    <h3>Список товаров</h3>
    <table border="1">
        <tr><th>ID</th><th>Фото</th><th>Название</th><th>Цена</th><th>Действие</th></tr>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><img src="uploads/<?= htmlspecialchars($p['image']) ?>" width="50"></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= $p['price'] ?> руб.</td>
                <td><a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Удалить?')">Удалить</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>