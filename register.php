<?php require 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Регистрация</h2>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);
            echo "<p class='success'>Регистрация успешна! <a href='login.php'>Войти</a></p>";
        } catch(PDOException $e) {
            echo "<p class='error'>Email уже используется</p>";
        }
    }
    ?>
    <form method="post">
        <input type="text" name="name" placeholder="Имя" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
    <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
</div>
</body>
</html>