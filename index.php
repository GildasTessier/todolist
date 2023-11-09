<?php
session_start();
require_once './_function.php';
require_once './dbCo.php';
include './notif.php';
generateToken();
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
        <h2 class="notif"><?php
        if(isset($_GET['notif'])){echo $notif[$_GET['notif']];}?>
        </h2>
        </header>
    <main>
        <ul>
        <?php
$query = $dbCo->prepare("SELECT id_task, name_task, description_task FROM task WHERE state_task = 0 ORDER BY priority_task DESC");
$query->execute();
$result = $query->fetchAll();

foreach($result as $task) {
    ?>
<li>
    <form class="task" action="action.php" method="post">
    <input class="title-task" type="text" name="task" id="text-task" value="<?=$task['name_task']?>">
    <input type="submit" class="submit btn-mod-task" value="MODIFY">
    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    <input type="hidden" name="id" value="<?=$task['id_task']?>">
    <a class="submit btn-priority-up" href="#">⇧</a>
    <a class="submit btn-priority-down" href="#">⇩</a>
    <a class="submit btn-del-task" href="action.php?action=delete&id=<?=$task['id_task']?>">DELET</a>
    <a class="submit btn-finish-task" href="action.php?action=state&id=<?=$task['id_task']?>">FINISH ✓</a>
    </form>
    </li>
    <?php
};
    ?>
        </ul>

<li>
    <form action="action.php" method="post" class="create-task">
        <input class="title-create-task" type="text" name="new_task" id="task-text" >
        <input class="btn-add-task" name="btn-task" type="submit" value="Add new task">
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        </form>
        </li>

    </main>
    <footer>
        <p class="text-footer"> By Aurelien & surtout Gildas </p>
    </footer> 
    <script src="./asset/js/script.js"></script>  
</body>
</html>