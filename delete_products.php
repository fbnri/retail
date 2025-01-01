<?php

session_start();

include "koneksi.php";
include 'config.php';

if (!isset($_SESSION['name'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM product_management WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Produk berhasil dihapus!";
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat menghapus produk.";
    }
    mysqli_stmt_close($stmt);

    header("Location: management_products.php");
    exit;
} else {
    $_SESSION['message'] = "Permintaan tidak valid.";
    header("Location: management_products.php");
    exit;
}

?>
