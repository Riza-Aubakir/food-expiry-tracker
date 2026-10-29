<?php
require_once 'db.php';

$stmt = $pdo->query("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    ORDER BY products.expiry_date ASC
");
$products = $stmt->fetchAll();

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Азық-түлік мерзімін бақылау</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <h1>🥗 Азық-түлік бақылау жүйесі</h1>
    <nav>
        <a href="index.php">Басты бет</a>
        <a href="products.php">Өнімдер</a>
        <a href="register.php">Тіркелу</a>
        <a href="login.php">Кіру</a>
        <a href="logout.php">Шығу</a>
    </nav>
</header>

<main>
    <h2>Өнімдердің жарамдылық мерзімі</h2>
    <p>Өнімдеріңнің жарамдылық мерзімін бір жерден бақыла!</p>

    <p><a href="products.php">+ Өнімдерді басқару</a></p>

    <?php if (count($products) === 0): ?>
        <p>Әзірге өнімдер қосылмаған.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Өнім</th>
                <th>Санат</th>
                <th>Саны</th>
                <th>Жарамдылық мерзімі</th>
                <th>Күйі</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category_name'] ?? 'Санат жоқ') ?></td>
                    <td><?= (int)$product['quantity'] ?></td>
                    <td><?= htmlspecialchars($product['expiry_date']) ?></td>
                    <td>
                        <?php if ($product['expiry_date'] < $today): ?>
                            Мерзімі өткен
                        <?php elseif ($product['expiry_date'] === $today): ?>
                            Бүгін аяқталады
                        <?php else: ?>
                            Жарамды
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
