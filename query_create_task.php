
<?php
$query = $dbCo->prepare(" INSERT INTO task (name_task, date_create, state_task)

                            VALUES (:new_task, :day_now, 0);");


if(isset($_POST['task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {

        if(strlen($_POST['task']) > 0) {                        
            $query->execute([
                'new_task' => strip_tags($_POST['task']),
                'day_now' => date('Y-m-d h:i:s')
                ]
            );
        }
        else {
            $msg = 'Nom de tache vide';
        };
    }
    else {
        $msg = 'Token non valide ';
    }
}
?>