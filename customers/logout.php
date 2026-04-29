<?php
session_start();

/* UNSET ALL SESSION VARIABLES */
session_unset();

/* DESTROY SESSION */
session_destroy();

/* REDIRECT TO LOGIN */
header("Location: ../login.php");
exit();
?>