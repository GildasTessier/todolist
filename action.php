<?php
require_once './dbCo.php';

session_start();
if(!isset($_SESSION['token'])) {
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
}



// FOR ADD NEW TASK
if(isset($_POST['new_task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {

        if(strlen($_POST['new_task']) > 0) {                        
$query = $dbCo->prepare(" INSERT INTO task (name_task, date_create, state_task)
                            VALUES (:new_task, :day_now, 0);
                            UPDATE task SET priority_task_trans = (SELECT ROW_NUMBER() OVER(PARTITION BY priority_task) FROM task ;
                            UPDATE task SET priority_task = priority_task_trans 
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

// FOR MODIFY TASK
else if(isset($_POST['task'])) {  
    var_dump($_POST);
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {
        
        if(strlen($_POST['task']) > 0) {                        
            $query = $dbCo->prepare(" UPDATE task SET name_task = :name_task   WHERE id_task = :id ");
            $isQueryOk = $query->execute([
                'name_task' => strip_tags($_POST['task']), 
                'id' => intval($_POST['id']) ]
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
// FOR UPDATE STATE TASK
else if ($_GET['action'] === 'state' && isset($_GET['id'])){
    $query = $dbCo->prepare(" UPDATE task SET state_task = 1 WHERE id_task = :id_task;");
    $isquerryok = $query->execute([
        'id_task' => intval($_GET['id'])
        ]);}

header('Location: index.php?')
?>
