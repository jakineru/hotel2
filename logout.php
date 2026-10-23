<?php
session_start();
session_unset();
session_destroy();
header("location: /hotel2/index.php");
exit;