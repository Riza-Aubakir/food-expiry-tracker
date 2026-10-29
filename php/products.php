<?php
require_once 'db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $expiry_date = $_POST['expiry_date'] ?? '';
        if ($name === '' || !$quantity || $quantity < 1 || $expiry_date === '') {
            $message = 'Барлық өрісті дұрыс толтырыңыз.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO products (name, category_id, quantity, expiry_date)
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $name,
                $category_id ?: null,
                $quantity,
                $expiry_date
            ]);
            $message = 'Өнім сәтті қосылды!';
        }
    }
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $message = 'Өнім өшірілді.';
    }
    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $expiry_date = $_POST['expiry_date'] ?? '';
        if ($id < 1 || $name === '' || !$quantity || $quantity < 1 || $expiry_date === '') {
            $message = 'Өнім мәліметтерін дұрыс толтырыңыз.';
        } else {
            $stmt = $pdo->prepare(
                'UPDATE products SET name = ?, quantity = ?, expiry_date = ? WHERE id = ?'
            );
            $stmt->execute([$name, $quantity, $expiry_date, $id]);
            $message = 'Өнім мәліметтері жаңартылды.';
        }
    }
}
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.name LIKE ?
         ORDER BY p.expiry_date ASC'
    );
    $stmt->execute(['%' . $search . '%']);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         ORDER BY p.expiry_date ASC'
    )->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Өнімдерді басқару</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <h1>🥗 Өнімдерді басқару</h1>
    <nav>
        <a href="index.php">Басты бет</a>
        <a href="products.php">Өнімдер</a>
        <a href="register.php">Тіркелу</a>
        <a href="login.php">Кіру</a>
    </nav>
</header>
<main>
    <h2>Жаңа өнім қосу</h2>
    <?php if ($message !== ''): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <label>Өнім атауы</label><br>
        <input type="text" name="name" required maxlength="150"><br>
        <label>Санат</label><br>
        <select name="category_id">
            <option value="0">Санат таңдаңыз</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <label>Саны</label><br>
        <input type="number" name="quantity" min="1" value="1" required><br>
        <label>Жарамдылық мерзімі</label><br>
        <input type="date" name="expiry_date" required><br>
        <button type="submit">Өнімді қосу</button>
    </form>
    <hr>
    <h2>Өнімдер тізімі</h2>
    <form method="get">
        <input type="search" name="search"
               placeholder="Өнім атауын іздеу"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Іздеу</button>
        <a href="products.php">Барлығын көрсету</a>
    </form>
    <?php if (!$products): ?>
        <p>Өнімдер табылмады.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <section>
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p>Санат: <?= htmlspecialchars($product['category_name'] ?? 'Санат жоқ') ?></p>
                <p>Саны: <?= (int)$product['quantity'] ?></p>
                <p>Жарамдылық мерзімі: <?= htmlspecialchars($product['expiry_date']) ?></p>
                <form method="post">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($product['name']) ?>" required>
                    <input type="number" name="quantity"
                           value="<?= (int)$product['quantity'] ?>" min="1" required>
                    <input type="date" name="expiry_date"
                           value="<?= htmlspecialchars($product['expiry_date']) ?>" required>
                    <button type="submit">Өзгерту</button>
                </form>
                <form method="post" onsubmit="return confirm('Өнімді өшіресіз бе?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                    <button type="submit">Өшіру</button>
                </form>
            </section>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>
