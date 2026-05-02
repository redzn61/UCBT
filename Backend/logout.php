<?php
session_start();
session_destroy();
header("Location: ../Html/portal.html");
exit;
?>