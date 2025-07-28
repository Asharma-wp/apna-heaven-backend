<?php
session_start();
session_destroy();
header('Location: apna-heaven.php');
exit();
?>