<?php
session_start();
if ($_SESSION['role'] != 1) {
    // Nếu không phải admin, chuyển về trang login
    header('Location: ../Public/view/login.php');
    exit();
}
?>