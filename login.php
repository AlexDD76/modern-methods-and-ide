<?php require 'db.php';
    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
}
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
                header('Location: index.php');
                exit;
            } else {
                echo "<p class='error'>Неверный email или пароль</p>";
            }
        }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Вход</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Регистрация</a></p>
</div>
</body>
</html>