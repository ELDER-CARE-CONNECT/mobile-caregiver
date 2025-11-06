<?php
session_start();

if (!isset($_SESSION['caregiver_id'])) {
    echo "<script>alert('Vui lòng đăng nhập trước!'); window.location.href='login_caregiver.php';</script>";
    exit;
}
?>
