<?php
include "../Backend/auth.php";

$studentsFile = "../Backend/data/students.json";
$studentsData = json_decode(file_get_contents($studentsFile), true);

foreach ($studentsData as $freshStudent) {
    if ($freshStudent["id"] === $student["id"]) {
        $student = $freshStudent;
        $_SESSION["student"] = $freshStudent;
        break;
    }
}

$courses = $student["courses"] ?? [];
$timetable = $student["timetable"] ?? [];

$totalAbsences = 0;
$totalNotes = 0;
$notesCount = 0;

foreach ($courses as $course) {
    $totalAbsences += $course["absence"] ?? 0;
    $totalNotes += $course["note"] ?? 0;
    $notesCount++;
}

$average = $notesCount > 0 ? round($totalNotes / $notesCount, 2) : 0;
$photo = $student["photo"] ?? "logo.png";
$status = $average >= 14 ? "Excellent" : ($average >= 10 ? "Validé" : "À améliorer");

foreach ($studentsData as &$s) {
    $total = 0;
    $count = 0;

    foreach ($s["courses"] ?? [] as $c) {
        $total += $c["note"] ?? 0;
        $count++;
    }

    $s["average"] = $count > 0 ? round($total / $count, 2) : 0;
}

usort($studentsData, function($a, $b) {
    return $b["average"] <=> $a["average"];
});

