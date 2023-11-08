<?php
$query = $dbCo->prepare("SELECT id_task, name_task, description_task FROM task WHERE state_task = 0 ORDER BY priority_task DESC");
$query->execute();
$result = $query->fetchAll();

foreach($result as $task) {

echo '<li>
    <form class="task" id="'.$task['id_task'].'">
        <input class="title-task" type="text" name="task" id="text-task" value=" '. $task['name_task'].'">
        <input type="submit" class="submit btn-mod-task" value="MODIFY">
        <a class="submit btn-priority-up" href="#">⇧</a>
        <a class="submit btn-priority-down" href="#">⇩</a>
        <a class="submit btn-del-task" href="#">DELET</a>
        <a class="submit btn-finish-task" href="?id='.$task['id_task'].'">FINISH ✓</a>
    </form>
</li>';
};
?>