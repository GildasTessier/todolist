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
        <h2 class="name-task-list">Task list 1<h2>
        <p class="notif hidden" id="msg-notif"><?php
            if(isset($_SESSION['notif'])) {
                echo $notif[$_SESSION['notif']];
                unset($_SESSION['notif']);

            };
            ?>
        </p>
        </header>
    <main>
        <ul>
        <?php
$query = $dbCo->prepare("SELECT id_task, name_task, alert_date FROM task WHERE state_task = 0 ORDER BY priority_task");
$query->execute();
$result = $query->fetchAll();

foreach($result as $task) {
    ?>
<li>
    <form class="task" action="action.php" method="post">
   <input class="title-task" type="text" name="name_task" id="text-task" value="<?=$task['name_task']?>">
    <input type="submit" class="submit btn-mod-task" value="MODIFY">
    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    <input type="hidden" name="id" value="<?=$task['id_task']?>">
    <input type="hidden" name="modify" value="">
    <a class="submit btn-priority-up" href="action.php?action=up&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">⇧</a>
    <a class="submit btn-priority-down" href="action.php?action=down&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">⇩</a>
    <a class="submit btn-del-task" href="action.php?action=delete&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">DELET</a>
    <a class="submit btn-finish-task" href="action.php?action=state&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">FINISH ✓</a>
    <p class="alert-date-task"><?=isset($task['alert_date'])?'Call back on '.$task['alert_date'] : '' ?></p>
    </form>
    </li>
    <?php
};
    ?>
        </ul>

<li class="li-create-task">
    <form action="action.php" method="post" class="create-task">
        <div class="classic-option">
            <input class="title-create-task" type="text" name="name_task" id="task-text" >
            <input class="btn-add-task" name="btn-task" type="submit" value="Add new task">
            <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
            <input type="hidden" name="add" value="">
        </div>
        <div id="more-option" class="more-option hidden">
            <input class="date-create-task" type="date" name="alert_date" id="task-text">
        </div>
    </form>
    <button class="btn-more-options" id="btn-more-options">More options</button>
    </li>

    </main>
    <footer>
        <p class="text-footer"> By Aurelien & Gildas </p>
    </footer> 
    <script src="./asset/js/script.js"></script>  
</body>

</html>