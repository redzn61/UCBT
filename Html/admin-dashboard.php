<?php
include "../Backend/admin-auth.php";

$file = "../Backend/data/students.json";
$students = json_decode(file_get_contents($file), true);

$search = strtolower(trim($_GET["search"] ?? ""));

foreach ($students as &$student) {
    $total = 0;
    $count = 0;
    $absences = 0;

    foreach ($student["courses"] ?? [] as $course) {
        $total += $course["note"] ?? 0;
        $count++;
        $absences += $course["absence"] ?? 0;
    }

    $student["average"] = $count > 0 ? round($total / $count, 2) : 0;
    $student["total_absences"] = $absences;
}

$ranked = $students;

usort($ranked, function($a, $b) {
    return $b["average"] <=> $a["average"];
});

$filtered = array_filter($students, function($student) use ($search) {
    if ($search === "") return false;

    return str_contains(strtolower($student["name"]), $search)
        || str_contains(strtolower($student["id"]), $search);
});

$totalStudents = count($students);
$passed = 0;
$failed = 0;
$excellent = 0;
$alerts = 0;

foreach ($students as $student) {
    if ($student["average"] >= 10) {
        $passed++;
    } else {
        $failed++;
    }

    if ($student["average"] >= 14) {
        $excellent++;
    }

    if ($student["total_absences"] > 4) {
        $alerts++;
    }
}

