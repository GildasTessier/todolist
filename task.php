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
<p class="date-task"> 22-10-2023</p>
<p class="text-task">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Reprehenderit, distinctio cumque. 
        Similique velit animi quidem illo ab non dolorem culpa incidunt saepe fugit aperiam placeat, 
        esse natus rerum porro doloribus! </p>
        <div class="btns-task">
        <button class="btn-mod-task">Modifier</button>
        <button class="btn-del-task">Supprimer</button>
        </div>
        </div>
        </li>'
    ;}
        ?>

