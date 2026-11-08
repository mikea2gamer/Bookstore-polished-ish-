<?php
    session_start();

    $conn = mysqli_connect("localhost", "root", "", "bookit");
    if (!$conn) {
        die("Database connection failed");
    }

    function h($text) {
        return htmlspecialchars($text);
    }

    // format rupiah
    function rupiah($number) {
        return "Rp " . number_format($number, 0, ",", ".");
    }

    // paksa login
    function need_login() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
    }

    // aku atmin kau user (biar hanya admin bisa akses halaman admin)
    function need_admin() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header("Location: ../login.php");
            exit;
        }
    }
?>
