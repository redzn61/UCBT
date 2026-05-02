<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: ../Html/admin-login.html");
    exit;
}

$id = $_POST["student_id"] ?? "";

$file = __DIR__ . "/data/students.json";
$students = json_decode(file_get_contents($file), true);

$students = array_values(array_filter($students, function($student) use ($id) {
    return $student["id"] !== $id;
}));

file_put_contents($file, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: ../Html/admin-dashboard.php?deleted=1");
exit;
?>