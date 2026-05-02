<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: ../Html/admin-login.html");
    exit;
}

$file = __DIR__ . "/data/students.json";
$students = json_decode(file_get_contents($file), true);

$code = trim($_POST["code"] ?? "");
$name = trim($_POST["name"] ?? "");
$group = trim($_POST["group"] ?? "Groupe 2");
$section = trim($_POST["section"] ?? "Section A");

if ($code === "" || $name === "") {
    header("Location: ../Html/admin-dashboard.php");
    exit;
}

$id = "2023" . $code;

foreach ($students as $student) {
    if ($student["id"] === $id) {
        header("Location: ../Html/admin-dashboard.php");
        exit;
    }
}

function makeCourse($name, $credit, $coef) {
    return [
        "name" => $name,
        "credit" => $credit,
        "coef" => $coef,
        "progress" => 0,
        "absence" => 0,
        "td" => 0,
        "exam" => 0,
        "note" => 0,
        "admin_note" => ""
    ];
}

$students[] = [
    "id" => $id,
    "password" => $code,
    "name" => strtoupper($name),
    "level" => "L2 Informatique",
    "field" => "Informatique",
    "group" => $group,
    "section" => $section,
    "photo" => "logo.png",
    "card_number" => "UCB-2023-" . $code,
    "courses" => [
        makeCourse("Algorithmes et structures de données", 6, 3),
        makeCourse("Architecture d’ordinateur", 4, 2),
        makeCourse("Système Information", 4, 2),
        makeCourse("Théorie de graphes", 5, 2),
        makeCourse("Méthode Numérique", 5, 2),
        makeCourse("Logique mathématique", 6, 3)
    ],
    "timetable" => [
        ["day" => "Dimanche", "time" => "08:00 - 09:30", "course" => "Algorithmes et structures de données", "room" => "Salle B12", "type" => "Cours"],
        ["day" => "Dimanche", "time" => "10:00 - 11:30", "course" => "Architecture d’ordinateur", "room" => "Salle C04", "type" => "TD"],
        ["day" => "Lundi", "time" => "08:00 - 09:30", "course" => "Système Information", "room" => "Salle A08", "type" => "Cours"],
        ["day" => "Mardi", "time" => "10:00 - 11:30", "course" => "Théorie de graphes", "room" => "Amphi 2", "type" => "Cours"],
        ["day" => "Mercredi", "time" => "08:00 - 09:30", "course" => "Méthode Numérique", "room" => "Lab 1", "type" => "TP"],
        ["day" => "Jeudi", "time" => "10:00 - 11:30", "course" => "Logique mathématique", "room" => "Salle B05", "type" => "TD"]
    ]
];

file_put_contents($file, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: ../Html/admin-dashboard.php?added=1");
exit;
?>