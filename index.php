<?php
session_start();
if(!isset($_SESSION['token'])) {
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
}

require_once './dbCo.php';
require_once './query_create_task.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./asset/css/reset.css">
    <link rel="stylesheet" href="./asset/css/style.css">
    <title>to do +</title>
</head>
<body>
    <header class="container">
        <h1 class="home-title">My To Do +</h1>
    </header>
    <main>
        <ul>
        <?php
            require_once './task.php';
        ?>
        </ul>
        <?php
            require_once './create_task.php';
        ?>
    </main>
    <footer>
        <p class="text-footer"> By Aurelien & Gildas </p>
    </footer> 
    <script src="./asset/js/script.js"></script>  
</body>
</html>