<?php
session_start();
require_once './vendor/autoload.php';
require_once './includes/_function.php';
require_once './includes/_dbCo.php';
include './includes/_notif.php';
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
    <?php include './includes/_connection.php'?>
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
                echo '<li class="li-burger-menu">
                        <a  href="index.php?category='.$category['name_category'].'&id_category='.$category['id_category'].'">'.$category['name_category'].'</a>
                        <a class="btn-delete-category" href="action.php?action=deletecategory&id_category='.$category['id_category'].'&token='.$_SESSION['token'].'">DELETE</a>
                        </li>';
            }}
             ?>
                <li class="li-burger-menu li-burger-menu-form">
                    <form action="action.php" method="post">
                    <input type="text" placeholder="ADD NEW CATEGORY" name="new-category" required="text" value="">
                    <input type="submit" value="ADD" class="btn-add-category">
                    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
                </form>
        </li>
        <li class="li-burger-menu li-burger-menu-disconnection">
            <a href="action.php?action=disconnection&token=<?=$_SESSION['token']?>">DISCONNECTION</a>
        </li>
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
    $query = $dbCo->prepare("SELECT id_task, name_task, alert_date, color FROM task t JOIN association a USING (id_task) WHERE state_task = :state_ AND id_category = :id_category AND id_user = :id_user ORDER BY a.priority_task");
    $query->execute([
        'state_' => isset($_GET['state']) ? 1 : 0,
        'id_category' => intval($_GET['id_category']),
        'id_user' => $_SESSION['id_user']]
    );
}
else {
    $query = $dbCo->prepare("SELECT id_task, name_task, alert_date, color FROM task WHERE state_task = :state_ AND id_user = :id_user ORDER BY priority_task");
    $query->execute([
        'state_' => isset($_GET['state']) ? 1 : 0,
        'id_user' => $_SESSION['id_user']
    ]);
}
$result = $query->fetchAll();


if(!isset($_GET['state'])){
    if(sizeof($result) < 1 ) echo '<p class="text-no-task">No task to display at the moment</p>';
    foreach($result as $task) {
        $query = $dbCo->prepare("SELECT id_category FROM association WHERE id_task = :id_task");
            $query->execute(['id_task' => $task['id_task']]);
            $categoryActive = $query->fetchAll();
            $idCategoryTask = [];
            if (isset($categoryActive[0]['id_category'])) {
            foreach ($categoryActive as $value) {
                $idCategoryTask[] .= $value['id_category'];
            }
        }
        ?>
<li>
    <form class="task" action="action.php" method="post" style="background-color:<?=$task['color']?>">
   <input class="title-task" type="text" name="name_task" id="text-task" value="<?=$task['name_task']?>"style="background-color:<?=$task['color']?>">
    <input type="submit" class="submit btn-mod-task" value="MODIFY">
    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    <input type="hidden" name="id" value="<?=$task['id_task']?>">
    <input type="hidden" name="modify" value="">

    <a class="submit btn-priority-up" href="action.php?action=up&id=<?=$task['id_task'].'&'.$_SERVER['QUERY_STRING'].'&token='.$_SESSION['token']?>">⇧</a>
    <a class="submit btn-priority-down" href="action.php?action=down&id=<?=$task['id_task'].'&'.$_SERVER['QUERY_STRING'].'&token='.$_SESSION['token']?>">⇩</a>

    <input class="btn-color" type="color" id="color" name="color" value="<?=$task['color']?>">

    <a class="submit btn-del-task" href="action.php?action=delete&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">DELET</a>
    <a class="submit btn-finish-task" href="action.php?action=state_finish&id=<?=$task['id_task'].'&token='.$_SESSION['token']?>">FINISH ✓</a>
    <p class="alert-date-task"><?=isset($task['alert_date'])?'Call back on <span class="span-date-alert">'.$task['alert_date'].'</span>' : '' ?></p>

    <div class="add-category" id="">
            <?php 
            foreach ($resultCategorys as $category ) { 
                echo '<input id="category-' . $category['id_category'] . $task['id_task'] .'" value="' . $category['id_category'] . '" type="checkbox" name="category[]"';
                echo in_array($category['id_category'], $idCategoryTask)? "checked/> " : " />";
                echo '<label for="category-' . $category['id_category']. $task['id_task'].'">'. $category['name_category'] . '</label>'; 
        }
            ?>
            </div>

    </form>
    </li>
    <?php
};}

else {
    if(sizeof($result) < 1 ) echo '<p class="text-no-task">No task to display at the moment</p>';
foreach($result as $task) {
    ?>
<li>
    <form class="task" action="action.php" method="post" style="background-color:<?=$task['color']?>">
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
            <label class="js-more-option hidden" for="color">Change color</label>
            <input class="js-more-option hidden" type="color" id="color" name="color" value="#9EB8B7">
        <a href="#" class="btn-more-options" id="btn-more-options">More options</a>
        <input class="btn-add-task" name="btn-task" type="submit" value="Add new task">
    </form>
    </main>
    <script src="./asset/js/script.js"></script>  
</body>

</html>