$topStudents = array_slice($studentsData, 0, 5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord étudiant</title>
  <link rel="stylesheet" href="../Css/student-portal.css">
</head>

<body>

<div class="dashboard">

  <aside class="sidebar">
    <div class="profile-mini">
      <img src="../Media/Pictures/<?php echo htmlspecialchars($photo); ?>" alt="Student">
      <h3><?php echo htmlspecialchars($student["name"]); ?></h3>
      <p><?php echo htmlspecialchars($student["level"]); ?></p>
    </div>

    <nav>
      <a href="#" class="active">🏠 Accueil</a>
      <a href="#student-card">💳 Carte étudiant</a>
      <a href="#courses">📚 Cours</a>
      <a href="#absences">📌 Absences</a>
      <a href="#marks">📊 Notes</a>
      <a href="#timetable">🗓️ Emploi du temps</a>
      <a href="#top">🏆 Top 5</a>
      <a href="../Backend/logout.php" class="logout">🚪 Déconnexion</a>
    </nav>
  </aside>

  <main class="main">

    <header class="topbar">
      <div>
        <span class="tag">Portail Étudiant</span>
        <h1>Bienvenue, <?php echo htmlspecialchars($student["name"]); ?></h1>
        <p><?php echo htmlspecialchars($student["group"]); ?> • <?php echo htmlspecialchars($student["section"]); ?></p>
      </div>

      <a href="../Backend/logout.php" class="logout-btn">Déconnexion</a>
    </header>

    <section class="student-card-pro" id="student-card">
      <div class="student-card-left">
        <img src="../Media/Pictures/<?php echo htmlspecialchars($photo); ?>" alt="Student">
      </div>

      <div class="student-card-info">
        <span>Carte Étudiant</span>
        <h2><?php echo htmlspecialchars($student["name"]); ?></h2>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($student["id"]); ?></p>
        <p><strong>Filière:</strong> <?php echo htmlspecialchars($student["field"]); ?></p>
        <p><strong>Niveau:</strong> <?php echo htmlspecialchars($student["level"]); ?></p>
        <p><strong>Groupe:</strong> <?php echo htmlspecialchars($student["group"]); ?></p>
      </div>

      <div class="student-card-code">
        <img src="../Media/Pictures/logo.png" alt="Logo">
        <small><?php echo htmlspecialchars($student["card_number"]); ?></small>
      </div>
    </section>

    <section class="stats-grid">
      <div class="stat-card">
        <span>📚</span>
        <h3><?php echo count($courses); ?></h3>
        <p>Modules</p>
      </div>

      <div class="stat-card green">
        <span>✅</span>
        <h3><?php echo $average; ?></h3>
        <p>Moyenne générale</p>
      </div>

      <div class="stat-card red">
        <span>📌</span>
        <h3><?php echo $totalAbsences; ?></h3>
        <p>Absences</p>
      </div>

      <div class="stat-card gold">
        <span>🏆</span>
        <h3><?php echo $status; ?></h3>
        <p>Statut</p>
      </div>
    </section>

    <?php if ($totalAbsences > 4): ?>
      <section class="panel absence-alert">
        <div class="panel-head">
          <h2>🚨 Alerte absences</h2>
          <span>Important</span>
        </div>
        <p>Vous avez dépassé 4 absences. Veuillez contacter l’administration.</p>
      </section>
    <?php endif; ?>

    <section class="content-grid">
      <div class="panel" id="courses">
        <div class="panel-head">
          <h2>Mes cours</h2>
          <span>Dynamique</span>
        </div>

        <div class="course-list">
          <?php foreach ($courses as $course): ?>
            <div class="course-item">
              <span>📘</span>
              <div>
                <h3><?php echo htmlspecialchars($course["name"]); ?></h3>
                <p>Crédit <?php echo $course["credit"]; ?> • Coef <?php echo $course["coef"]; ?></p>

                <?php if (!empty($course["admin_note"])): ?>
                  <div class="course-admin-note">
                    📝 <?php echo htmlspecialchars($course["admin_note"]); ?>
                  </div>
                <?php endif; ?>
              </div>
              <b><?php echo $course["progress"]; ?>%</b>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="panel absences" id="absences">
        <div class="panel-head">
          <h2>Absences</h2>
          <span>Par module</span>
        </div>

        <?php foreach ($courses as $course):
          $absence = $course["absence"] ?? 0;
          $width = min($absence * 25, 100);
        ?>
          <div class="absence-row">
            <div>
              <h3><?php echo htmlspecialchars($course["name"]); ?></h3>
              <p>
                <?php echo $absence; ?> absence(s)
                <?php if ($absence > 4): ?>
                  <strong class="absence-warning"> — Alerte 🚨</strong>
                <?php endif; ?>
              </p>
            </div>
            <div class="bar <?php echo $absence > 4 ? "warning" : ""; ?>">
              <span style="width:<?php echo $width; ?>%"></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="panel" id="marks">
      <div class="panel-head">
        <h2>Notes TD/TP & Examens (S1)</h2>
        <span>Chaque étudiant</span>
      </div>

      <table>
        <tr>
          <th>Module</th>
          <th>TD/TP</th>
          <th>Exam</th>
          <th>Final</th>
        </tr>

        <?php foreach ($courses as $course): ?>
          <tr>
            <td><?php echo htmlspecialchars($course["name"]); ?></td>
            <td><?php echo $course["td"] ?? 0; ?></td>
            <td><?php echo $course["exam"] ?? 0; ?></td>
            <td class="<?php echo ($course["note"] ?? 0) < 10 ? "bad" : ""; ?>">
              <?php echo $course["note"] ?? 0; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </section>

    <section class="panel timetable-panel" id="timetable">
      <div class="panel-head">
        <h2>🗓️ Emploi du temps (S1)</h2>
        <span>Personnel</span>
      </div>

      <div class="timetable-list">
        <?php foreach ($timetable as $item): ?>
          <div class="time-card">
            <div class="time-box">
              <strong><?php echo htmlspecialchars($item["time"]); ?></strong>
              <span><?php echo htmlspecialchars($item["day"]); ?></span>
            </div>

            <div>
              <h3><?php echo htmlspecialchars($item["course"]); ?></h3>
              <p><?php echo htmlspecialchars($item["type"]); ?> • <?php echo htmlspecialchars($item["room"]); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="panel top-students-panel" id="top">
      <div class="panel-head">
        <h2>🏆 Top 5 Étudiants</h2>
        <span>Classement</span>
      </div>

      <div class="top-students-list">
        <?php foreach ($topStudents as $index => $top): ?>
          <div class="top-student">
            <strong>#<?php echo $index + 1; ?></strong>
            <img src="../Media/Pictures/<?php echo htmlspecialchars($top["photo"] ?? "logo.png"); ?>" alt="Student">

            <div>
              <h3><?php echo htmlspecialchars($top["name"]); ?></h3>
              <p><?php echo htmlspecialchars($top["level"]); ?> • <?php echo htmlspecialchars($top["field"]); ?></p>
            </div>

            <span><?php echo $top["average"]; ?>/20</span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

  </main>

</div>

</body>
</html>