<?php

session_start();
include "koneksi.php";
include 'config.php';

if (!isset($_SESSION['name'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['delete'])) {
    $transaction_id = $_POST['id'];

    mysqli_begin_transaction($link);
    try {
        $delete_items_stmt = $link->prepare("DELETE FROM transaction_items WHERE transaction_id = ?");
        $delete_transaction_stmt = $link->prepare("DELETE FROM transaction WHERE id = ?");

        $delete_items_stmt->bind_param("i", $transaction_id);
        $delete_transaction_stmt->bind_param("i", $transaction_id);

        $delete_items_stmt->execute();
        $delete_transaction_stmt->execute();

        mysqli_commit($link);
        $_SESSION['message'] = "Transaksi berhasil dihapus.";
    } catch (Exception $e) {
        mysqli_rollback($link);
        $_SESSION['message'] = "Gagal menghapus transaksi: " . $e->getMessage();
    }

    $delete_items_stmt->close();
    $delete_transaction_stmt->close();

    header("Location: transaction.php");
    exit;
}
?>