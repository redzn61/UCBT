<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: ../Html/admin-login.html");
    exit;
}

$studentId = $_POST["student_id"] ?? "";
$courseName = $_POST["course_name"] ?? "";

$absence = intval($_POST["absence"] ?? 0);
$td = floatval($_POST["td"] ?? 0);
$exam = floatval($_POST["exam"] ?? 0);
$progress = intval($_POST["progress"] ?? 0);

$finalNote = round(($td * 0.4) + ($exam * 0.6), 2);

$file = __DIR__ . "/data/students.json";
$students = json_decode(file_get_contents($file), true);

foreach ($students as &$student) {
    if ($student["id"] === $studentId) {
        foreach ($student["courses"] as &$course) {
            if ($course["name"] === $courseName) {
                $course["absence"] = $absence;
                $course["td"] = $td;
                $course["exam"] = $exam;
                $course["progress"] = $progress;
                $course["note"] = $finalNote;
                break;
            }
        }
        break;
    }
}

file_put_contents(
    $file,
    json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

header("Location: ../Html/admin-dashboard.php?updated=1");
exit;
?>