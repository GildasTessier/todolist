<?php
$query = $dbCo->prepare("SELECT id_task, name_task, description_task FROM task WHERE state_task = 0 ORDER BY priority_task DESC");
$query->execute();
$result = $query->fetchAll();

foreach($result as $task) {

echo '<li>
    <form class="task">
        <input class="title-task" type="text" name="task" id="'.$task['id_task'].'" value=" '. $task['name_task'].'">
        <button class="btn-priority-up" id="btn-priority">⇧</button>
        <button class="btn-priority-down" id="btn-priority">⇩</button>
        <button class="btn-mod-task">MODIFY</button>
        <button class="btn-del-task">DELET</button>
        <button class="btn-finish-task">FINISH ✓</button>
    </form>
</li>';
}
?>