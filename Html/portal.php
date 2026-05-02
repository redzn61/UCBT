<?php
$error = $_GET["error"] ?? "";
$forgot = isset($_GET["forgot"]);

$studentsFile = "../Backend/data/students.json";
$topStudents = [];

if (file_exists($studentsFile)) {
    $students = json_decode(file_get_contents($studentsFile), true);

    foreach ($students as &$s) {
        $total = 0;
        $count = 0;

        foreach ($s["courses"] ?? [] as $course) {
            $total += $course["note"] ?? 0;
            $count++;
        }

        $s["average"] = $count > 0 ? round($total / $count, 2) : 0;
    }

    usort($students, function($a, $b) {
        return $b["average"] <=> $a["average"];
    });

    $topStudents = array_slice($students, 0, 5);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Portail Étudiant</title>
  <link rel="stylesheet" href="../Css/portal.css">
</head>

<body>

<header class="header">
  <div class="container nav">
    <a href="../index.html" class="brand">
      <img src="../Media/Pictures/logo.png" alt="Logo">
      <div>
        <strong>جامعة الشاذلي بن جديد - الطارف</strong>
        <span>UNIVERSITÉ CHADLI BENDJEDID - EL TARF</span>
      </div>
    </a>

    <nav class="menu">
      <a href="../index.html">Accueil</a>
      <a href="about.html">À propos</a>
      <a href="departments.html">Départements</a>
      <a href="news.html">Actualités</a>
      <a href="contact.html">Contact</a>
    </nav>

    <a href="portal.php" class="portal-btn">👤 Portail Étudiant</a>
  </div>
</header>

<main>

<section class="portal-hero">
  <div class="circle circle-1"></div>
  <div class="circle circle-2"></div>

  <div class="container portal-grid">

    <div class="login-card">
      <div class="login-icon">👤</div>
      <p class="tag">Espace sécurisé</p>
      <h1>Connexion Étudiant</h1>
      <p class="login-desc">Accédez à votre espace personnel universitaire.</p>

      <?php if ($error === "invalid"): ?>
        <div class="alert error-alert">❌ Identifiant ou mot de passe incorrect.</div>
      <?php endif; ?>

      <?php if ($forgot): ?>
        <div class="alert info-alert">📩 Veuillez contacter l’administration pour récupérer votre mot de passe.</div>
      <?php endif; ?>

      <form action="../Backend/login.php" method="POST">
        <label>Identifiant étudiant</label>
        <input type="text" name="student_id" placeholder="202336326510" required>

        <label>Mot de passe</label>
        <input type="password" name="password" placeholder="Axx5TRE" required>

        <div class="login-row">
          <label class="remember">
            <input type="checkbox">
            Se souvenir de moi
          </label>

          <a href="portal.php?forgot=1">Mot de passe oublié ?</a>
        </div>

        <button type="submit">Se connecter</button>
      </form>
    </div>

    <div class="portal-content">
      <p class="tag">Portail Étudiant</p>
      <h2>Votre espace universitaire moderne</h2>
      <p>
        Consultez vos cours, notes, absences, emploi du temps et classement étudiant.
      </p>

      <div class="features">
        <div class="feature-card">
          <div>📚</div>
          <h3>Cours</h3>
          <p>Modules et supports pédagogiques.</p>
        </div>

        <div class="feature-card">
          <div>📊</div>
          <h3>Notes</h3>
          <p>TD/TP, examens et moyenne finale.</p>
        </div>

        <div class="feature-card">
          <div>📌</div>
          <h3>Absences</h3>
          <p>Suivi des absences par module.</p>
        </div>

        <div class="feature-card">
          <div>🗓️</div>
          <h3>Planning</h3>
          <p>Emploi du temps personnalisé.</p>
        </div>
      </div>
    </div>

  </div>
</section>

<section class="portal-top-section">
  <div class="container">
    <div class="portal-section-title">
      <span class="tag">Classement réel</span>
      <h2>🏆 Top 5 Étudiants</h2>
    </div>

    <div class="portal-top-list">
      <?php foreach ($topStudents as $index => $top): ?>
        <div class="portal-top-card">
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
  </div>
</section>

</main>

</body>
</html>