$passPercent = $totalStudents > 0 ? round(($passed / $totalStudents) * 100) : 0;
$failPercent = $totalStudents > 0 ? round(($failed / $totalStudents) * 100) : 0;
$excellentPercent = $totalStudents > 0 ? round(($excellent / $totalStudents) * 100) : 0;
$alertPercent = $totalStudents > 0 ? round(($alerts / $totalStudents) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../Css/admin.css">
</head>

<body>

<div class="admin-layout">

  <aside class="admin-sidebar">
    <h2>UCB Admin</h2>

    <a class="active" href="#">📊 Dashboard</a>
    <a href="#add">➕ Ajouter étudiant</a>
    <a href="#search">🔍 Recherche</a>
    <a href="#ranking">🏆 Classement</a>
    <a href="#charts">📈 Analyse</a>
    <a href="../Backend/admin-logout.php" class="logout">🚪 Logout</a>
    <div class="sidebar-showcase">
  <div class="pulse-ring"></div>

  </aside>

  <main class="admin-main">

    <header class="admin-header">
      <span class="tag">Administration</span>
      <h1>Centre de contrôle universitaire</h1>
      <p>Gestion professionnelle des étudiants, notes, absences, classement et rapports.</p>
    </header>

    <?php if (isset($_GET["updated"])): ?>
      <div class="success-box">✅ Données mises à jour avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["added"])): ?>
      <div class="success-box">✅ Étudiant ajouté avec succès.</div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
      <div class="success-box">✅ Étudiant supprimé avec succès.</div>
    <?php endif; ?>

    <section class="admin-stats pro-stats">
      <div>
        <span>👨‍🎓</span>
        <h3><?php echo $totalStudents; ?></h3>
        <p>Étudiants</p>
      </div>

      <div>
        <span>✅</span>
        <h3><?php echo $passed; ?></h3>
        <p>Validés</p>
      </div>

      <div>
        <span>❌</span>
        <h3><?php echo $failed; ?></h3>
        <p>En difficulté</p>
      </div>

      <div>
        <span>🏆</span>
        <h3><?php echo $excellent; ?></h3>
        <p>Excellents</p>
      </div>

      <div>
        <span>🚨</span>
        <h3><?php echo $alerts; ?></h3>
        <p>Alertes absence</p>
      </div>
    </section>

    <section class="charts-section" id="charts">
      <div class="section-admin-head">
        <span class="tag">Analyse</span>
        <h2>Graphiques dynamiques</h2>
      </div>

      <div class="charts-grid">

        <div class="chart-card">
          <h3>📊 Analyse des étudiants</h3>

          <div class="bar-chart-pro">
            <div>
              <span style="height:<?php echo $passPercent; ?>%"></span>
              <b><?php echo $passed; ?></b>
              <p>Validés</p>
            </div>

            <div>
              <span class="red-bar" style="height:<?php echo $failPercent; ?>%"></span>
              <b><?php echo $failed; ?></b>
              <p>Faibles</p>
            </div>

            <div>
              <span class="gold-bar" style="height:<?php echo $excellentPercent; ?>%"></span>
              <b><?php echo $excellent; ?></b>
              <p>Excellents</p>
            </div>

            <div>
              <span class="alert-bar" style="height:<?php echo $alertPercent; ?>%"></span>
              <b><?php echo $alerts; ?></b>
              <p>Alertes</p>
            </div>
          </div>
        </div>

        <div class="chart-card">
          <h3>🥧 Taux de réussite</h3>

          <div class="pie-chart" style="--pass:<?php echo $passPercent; ?>"></div>

          <div class="chart-legend">
            <span><b class="green-dot"></b> Réussite <?php echo $passPercent; ?>%</span>
            <span><b class="red-dot"></b> Échec <?php echo $failPercent; ?>%</span>
          </div>
        </div>

      </div>
    </section>

    <section class="add-student-section" id="add">
      <div class="section-admin-head">
        <span class="tag">Nouveau</span>
        <h2>➕ Ajouter un étudiant</h2>
      </div>

      <form class="add-student-form" action="../Backend/add-student.php" method="POST">
        <input type="text" name="code" placeholder="Code étudiant ex: 2676" required>
        <input type="text" name="name" placeholder="Nom complet" required>
        <input type="text" name="group" placeholder="Groupe" value="Groupe 2">
        <input type="text" name="section" placeholder="Section" value="Section A">
        <button type="submit">Ajouter</button>
      </form>
    </section>

    <section class="search-section" id="search">
      <div class="section-admin-head">
        <span class="tag">Recherche</span>
        <h2>🔍 Rechercher puis modifier</h2>
      </div>

      <form class="search-box" method="GET">
        <input type="text" name="search" placeholder="Nom ou ID étudiant..." value="<?php echo htmlspecialchars($_GET["search"] ?? ""); ?>">
        <button type="submit">Rechercher</button>
        <a href="admin-dashboard.php">Reset</a>
      </form>
    </section>

    <?php if ($search !== ""): ?>
      <section class="students-list">

        <?php if (count($filtered) === 0): ?>
          <div class="student-admin-card">
            <h2>Aucun étudiant trouvé.</h2>
          </div>
        <?php endif; ?>

        <?php foreach ($filtered as $student): ?>
          <div class="student-admin-card">

            <div class="student-admin-head advanced-head">
              <div class="student-admin-profile">
                <img src="../Media/Pictures/<?php echo htmlspecialchars($student["photo"] ?? "logo.png"); ?>" alt="Student">

                <div>
                  <h2><?php echo htmlspecialchars($student["name"]); ?></h2>
                  <p>
                    ID: <?php echo htmlspecialchars($student["id"]); ?> •
                    <?php echo htmlspecialchars($student["level"]); ?> •
                    <?php echo htmlspecialchars($student["group"]); ?>
                  </p>
                </div>
              </div>

              <div class="student-actions">
                <a href="../Backend/export-student-pdf.php?id=<?php echo urlencode($student["id"]); ?>" class="pdf-btn" target="_blank">📄 PDF</a>

                <form action="../Backend/delete-student.php" method="POST">
                  <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student["id"]); ?>">
                  <button type="submit" class="delete-btn">🗑 Delete</button>
                </form>
              </div>
            </div>

            <?php if ($student["total_absences"] > 4): ?>
              <div class="admin-alert">🚨 Cet étudiant a dépassé 4 absences.</div>
            <?php endif; ?>

            <div class="course-admin-list">

              <?php foreach ($student["courses"] as $course): ?>
                <form class="course-admin-row advanced-row" action="../Backend/update-student-course.php" method="POST">

                  <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student["id"]); ?>">
                  <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course["name"]); ?>">

                  <div class="course-title-box">
                    <h3><?php echo htmlspecialchars($course["name"]); ?></h3>
                    <p>Coef <?php echo $course["coef"]; ?> • Crédit <?php echo $course["credit"]; ?></p>
                  </div>

                  <div class="admin-field">
                    <label>Absences</label>
                    <input type="number" name="absence" min="0" value="<?php echo $course["absence"] ?? 0; ?>">
                  </div>

                  <div class="admin-field">
                    <label>TD/TP</label>
                    <input type="number" name="td" min="0" max="20" step="0.25" value="<?php echo $course["td"] ?? 0; ?>">
                  </div>

                  <div class="admin-field">
                    <label>Exam</label>
                    <input type="number" name="exam" min="0" max="20" step="0.25" value="<?php echo $course["exam"] ?? 0; ?>">
                  </div>

                  <div class="admin-field">
                    <label>Progress</label>
                    <input type="number" name="progress" min="0" max="100" value="<?php echo $course["progress"] ?? 0; ?>">
                  </div>

                  <div class="final-note">
                    <label>Final</label>
                    <strong><?php echo $course["note"] ?? 0; ?>/20</strong>
                  </div>

                  <div class="admin-field note-field">
                    <label>Note admin</label>
                    <textarea name="admin_note"><?php echo htmlspecialchars($course["admin_note"] ?? ""); ?></textarea>
                  </div>

                  <button type="submit">Sauvegarder</button>

                </form>
              <?php endforeach; ?>

            </div>
          </div>
        <?php endforeach; ?>

      </section>
    <?php endif; ?>

    <section class="ranking-section" id="ranking">
      <div class="section-admin-head">
        <span class="tag">Classement</span>
        <h2>🏆 Classement des étudiants</h2>
      </div>

      <div class="ranking-container">

        <div class="ranking-top">
          <?php foreach (array_slice($ranked, 0, 3) as $index => $student): ?>
            <div class="podium-card rank-<?php echo $index + 1; ?>">
              <div class="medal">
                <?php
                  if ($index === 0) echo "🥇";
                  elseif ($index === 1) echo "🥈";
                  else echo "🥉";
                ?>
              </div>

              <img src="../Media/Pictures/<?php echo htmlspecialchars($student["photo"] ?? "logo.png"); ?>" alt="Student">

              <h3><?php echo htmlspecialchars($student["name"]); ?></h3>
              <p><?php echo htmlspecialchars($student["id"]); ?></p>
              <strong><?php echo $student["average"]; ?>/20</strong>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="ranking-list-box">

          <?php foreach ($ranked as $index => $student): ?>
            <div class="ranking-row-pro">

              <div class="rank-number">
                #<?php echo $index + 1; ?>
              </div>

              <img src="../Media/Pictures/<?php echo htmlspecialchars($student["photo"] ?? "logo.png"); ?>" alt="Student">

              <div class="ranking-info">
                <h3><?php echo htmlspecialchars($student["name"]); ?></h3>
                <p><?php echo htmlspecialchars($student["id"]); ?> • <?php echo htmlspecialchars($student["level"]); ?></p>
              </div>

              <div class="ranking-score">
                <?php echo $student["average"]; ?>/20
              </div>

              <?php if ($student["average"] >= 14): ?>
                <span class="rank-status excellent">Excellent</span>
              <?php elseif ($student["average"] >= 10): ?>
                <span class="rank-status valid">Validé</span>
              <?php else: ?>
                <span class="rank-status weak">Faible</span>
              <?php endif; ?>

            </div>
          <?php endforeach; ?>

        </div>

      </div>
    </section>

  </main>

</div>

</body>
</html>