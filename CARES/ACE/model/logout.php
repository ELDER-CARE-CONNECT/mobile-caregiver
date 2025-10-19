<?php
session_start();
session_unset(); // Xoá các session
session_destroy(); // Hủy session hoàn toàn
header("Location: ../view/login.php");
exit();
