<?php
session_start();
session_destroy();

header("Location: ../Html/admin-login.html");
exit;
?>