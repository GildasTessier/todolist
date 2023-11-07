<?php
$query = $dbCo->prepare("SELECT name_task, date_create_task, description_task FROM task");
$query->execute();
$result = $query->fetchAll();
?>

<?php
foreach($result as $task) {
echo'<li class="task">
    <div class="first-line-task">
    
    <button class="btn-priority" id="btn-priority">UP⇧</button>
<h3 class="title-task">'.$task['name_task'].'</h3>

<form>
<input class="checkbox-task" type="checkbox" name="checkbox" id="checkbox-task">
</form>
</div>
<div class="back-line hidden">
<p class="date-task">'.$task['date_create_task'].'</p>
<p class="text-task">'.$task['description_task'].'</p>
        <div class="btns-task">
        <button class="btn-mod-task">Modifier</button>
        <button class="btn-del-task">Supprimer</button>
        </div>
        </div>
        </li>'
    ;}
        ?>

