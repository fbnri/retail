<?php

include "koneksi.php";
include 'config.php';

if (!isset($_SESSION['name'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];

if (isset($_POST['delete'])) {
    $sql = "DELETE FROM category WHERE id_category='$id'";
    mysqli_query($link, $sql);

    session_start();
    $_SESSION['message'] = "Data berhasil dihapus!";

    header("Location: management_category.php");
    exit;
}
?>
