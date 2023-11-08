
<?php


if(isset($_POST['new_task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {

        if(strlen($_POST['new_task']) > 0) {                        
$query = $dbCo->prepare(" INSERT INTO task (name_task, date_create, state_task)
                            VALUES (:new_task, :day_now, 0);
                            UPDATE task SET priority_task = id_task   WHERE name_task = :new_task AND date_create = :day_now; 
                            ");

            $isQueryOk = $query->execute([
            'new_task' => strip_tags($_POST['new_task']), 
            'day_now' => date('Y-m-d h:i:s')]
            // $query = $dbCo->prepare(" UPDATE task SET priority_task = id_task   WHERE name_task = :new_task AND date_create = :day_now ");
            // $isQueryOk = $query->execute();
        );

            if($isQueryOk && $query->rowCount()=== 1) {
                $msg = 'La tache à été ajouté à la liste';
            };
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