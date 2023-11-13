<?php
session_start();
require_once './_function.php';
require_once './dbCo.php';
checkCSRF('index.php');

if (isset($_POST['lasttask'])) {
    $_SESSION['filter_status'] = 1;
}

// header('Location: index.php');

?>