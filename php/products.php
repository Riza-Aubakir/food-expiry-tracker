<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $expiry_date = $_POST['expiry_date'] ?? '';
        $date = DateTime::createFromFormat('Y-m-d', $expiry_date);
        $valid_date = $date && $date->format('Y-m-d') === $expiry_date;
        if ($name === '' || strlen($name) > 150 || !$quantity || $quantity < 1 || !$valid_date) {
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
        if ($id < 1) {
            $message = 'Өнім ID нөмірі дұрыс емес.';
        } else {
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
            $stmt->execute([$id]);
            $message = 'Өнім өшірілді.';
        }
    }
    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $expiry_date = $_POST['expiry_date'] ?? '';
        $date = DateTime::createFromFormat('Y-m-d', $expiry_date);
        $valid_date = $date && $date->format('Y-m-d') === $expiry_date;
        if ($id < 1 || $name === '' || strlen($name) > 150 || !$quantity || $quantity < 1 || !$valid_date) {
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
<?php include 'header.php'; ?>
<main class="dashboard">
    <section class="products-section">
        <div class="section-heading">
            <h2>＋ Жаңа өнім қосу</h2>
            <p>Өнім туралы ақпаратты енгізіп, жарамдылық мерзімін бақылаңыз.</p>
        </div>

        <?php if ($message !== ''): ?>
            <p class="form-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="product-form">
            <input type="hidden" name="action" value="add">

            <label>Өнім атауы</label>
            <input type="text" name="name" placeholder="Мысалы, Сүт" required maxlength="150">
            <label>Санат</label>
            <select name="category_id">
                <option value="0">Санат таңдаңыз</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Саны</label>
            <input type="number" name="quantity" min="1" value="1" required>

            <label>Жарамдылық мерзімі</label>
            <input type="date" name="expiry_date" required>

            <button type="submit">Өнімді қосу →</button>
        </form>
    </section>
    <section class="products-section">
        <div class="section-heading">
            <h2>Өнімдер тізімі</h2>
            <p>Қосылған өнімдерді іздеп, ақпаратын өзгертіңіз.</p>
        </div>
        <form method="get" class="search-form">
            <input type="search" name="search"
                   placeholder="🔍 Өнім атауын іздеу..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Іздеу</button>
            <a href="products.php">Барлығын көрсету</a>
        </form>
        <?php if (!$products): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Өнімдер табылмады</h3>
                <p>Жаңа өнім қосыңыз немесе іздеу сөзін өзгертіңіз.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <div class="product-card-heading">
                            <span class="product-card-icon">🥗</span>
                            <div>
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p><?= htmlspecialchars($product['category_name'] ?? 'Санат жоқ') ?></p>
                            </div>
                        </div>
                        <div class="product-details">
                            <p><strong>Саны:</strong> <?= (int)$product['quantity'] ?></p>
                            <p><strong>Жарамдылық мерзімі:</strong> <?= htmlspecialchars($product['expiry_date']) ?></p>
                        </div>
                        <form method="post" class="edit-form">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">

                            <label>Өнім атауы</label>
                            <input type="text" name="name"
                                   value="<?= htmlspecialchars($product['name']) ?>" required>

                            <label>Саны</label>
                            <input type="number" name="quantity"
                                   value="<?= (int)$product['quantity'] ?>" min="1" required>

                            <label>Жарамдылық мерзімі</label>
                            <input type="date" name="expiry_date"
                                   value="<?= htmlspecialchars($product['expiry_date']) ?>" required>

                            <button type="submit">Өзгерістерді сақтау</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Өнімді өшіресіз бе?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                            <button type="submit" class="delete-button">Өнімді өшіру</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php include 'footer.php'; ?>