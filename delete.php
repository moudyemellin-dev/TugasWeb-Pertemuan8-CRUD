<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();


// Hanya boleh diproses melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php?msg=error');
    exit;
}


$id = isset($_POST['id'])
    ? (int) $_POST['id']
    : 0;


if ($id <= 0) {

    header('Location: index.php?msg=error');
    exit;
}


try {

    // =====================================
    // MULAI TRANSACTION
    // =====================================
    $pdo->beginTransaction();


    // =====================================
    // AMBIL DATA PRODUK
    // =====================================
    $productStmt = $pdo->prepare(
        "SELECT id, name
         FROM products
         WHERE id = ?"
    );

    $productStmt->execute([$id]);

    $product = $productStmt->fetch();


    if (!$product) {

        throw new Exception(
            'Produk tidak ditemukan.'
        );
    }


    // =====================================
    // CATAT LOG AKTIVITAS
    // =====================================
    $logSql = "
        INSERT INTO activity_logs
        (
            product_id,
            action,
            description
        )
        VALUES (?, ?, ?)
    ";

    $logStmt = $pdo->prepare($logSql);

    $description =
        'Produk "' .
        $product['name'] .
        '" dihapus dari inventaris.';

    $logStmt->execute([
        $id,
        'DELETE',
        $description
    ]);


    // =====================================
    // HAPUS PRODUK
    // =====================================
    $deleteStmt = $pdo->prepare(
        "DELETE FROM products
         WHERE id = ?"
    );

    $deleteStmt->execute([$id]);


    // =====================================
    // SEMUA BERHASIL
    // =====================================
    $pdo->commit();


    header(
        'Location: index.php?msg=deleted'
    );

    exit;


} catch (Exception $e) {

    // =====================================
    // JIKA ADA ERROR, BATALKAN SEMUA
    // =====================================
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    header(
        'Location: index.php?msg=error'
    );

    exit;
}
?>