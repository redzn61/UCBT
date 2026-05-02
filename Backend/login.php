<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Html/portal.php");
    exit;
}

$studentId = trim($_POST["student_id"] ?? "");
$password = trim($_POST["password"] ?? "");

$studentsFile = __DIR__ . "/data/students.json";

if (!file_exists($studentsFile)) {
    header("Location: ../Html/portal.php?error=missing_data");
    exit;
}

$students = json_decode(file_get_contents($studentsFile), true);

foreach ($students as $student) {
    if ($student["id"] === $studentId && $student["password"] === $password) {
        $_SESSION["student"] = $student;
        header("Location: ../Html/student-dashboard.php");
        exit;
    }
}

header("Location: ../Html/portal.php?error=invalid");
exit;
?>