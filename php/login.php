<?php
session_start();
require_once 'db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit;
    } else {
        $message = 'Email немесе құпиясөз қате.';
    }
}
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Жүйеге кіру</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <h1>Азық-түлік бақылау жүйесі</h1>
    <nav>
        <a href="index.php">Басты бет</a>
        <a href="products.php">Өнімдер</a>
        <a href="register.php">Тіркелу</a>
    </nav>
</header>
<main>
    <h2>Жүйеге кіру</h2>
    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Email</label><br>
        <input type="email" name="email" required><br>
        <label>Құпиясөз</label><br>
        <input type="password" name="password" required><br>
        <button type="submit">Кіру</button>
    </form>
</main>
</body>
</html>