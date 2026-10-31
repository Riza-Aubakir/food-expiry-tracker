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
<main class="dashboard">
    <section class="welcome-banner">
        <div>
            <p class="welcome-label">ҚОШ КЕЛДІҢІЗ!</p>
            <h2>Азық-түлік бақылау жүйесі</h2>
            <p>Өнімдеріңіздің жарамдылық мерзімін оңай бақылап, тағам қалдығын азайтыңыз.</p>
            <a class="primary-button" href="products.php">＋ Өнімдерді басқару</a>
        </div>
        <div class="welcome-emoji">🥬</div>
    </section>
    <section class="products-section">
        <div class="section-heading">
            <div>
                <h2>Өнімдер тізімі</h2>
                <p>Өнімдердің жарамдылық мерзімі мен күйін бақылаңыз.</p>
            </div>
        </div>
        <?php if (count($products) === 0): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Әзірге өнімдер жоқ</h3>
                <p>Бақылауды бастау үшін алғашқы өніміңізді қосыңыз.</p>
                <a class="primary-button" href="products.php">＋ Өнім қосу</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Өнім</th>
                            <th>Санат</th>
                            <th>Саны</th>
                            <th>Жарамдылық мерзімі</th>
                            <th>Күйі</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="product-name"><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'Санат жоқ') ?></td>
                                <td><?= (int)$product['quantity'] ?></td>
                                <td><?= htmlspecialchars($product['expiry_date']) ?></td>
                                <td>
                                    <?php if ($product['expiry_date'] < $today): ?>
                                        <span class="status status-expired">Мерзімі өткен</span>
                                    <?php elseif ($product['expiry_date'] === $today): ?>
                                        <span class="status status-today">Бүгін аяқталады</span>
                                    <?php else: ?>
                                        <span class="status status-valid">Жарамды</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
