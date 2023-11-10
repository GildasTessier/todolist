<?php
session_start();
require_once './_function.php';
require_once './dbCo.php';
checkCSRF('index.php');
$msg = 'nothing';

// FOR ADD NEW TASK
if(isset($_POST['new_task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {

        if(strlen($_POST['new_task']) > 0) {  

$query = $dbCo->prepare("SELECT (COUNT(id_task)+1) AS row_ FROM task WHERE priority_task IS NOT NULL;");
$query -> execute();
$nbRow = $query->fetch();

$date_alert = strlen($_POST['alert_date']) === 0 ? NULL : strip_tags($_POST['alert_date']);
var_dump($date_alert);
var_dump($_POST['alert_date']);

$query = $dbCo->prepare(" INSERT INTO task (name_task, alert_date, date_create ,state_task, priority_task)
                            VALUES (:new_task, :alert_date, :day_now, 0, :nb_row)
                            ");
            $isQueryOk = $query->execute([
            'new_task' => strip_tags($_POST['new_task']), 
            'alert_date' => $date_alert, 
            'day_now' => date('Y-m-d h:i:s'),
            'nb_row' => $nbRow['row_']]
        );

            if($isQueryOk && $query->rowCount()=== 1) {
                $msg = 'addTask';
            };
    }
        else {
            $msg = 'addTaskError';
            
        };
    }
}

// FOR MODIFY TASK
else if(isset($_POST['task'])) {   
        if(strlen($_POST['task']) > 0) {                        
            $query = $dbCo->prepare(" UPDATE task SET name_task = :name_task   WHERE id_task = :id_task ");
            $isQueryOk = $query->execute([
                'name_task' => strip_tags($_POST['task']), 
                'id_task' => intval($_POST['id']) ]
            );
            
            if($isQueryOk && $query->rowCount()=== 1) {
                $msg='updateTask';
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
    $isQueryOk = $query->execute([
        'id_task' => intval(strip_tags($_GET['id']))
    ]);
    if($isQueryOk && $query->rowCount()=== 1) {
        $msg='updateStateTask';
    };
} 
// FOR CHANGE ORDER IN LIST
else if (($_GET['action'] === 'up'|| $_GET['action'] === 'down') && isset($_GET['id'])){ 

    $query = $dbCo->prepare ("SELECT priority_task FROM task WHERE id_task = :id_task;");
$isQueryOk = $query->execute([
'id_task' => intval(strip_tags($_GET['id']))]);
$nbPrioTask = $query->fetch();

$query = $dbCo->prepare ("SELECT COUNT(id_task) as nb_row FROM task WHERE state_task = 0");
$query->execute();
$nbrow = $query->fetch();

    // FOR UP
    if ($_GET['action'] === 'up' && $nbPrioTask['priority_task'] != 1) {

        $query = $dbCo->prepare("UPDATE task SET priority_task = (priority_task + 1)  WHERE priority_task = (:priority_task - 1);
                                 UPDATE task SET priority_task = (priority_task - 1)  WHERE id_task = :id_task;");

        $isQueryOk = $query->execute([
            'id_task' => intval(strip_tags($_GET['id'])),
            'priority_task' => intval($nbPrioTask['priority_task'])
            ]);
        $msg='updatePriority';
    }
    // FOR DOWN
    else if ($_GET['action'] === 'down' && $nbPrioTask['priority_task'] != $nbrow['nb_row']) {
        $query = $dbCo->prepare("UPDATE task SET priority_task = (priority_task - 1)  WHERE priority_task = (:priority_task + 1);
                                 UPDATE task SET priority_task = (priority_task + 1)  WHERE id_task = :id_task;");

        $isQueryOk = $query->execute([
            'id_task' => intval(strip_tags($_GET['id'])),
            'priority_task' => intval($nbPrioTask['priority_task'])
            ]);
        $msg='updatePriority';
    }

    else {
        $msg = '';
    }
}


// FOR DELETE TASK
else if ($_GET['action'] === 'delete' && isset($_GET['id'])){
    // FOR CHANDE ORDER WHEN DELETE
    updateNbPriority($dbCo);
    // FOR DELETE
    $query = $dbCo->prepare(" DELETE FROM task WHERE id_task = :id_task;");
    $isQueryOk = $query->execute([
        'id_task' => intval(strip_tags($_GET['id']))
        ]);
        if($isQueryOk && $query->rowCount()=== 1) {
            $msg='deleteTask';
    }
};

$_SESSION['notif'] = $msg;
header('Location: index.php')
?>
