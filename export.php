<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();


// =========================================
// AMBIL DATA PRODUK + JOIN
// =========================================
$sql = "
    SELECT
        p.id,
        p.name,
        c.name AS category,
        s.name AS supplier,
        p.price,
        p.stock
    FROM products p

    JOIN categories c
        ON p.category_id = c.id

    JOIN suppliers s
        ON p.supplier_id = s.id

    ORDER BY p.id ASC
";

$stmt = $pdo->query($sql);

$products = $stmt->fetchAll();


// =========================================
// HEADER FILE CSV
// =========================================
$filename =
    'laporan_inventaris_' .
    date('Y-m-d') .
    '.csv';

header(
    'Content-Type: text/csv; charset=utf-8'
);

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);


// =========================================
// BUKA OUTPUT
// =========================================
$output = fopen(
    'php://output',
    'w'
);


// BOM UTF-8 agar Excel membaca karakter dengan benar
fprintf(
    $output,
    chr(0xEF) .
    chr(0xBB) .
    chr(0xBF)
);


// =========================================
// JUDUL KOLOM
// =========================================
fputcsv(
    $output,
    [
        'ID',
        'Nama Produk',
        'Kategori',
        'Supplier',
        'Harga',
        'Stok'
    ],
    ';'
);


// =========================================
// ISI DATA
// =========================================
foreach ($products as $product) {

    fputcsv(
        $output,
        [
            $product['id'],
            $product['name'],
            $product['category'],
            $product['supplier'],
            $product['price'],
            $product['stock']
        ],
        ';'
    );
}


fclose($output);

exit;
?>