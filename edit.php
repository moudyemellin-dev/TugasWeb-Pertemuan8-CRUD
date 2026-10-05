<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$error = '';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header('Location: index.php?msg=error');
    exit;
}


// =========================================
// AMBIL PRODUK LAMA
// =========================================
$stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php?msg=error');
    exit;
}


// =========================================
// AMBIL KATEGORI
// =========================================
$categoryStmt = $pdo->query(
    "SELECT id, name
     FROM categories
     ORDER BY name"
);

$categories = $categoryStmt->fetchAll();


// =========================================
// AMBIL SUPPLIER
// =========================================
$supplierStmt = $pdo->query(
    "SELECT id, name
     FROM suppliers
     ORDER BY name"
);

$suppliers = $supplierStmt->fetchAll();


// =========================================
// PROSES UPDATE
// =========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');

    $categoryId =
        (int) ($_POST['category_id'] ?? 0);

    $supplierId =
        (int) ($_POST['supplier_id'] ?? 0);

    $price =
        (float) ($_POST['price'] ?? 0);

    $stock =
        (int) ($_POST['stock'] ?? 0);


    if (
        $name === '' ||
        $categoryId <= 0 ||
        $supplierId <= 0 ||
        $price <= 0 ||
        $stock < 0
    ) {

        $error =
            'Semua data wajib diisi dengan benar.';

    } else {

        try {

            $sql = "
                UPDATE products
                SET
                    name = ?,
                    category_id = ?,
                    supplier_id = ?,
                    price = ?,
                    stock = ?
                WHERE id = ?
            ";

            $updateStmt = $pdo->prepare($sql);

            $updateStmt->execute([
                $name,
                $categoryId,
                $supplierId,
                $price,
                $stock,
                $id
            ]);

            header(
                'Location: index.php?msg=updated'
            );

            exit;

        } catch (PDOException $e) {

            $error =
                'Produk gagal diperbarui.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Produk</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>

<main class="form-container">

    <section class="form-card">

        <div class="form-header">

            <p class="eyebrow">
                CRUD Inventaris
            </p>

            <h1>
                ✏️ Edit Produk
            </h1>

            <p>
                Perbarui data produk.
            </p>

        </div>


        <?php if (!empty($error)): ?>

            <div class="message error">

                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
            class="product-form"
        >

            <div class="form-group">

                <label for="name">
                    Nama Produk
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars(
                        $_POST['name']
                        ?? $product['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category_id">
                    Kategori
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                >

                    <?php foreach (
                        $categories as $category
                    ): ?>

                        <?php
                        $selectedCategory =
                            $_POST['category_id']
                            ?? $product['category_id'];
                        ?>

                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= (int) $selectedCategory
                                === (int) $category['id']
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $category['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="supplier_id">
                    Supplier
                </label>

                <select
                    id="supplier_id"
                    name="supplier_id"
                    required
                >

                    <?php foreach (
                        $suppliers as $supplier
                    ): ?>

                        <?php
                        $selectedSupplier =
                            $_POST['supplier_id']
                            ?? $product['supplier_id'];
                        ?>

                        <option
                            value="<?= (int) $supplier['id'] ?>"
                            <?= (int) $selectedSupplier
                                === (int) $supplier['id']
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $supplier['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="price">
                    Harga
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    min="1"
                    step="0.01"
                    value="<?= htmlspecialchars(
                        $_POST['price']
                        ?? $product['price'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="stock">
                    Stok
                </label>

                <input
                    type="number"
                    id="stock"
                    name="stock"
                    min="0"
                    value="<?= htmlspecialchars(
                        $_POST['stock']
                        ?? $product['stock'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

            </div>


            <div class="form-actions">

                <a
                    href="index.php"
                    class="secondary-btn"
                >
                    ← Kembali
                </a>

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </section>

</main>

</body>

</html>