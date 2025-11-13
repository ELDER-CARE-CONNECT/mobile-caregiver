<?php
session_name("CARES_SESSION");
session_start();

if (!isset($_SESSION['caregiver_id']) || empty($_SESSION['caregiver_id'])) {

    // xoá session còn sót lại
    $_SESSION = [];
    session_unset();
    session_destroy();

    header("Location: ../../Admin/login.php");
    exit;
}
?>
