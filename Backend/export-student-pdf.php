<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: ../Html/admin-login.html");
    exit;
}

$id = $_GET["id"] ?? "";

$file = __DIR__ . "/data/students.json";
$students = json_decode(file_get_contents($file), true);

$student = null;

foreach ($students as $s) {
    if ($s["id"] === $id) {
        $student = $s;
        break;
    }
}

if (!$student) {
    die("Student not found");
}

$total = 0;
$count = 0;
foreach ($student["courses"] as $c) {
    $total += $c["note"] ?? 0;
    $count++;
}
$avg = $count > 0 ? round($total / $count, 2) : 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Report</title>
<style>
body{font-family:Arial;padding:40px;color:#111}
.header{border-bottom:3px solid #08702f;padding-bottom:20px;margin-bottom:25px}
h1{color:#08702f}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{border:1px solid #ddd;padding:10px;text-align:left}
th{background:#08702f;color:white}
.average{font-size:24px;font-weight:bold;color:#8a0612}
@media print{button{display:none}}
</style>
</head>
<body>

<button onclick="window.print()">Print / Save PDF</button>

<div class="header">
  <h1>Student Report</h1>
  <p><strong>Name:</strong> <?php echo htmlspecialchars($student["name"]); ?></p>
  <p><strong>ID:</strong> <?php echo htmlspecialchars($student["id"]); ?></p>
  <p><strong>Level:</strong> <?php echo htmlspecialchars($student["level"]); ?></p>
  <p class="average">Average: <?php echo $avg; ?>/20</p>
</div>

<table>
<tr>
  <th>Course</th>
  <th>TD/TP</th>
  <th>Exam</th>
  <th>Final</th>
  <th>Absences</th>
</tr>

<?php foreach ($student["courses"] as $c): ?>
<tr>
  <td><?php echo htmlspecialchars($c["name"]); ?></td>
  <td><?php echo $c["td"] ?? 0; ?></td>
  <td><?php echo $c["exam"] ?? 0; ?></td>
  <td><?php echo $c["note"] ?? 0; ?></td>
  <td><?php echo $c["absence"] ?? 0; ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>