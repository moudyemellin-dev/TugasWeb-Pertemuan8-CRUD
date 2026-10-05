<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();


// =========================================
// FLASH MESSAGE
// =========================================
$message = '';
$messageType = 'success';

if (isset($_GET['msg'])) {

    if ($_GET['msg'] === 'created') {
        $message = 'Produk berhasil ditambahkan.';
    }

    elseif ($_GET['msg'] === 'updated') {
        $message = 'Produk berhasil diperbarui.';
    }

    elseif ($_GET['msg'] === 'deleted') {
        $message = 'Produk berhasil dihapus.';
    }

    elseif ($_GET['msg'] === 'error') {
        $message = 'Terjadi kesalahan.';
        $messageType = 'error';
    }
}


// =========================================
// PENCARIAN
// =========================================
$search = trim($_GET['search'] ?? '');


// =========================================
// PAGINATION
// =========================================
$limit = 5;

$page = isset($_GET['page'])
    ? (int) $_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;


// =========================================
// HITUNG TOTAL DATA
// =========================================
if ($search !== '') {

    $countSql = "
        SELECT COUNT(*)
        FROM products
        WHERE name LIKE ?
    ";

    $countStmt = $pdo->prepare($countSql);

    $countStmt->execute([
        '%' . $search . '%'
    ]);

} else {

    $countSql = "
        SELECT COUNT(*)
        FROM products
    ";

    $countStmt = $pdo->prepare($countSql);

    $countStmt->execute();
}

$totalProducts = (int) $countStmt->fetchColumn();

$totalPages = max(
    1,
    (int) ceil($totalProducts / $limit)
);


// =========================================
// AMBIL DATA + JOIN 2 TABEL
// =========================================
if ($search !== '') {

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            p.stock,
            c.name AS category,
            s.name AS supplier
        FROM products p

        JOIN categories c
            ON p.category_id = c.id

        JOIN suppliers s
            ON p.supplier_id = s.id

        WHERE p.name LIKE ?

        ORDER BY p.id DESC

        LIMIT ?
        OFFSET ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        1,
        '%' . $search . '%',
        PDO::PARAM_STR
    );

    $stmt->bindValue(
        2,
        $limit,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        3,
        $offset,
        PDO::PARAM_INT
    );

} else {

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            p.stock,
            c.name AS category,
            s.name AS supplier
        FROM products p

        JOIN categories c
            ON p.category_id = c.id

        JOIN suppliers s
            ON p.supplier_id = s.id

        ORDER BY p.id DESC

        LIMIT ?
        OFFSET ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        1,
        $limit,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        2,
        $offset,
        PDO::PARAM_INT
    );
}

$stmt->execute();

$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CRUD Inventaris</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>

<main class="container">

    <header class="page-header">

        <div>

            <p class="eyebrow">
                Tugas Rutin 8
            </p>

            <h1>
                📦 CRUD Inventaris
            </h1>

            <p>
                Kelola data produk, kategori,
                dan supplier.
            </p>

        </div>

        <a
            href="create.php"
            class="primary-btn"
        >
            + Tambah Produk
        </a>

    </header>


    <?php if (!empty($message)): ?>

        <div
            class="message <?= $messageType ?>"
        >
            <?= htmlspecialchars(
                $message,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>


    <section class="toolbar">

        <form
            method="GET"
            action=""
            class="search-form"
        >

            <input
                type="text"
                name="search"
                placeholder="Cari nama produk..."
                value="<?= htmlspecialchars(
                    $search,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <button
                type="submit"
                class="search-btn"
            >
                🔍 Cari
            </button>

            <?php if ($search !== ''): ?>

                <a
                    href="index.php"
                    class="reset-btn"
                >
                    Reset
                </a>

            <?php endif; ?>

        </form>


        <a
            href="export.php"
            class="export-btn"
        >
            📄 Export Laporan
        </a>

    </section>


    <section class="table-card">

        <div class="table-header">

            <h2>
                Daftar Produk
            </h2>

            <span>
                Total:
                <?= $totalProducts ?>
                produk
            </span>

        </div>


        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (empty($products)): ?>

                    <tr>

                        <td
                            colspan="7"
                            class="empty-data"
                        >
                            Data produk tidak ditemukan.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php
                    $number = $offset + 1;
                    ?>

                    <?php foreach ($products as $product): ?>

                        <tr>

                            <td>
                                <?= $number++ ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $product['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $product['category'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $product['supplier'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                Rp
                                <?= number_format(
                                    $product['price'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= (int) $product['stock'] ?>
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="edit.php?id=<?= (int) $product['id'] ?>"
                                        class="edit-btn"
                                    >
                                        ✏️ Edit
                                    </a>


                                    <form
                                        method="POST"
                                        action="delete.php"
                                        onsubmit="return confirm(
                                            'Yakin ingin menghapus produk ini?'
                                        )"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $product['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-btn"
                                        >
                                            🗑️ Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <?php if ($totalPages > 1): ?>

        <nav class="pagination">

            <?php for (
                $i = 1;
                $i <= $totalPages;
                $i++
            ): ?>

                <a
                    href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                    class="<?= $i === $page
                        ? 'active'
                        : '' ?>"
                >
                    <?= $i ?>
                </a>

            <?php endfor; ?>

        </nav>

    <?php endif; ?>

</main>

</body>

</html>