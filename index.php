<?php
session_start();
require_once './_function.php';
require_once './dbCo.php';
include './notif.php';
generateToken();
getUrlWithParam('index.php');
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
        <img class="icon-menu-burger" id="icon-menu-burger" src="./asset/img/icons/menu.png">
        <nav id="burger-menu" class="hidden">
            <ul class="ul-burger-menu">
            <li class="li-burger-menu"><a  href="index.php">All tasks</a></li>
        <?php 
            $query = $dbCo->prepare("SELECT id_category, name_category FROM category  ORDER BY name_category");
            $query->execute();
            $result = $query->fetchall();
            $resultCategorys = $result;
            if(sizeof($result) > 0 )  {
            foreach($result as $category) {
                echo '<li class="li-burger-menu"><a  href="index.php?category='.$category['name_category'].'&id_category='.$category['id_category'].'">'.$category['name_category'].'</a></li>';
            }}
             ?>
            </ul>
        </nav>
         <h2 class="name-task-list"><?=(isset($_GET['category']))? $_GET['category'] : 'All tasks'?><h2>
        <p class="notif hidden" id="msg-notif"><?php

            if(isset($_SESSION['notif'])) {
                echo $notif[$_SESSION['notif']];
                unset($_SESSION['notif']);
            };
            ?>
        </p>
        </header>
    <main>
        <div class="filter-show">
                <a class="" href="index.php<?=isset($_GET['category'])? '?category=' . $_GET['category'] .'&id_category='. $_GET['id_category']:''?>">Tasks in progress</a>
                <a class="" href="index.php<?=isset($_GET['category'])? '?category=' . $_GET['category'] .'&id_category='. $_GET['id_category']:'?'?>&state=1">Finished tasks</a>
        </div>
        <ul>
        <?php
if (isset($_GET['id_category'])) {
    $query = $dbCo->prepare("SELECT id_task, name_task, alert_date FROM task t JOIN association a USING (id_task) WHERE state_task = :state_ AND id_category = :id_category ORDER BY a.priority_task");
    $query->execute([
        'state_' => isset($_GET['state']) ? 1 : 0,
        'id_category' => intval($_GET['id_category'])]
    );
}
else {
    $query = $dbCo->prepare("SELECT id_task, name_task, alert_date FROM task WHERE state_task = :state_ ORDER BY priority_task");
    $query->execute([
        'state_' => isset($_GET['state']) ? 1 : 0
    ]);
}
$result = $query->fetchAll();
if(!isset($_GET['state'])){
    if(sizeof($result) < 1 ) echo '<p class="text-no-task">No task to display at the moment</p>';
foreach($result as $task) {
    ?>
<li>
    <form class="task" action="action.php" method="post">
   <input class="title-task" type="text" name="name_task" id="text-task" value="<?=$task['name_task']?>">
    <input type="submit" class="submit btn-mod-task" value="MODIFY">
    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    <input type="hidden" name="id" value="<?=$task['id_task']?>">
    <input type="hidden" name="modify" value="">
    <a class="submit btn-priority-up" href="action.php?action=up&id=<?=$task['id_task'].'&'.$_SERVER['QUERY_STRING'].'&token='.$_SESSION['token']?>">⇧</a>
    <a class="submit btn-priority-down" href="action.php?action=down&id=<?=$task['id_task'].'&'.$_SERVER['QUERY_STRING'].'&token='.$_SESSION['token']?>">⇩</a>
    <a class="submit btn-del-task" href="action.php?action=delete&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">DELET</a>
    <a class="submit btn-finish-task" href="action.php?action=state_finish&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">FINISH ✓</a>
    <p class="alert-date-task"><?=isset($task['alert_date'])?'Call back on <span class="span-date-alert">'.$task['alert_date'].'</span>' : '' ?></p>
    </form>
    </li>
    <?php
};}

else {
    if(sizeof($result) < 1 ) echo '<p class="text-no-task">No task to display at the moment</p>';
foreach($result as $task) {
    ?>
<li>
    <form class="task" action="action.php" method="post">
   <input class="title-task" type="text" name="name_task" id="text-task" value="<?=$task['name_task']?>">
    <input type="submit" class="submit btn-mod-task" value="MODIFY">
    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    <input type="hidden" name="id" value="<?=$task['id_task']?>">
    <input type="hidden" name="modify" value="">
    <a class="submit btn-del-task" href="action.php?action=delete&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">DELET</a>
    <a class="submit btn-finish-task" href="action.php?action=state_unfinish&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">UNFINISH</a>
    <p class="alert-date-task"><?=isset($task['alert_date'])?'Call back on <span class="span-date-alert">'.$task['alert_date'].'</span>' : '' ?></p>
    </form>
    </li>
    <?php
    };}
    ?>
        </ul>
    <form id="form-add-task" action="action.php" method="post" class="create-task">
            <label  class="js-more-option hidden" for="task-text">Task name</label>
            <input class="title-create-task" type="text" name="name_task" id="task-text" >
            <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
            <input type="hidden" name="add" value="">
            
            <label class=" js-more-option hidden" for="task-datey">Add date call back</label>
            <input class="js-more-option date-create-task hidden" type="date" name="alert_date" id="task-date">
            <label  class="js-more-option hidden" for="add-category">Add category</label>
             <div class="js-more-option add-category hidden" id="add-category">
            <?php 
            foreach ($resultCategorys as $category ) { 

                echo '<input id="category-' . $category['id_category'] .'" value="' . $category['id_category'] . '"  type="checkbox" name="category[]"/>';
                echo '<label for="category-' . $category['id_category'].'">'. $category['name_category'] . '</label>'; 
        }
            ?>
            </div>
        <a href="#" class="btn-more-options" id="btn-more-options">More options</a>
        <input class="btn-add-task" name="btn-task" type="submit" value="Add new task">
    </form>
    </main>
    <script src="./asset/js/script.js"></script>  
</body>

</html>