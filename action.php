<?php
session_start();
require_once './_function.php';
require_once './dbCo.php';
checkCSRF('index.php');




// FOR ADD NEW TASK
if(isset($_POST['new_task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {

        if(strlen($_POST['new_task']) > 0) {  

$query = $dbCo->prepare("SELECT (COUNT(id_task)+1) as row FROM task;");
$query -> execute();
$nbRow = $query->fetch();
var_dump($nbRow['row']);

$query = $dbCo->prepare(" INSERT INTO task (name_task, date_create, state_task, priority_task)
                            VALUES (:new_task, :day_now, 0, :nb_row)
                            ");
            $isQueryOk = $query->execute([
            'new_task' => strip_tags($_POST['new_task']), 
            'day_now' => date('Y-m-d h:i:s'),
            'nb_row' => $nbRow['row']]
        );

            if($isQueryOk && $query->rowCount()=== 1) {
                $msg = 'addTask';
            };
    }
        else {
            $msg = 'addTaskError';
            
        };
    }
    else {
        $msg = 'addTaskError';
    }
}

// FOR MODIFY TASK
else if(isset($_POST['task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {
        
        if(strlen($_POST['task']) > 0) {                        
            $query = $dbCo->prepare(" UPDATE task SET name_task = :name_task   WHERE id_task = :id_task ");
            $isQueryOk = $query->execute([
                'name_task' => strip_tags($_POST['task']), 
                'id_task' => intval($_POST['id']) ]
            );
            
            if($isQueryOk && $query->rowCount()=== 1) {
                $msg='updateTask';
            };
        }
        else {
            $msg = 'addTaskError';
        };
    }
    else {
        $msg = 'addTaskError';
    }
}
//FOR UPDATE STATE TASK
else if ($_GET['action'] === 'state' && isset($_GET['id'])){

        // FOR CHANDE ORDER WHEN DELETE
        updateNbPriority($dbCo);
    $query = $dbCo->prepare(" UPDATE task SET state_task = 1, priority_task = NULL WHERE id_task = :id_task;");
    $isquerryok = $query->execute([
        'id_task' => intval($_GET['id'])
        ]);}
        


// FOR CHANGE ORDER IN LIST

        // UP
        else if ($_GET['action'] === 'up' && isset($_GET['id'])){
            $query = $dbCo->prepare(" UPDATE task SET priority_task = (priority_task - 1)  WHERE id_task = :id_task;");
            $isquerryok = $query->execute([
                'id_task' => intval($_GET['id'])
                ]);

            $query = $dbCo->prepare(" SELECT priority_task FROM task WHERE id_task = :id_task;");
            $isquerryok = $query->execute([
                'id_task' => intval($_GET['id'])
            ]);
            $nbprio = $query->fetch();
            
            $query = $dbCo->prepare(" UPDATE task SET priority_task = (:nbPrio + 1) WHERE id_task <> :id_task AND priority_task = :nbPrio;");
            $isquerryok = $query->execute([
                'id_task' => intval($_GET['id']),
                'nbPrio' => $nbprio['priority_task']
            ]);
            }

            //DOWN
            else if ($_GET['action'] === 'down' && isset($_GET['id'])){
                $query = $dbCo->prepare(" UPDATE task SET priority_task = (priority_task + 1)  WHERE id_task = :id_task;");
                $isquerryok = $query->execute([
                    'id_task' => intval($_GET['id'])
                    ]);
    
                $query = $dbCo->prepare(" SELECT priority_task FROM task WHERE id_task = :id_task;");
                $isquerryok = $query->execute([
                    'id_task' => intval($_GET['id'])
                ]);
                $nbprio = $query->fetch();
                
                $query = $dbCo->prepare(" UPDATE task SET priority_task = (:nbPrio - 1) WHERE id_task <> :id_task AND priority_task = :nbPrio;");
                $isquerryok = $query->execute([
                    'id_task' => intval($_GET['id']),
                    'nbPrio' => $nbprio['priority_task']
                ]);
                }


// FOR DELETE TASK
else if ($_GET['action'] === 'delete' && isset($_GET['id'])){
    // FOR CHANDE ORDER WHEN DELETE
    updateNbPriority($dbCo);
    // FOR DELETE
    $query = $dbCo->prepare(" DELETE FROM task WHERE id_task = :id_task;");
    $isQueryOk = $query->execute([
        'id_task' => intval($_GET['id'])
        ]);
        if($isQueryOk && $query->rowCount()=== 1) {
            $msg='deleteTask';
    }
}


header('Location: index.php?notif='.$msg)
?>
