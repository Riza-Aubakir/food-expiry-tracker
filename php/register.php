<?php
require_once 'db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $message = 'Атыңды, дұрыс email және кемінде 8 таңбалы құпиясөзді енгіз.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = 'Бұл email бұрын тіркелген.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash]);
            $message = 'Тіркелу сәтті аяқталды! Енді жүйеге кіре аласың.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тіркелу</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <h1>Азық-түлік бақылау жүйесі</h1>
    <nav>
        <a href="index.php">Басты бет</a>
        <a href="products.php">Өнімдер</a>
        <a href="login.php">Кіру</a>
    </nav>
</header>
<main class="auth-page">
    <section class="auth-card">
        <div class="auth-icon">🥬</div>
        <h2>Қош келдіңіз!</h2>
        <p class="auth-subtitle">Азық-түлік өнімдерінің жарамдылық мерзімін бірге бақылайық.</p>
        <?php if ($message !== ''): ?>
            <p class="form-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="name">Атыңыз</label>
            <input id="name" type="text" name="name" placeholder="Атыңызды енгізіңіз" required maxlength="100">

            <label for="email">Email мекенжайы</label>
            <input id="email" type="email" name="email" placeholder="example@mail.com" required maxlength="150">

            <label for="password">Құпиясөз</label>
            <input id="password" type="password" name="password" placeholder="Кемінде 8 таңба" minlength="8" required>

            <button type="submit">Аккаунт құру →</button>
        </form>
        <p class="auth-footer">Аккаунтыңыз бар ма? <a href="login.php">Жүйеге кіру</a></p>
    </section>
</main>
<script src="../js/script.js"></script>
</body>
</html>
