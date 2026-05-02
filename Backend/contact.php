<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Html/contact.html");
    exit;
}

$fullname = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($fullname === "" || $email === "" || $subject === "" || $message === "") {
    die("Tous les champs sont obligatoires.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email invalide.");
}

$dataFile = __DIR__ . "/data/messages.json";

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, "[]");
}

$messages = json_decode(file_get_contents($dataFile), true);

$newMessage = [
    "id" => uniqid("msg_"),
    "fullname" => htmlspecialchars($fullname),
    "email" => htmlspecialchars($email),
    "subject" => htmlspecialchars($subject),
    "message" => htmlspecialchars($message),
    "date" => date("Y-m-d H:i:s")
];

$messages[] = $newMessage;

file_put_contents(
    $dataFile,
    json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Message envoyé</title>
  <link rel="stylesheet" href="../Css/contact.css">
</head>
<body>

<section class="contact-hero">
  <div class="container">
    <div class="hero-card" style="max-width:600px;margin:auto;text-align:center;">
      <h3>✅ Message envoyé avec succès</h3>
      <p>Merci, votre message a été enregistré. L’administration pourra le consulter.</p>
      <br>
      <a href="../Html/contact.html" class="portal-btn">Retour au contact</a>
    </div>
  </div>
</section>

</body>
</html>