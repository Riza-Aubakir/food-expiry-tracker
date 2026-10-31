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
<main class="auth-page">
    <section class="auth-card">
        <div class="auth-icon">🥬</div>

        <h2>Қайта оралғаныңызға қуаныштымыз!</h2>
        <p class="auth-subtitle">
            Өнімдеріңіздің жарамдылық мерзімін бақылау үшін жүйеге кіріңіз.
        </p>
        <?php if ($message !== ''): ?>
            <p class="form-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="email">Email мекенжайы</label>
            <input
                id="email"
                type="email"
                name="email"
                placeholder="example@mail.com"
                required
            >
            <label for="password">Құпиясөз</label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="Құпиясөзіңізді енгізіңіз"
                required
            >
            <button type="submit">Жүйеге кіру →</button>
        </form>
        <p class="auth-footer">
            Аккаунтыңыз жоқ па?
            <a href="register.php">Тіркелу</a>
        </p>
    </section>
</main>
<script src="../js/script.js"></script>
</body>
</html>