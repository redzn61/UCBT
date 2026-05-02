<?php
session_start();

if (!isset($_SESSION["student"])) {
    header("Location: portal.html");
    exit;
}

$student = $_SESSION["student"];
?>