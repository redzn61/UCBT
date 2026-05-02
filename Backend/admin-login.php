<?php
session_start();

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

if ($username === "admin" && $password === "admin123") {
    $_SESSION["admin"] = true;
    header("Location: ../Html/admin-dashboard.php");
    exit;
}

header("Location: ../Html/admin-login.html?error=1");
exit;
?>