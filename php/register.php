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
<main>
    <h2>Тіркелу</h2>
    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Атың</label><br>
        <input type="text" name="name" required maxlength="100"><br>
        <label>Email</label><br>
        <input type="email" name="email" required maxlength="150"><br>
        <label>Құпиясөз (кемінде 8 таңба)</label><br>
        <input type="password" name="password" minlength="8" required><br>
        <button type="submit">Тіркелу</button>
    </form>
</main>
</body>
</html>
