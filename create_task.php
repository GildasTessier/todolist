<?php
echo '<li>
    <form action="" method="post" class="create-task">
        <input class="title-create-task" type="text" name="task" id="task-text" >
        <input class="btn-add-task" name="btn-task" type="submit" value="Add new task">
        <input type="hidden" name="token" value="'.$_SESSION['token'].'">
        </form>
        </li>';
        ?>