<?php
session_start();
require_once './_function.php';
require_once './dbCo.php';
checkCSRF('index.php');
$msg = 'nothing';

// FOR ADD NEW TASK
if(isset($_POST['add'])) {  

        if(strlen($_POST['name_task']) > 0) {  

            
            $query = $dbCo->prepare("SELECT (COUNT(id_task)+1) AS row_ FROM task WHERE priority_task IS NOT NULL;");
            $query -> execute();
            $nbRow = $query->fetch();
            
            $nameTask = strip_tags($_POST['name_task']);
            $dateNow = date('Y-m-d h:i:s');
            $dateAlert = strlen($_POST['alert_date']) === 0 ? NULL : strip_tags($_POST['alert_date']);

            $query = $dbCo->prepare(" INSERT INTO task (name_task, alert_date, date_create ,state_task, priority_task)
                            VALUES (:name_task, :alert_date, :day_now, 0, :nb_row)");
            $isQueryOk = $query->execute([
            'name_task' => $nameTask, 
            'alert_date' => $dateAlert, 
            'day_now' => $dateNow,
            'nb_row' => $nbRow['row_']
            ]);
        
        if($isQueryOk && $query->rowCount()=== 1) {
            $msg = 'addTask';
        };
    }
    else {
        $msg = 'addTaskError';
        
    };
}
if(isset($_POST['category'])) {
    $query = $dbCo->prepare("SELECT id_task FROM task WHERE name_task = :name_task AND date_create = :date_create");
            $query->execute([
                'name_task' => $nameTask,
                'date_create' => $dateNow
                ]);
                $idTask = $query->fetch();

    foreach ($_POST['category'] as $id ) {


        $query = $dbCo->prepare("SELECT (COUNT(id_association)+1) AS row_ FROM association WHERE priority_task IS NOT NULL AND id_category = :id_category;");
            $query -> execute([
                'id_category' => strip_tags($id)
            ]);
            $nbRow = $query->fetch();

        $query = $dbCo->prepare("INSERT INTO association(id_task, id_category, priority_task) VALUES (:id_task, :id_category, :priority_task)");
        $query->execute([
            'id_task' => $idTask['id_task'],
            'id_category' => strip_tags($id),
            'priority_task' => $nbRow['row_']
            ]);
    }
    
}

// FOR MODIFY TASK
else if(isset($_POST['modify'])) {   
        if(strlen($_POST['name_task']) > 0) {                        
            $query = $dbCo->prepare(" UPDATE task SET name_task = :name_task   WHERE id_task = :id_task ");
            $isQueryOk = $query->execute([
                'name_task' => strip_tags($_POST['name_task']), 
                'id_task' => intval(strip_tags($_POST['id'])) ]
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
//FOR UNFINISH TASK
else if (isset($_GET['action']) && $_GET['action'] === 'state_unfinish' && isset($_GET['id'])){
    $query = $dbCo->prepare("SELECT (COUNT(id_task)+1) AS row_ FROM task WHERE priority_task IS NOT NULL;");
    $query -> execute();
    $nbRow = $query->fetch();

    $query = $dbCo->prepare(" UPDATE task SET state_task = 0 , priority_task = :nb_row WHERE id_task = :id_task;");
    $isQueryOk = $query->execute([
        'id_task' => intval(strip_tags($_GET['id'])),
        'nb_row' => $nbRow['row_']
    ]);


    $query = $dbCo->prepare(" SELECT id_category FROM association WHERE id_task = :id_task;");
    $query->execute([
        'id_task' => intval(strip_tags($_GET['id'])),
        ]);
        $idCategory = $query->fetchAll();

        foreach ($idCategory as  $key) {

$query = $dbCo->prepare(" SELECT (COUNT(id_association)+1) AS row_ FROM association WHERE priority_task IS NOT NULL AND id_category = :id_category;");
$query->execute([
    'id_category' => $key['id_category']
    ]);
$nb = $query->fetch();


$query = $dbCo->prepare("  UPDATE association SET priority_task = :priority_task WHERE id_task = :id_task AND id_category = :id_category;");
$query->execute([
    'priority_task' => $nb['row_'],
    'id_task' => intval(strip_tags($_GET['id'])),
    'id_category' => $key['id_category']
]);
}

    if($isQueryOk && $query->rowCount()=== 1) {
        $msg='updateStateTask';
    };
}

//FOR UPDATE STATE TASK
else if (isset($_GET['action']) && $_GET['action'] === 'state_finish' && isset($_GET['id'])){
    
    // FOR CHANDE ORDER WHEN DELETE
    updateNbPriority($dbCo);
    updateNbPriorityCategorie($dbCo);
    $query = $dbCo->prepare(" UPDATE task SET state_task = 1 , priority_task = NULL WHERE id_task = :id_task;
                              UPDATE association SET priority_task = NULL WHERE id_task = :id_task");
    $isQueryOk = $query->execute([
        'id_task' => intval(strip_tags($_GET['id'])),
    ]);
    if($isQueryOk && $query->rowCount()=== 1) {
        $msg='updateStateTask';
    };
} 



// FOR CHANGE ORDER
else if (isset($_GET['action']) && ($_GET['action'] === 'up'|| $_GET['action'] === 'down') && isset($_GET['id'])){ 
    // FOR CHANGE ORDER IN LIST BY CATEGORY

    if(isset($_GET['id_category'])) {

        $query = $dbCo->prepare ("SELECT priority_task FROM association WHERE id_task = :id_task AND id_category = :id_category;");
        $isQueryOk = $query->execute([
            'id_task' => intval(strip_tags($_GET['id'])),
            'id_category' => strip_tags($_GET['id_category'])]);
        $nbPrioTask = $query->fetch();

        $query = $dbCo->prepare ("SELECT COUNT(id_association) as nb_row FROM association WHERE id_category = :id_category");
        $query->execute(['id_category' => strip_tags($_GET['id_category'])]);
        $nbrow = $query->fetch();

    // FOR UP
        if ($_GET['action'] === 'up' && $nbPrioTask['priority_task'] != 1) {

            $query = $dbCo->prepare("UPDATE association SET priority_task = (priority_task + 1)  WHERE priority_task = (:priority_task - 1) AND id_category = :id_category;
                                    UPDATE association SET priority_task = (priority_task - 1)  WHERE id_task = :id_task AND id_category = :id_category;");

            $isQueryOk = $query->execute([
                'id_task' => intval(strip_tags($_GET['id'])),
                'id_category' => strip_tags($_GET['id_category']),
                'priority_task' => intval($nbPrioTask['priority_task'])]);

            $msg='updatePriority';
        }
    // FOR DOWN
        else if ($_GET['action'] === 'down' && $nbPrioTask['priority_task'] != $nbrow['nb_row']) {
            $query = $dbCo->prepare("UPDATE association SET priority_task = (priority_task - 1)  WHERE priority_task = (:priority_task + 1) AND id_category = :id_category;
                                    UPDATE association SET priority_task = (priority_task + 1)  WHERE id_task = :id_task AND id_category = :id_category;") ;

            $isQueryOk = $query->execute([
                'id_task' => intval(strip_tags($_GET['id'])),
                'id_category' => strip_tags($_GET['id_category']),
                'priority_task' => intval($nbPrioTask['priority_task'])]);
            $msg='updatePriority';
        }

        else {
            $msg = 'nothing';
        }
}

    // FOR CHANGE ORDER GENERAL IN LIST 
    else {
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
        $msg = 'nothing';
    }

}}


// FOR DELETE TASK
else if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])){
    // FOR CHANDE ORDER WHEN DELETE
    updateNbPriority($dbCo);
    updateNbPriorityCategorie($dbCo);
    // FOR DELETE
    $query = $dbCo->prepare(" DELETE FROM task WHERE id_task = :id_task;
                              DELETE FROM association WHERE id_task = :id_task");
    $isQueryOk = $query->execute([
        'id_task' => intval(strip_tags($_GET['id']))
        ]);
        if($isQueryOk && $query->rowCount()=== 1) {
            $msg='deleteTask';
    }
};
$_SESSION['notif'] = $msg;
header('Location: '. $_SESSION['url']);
?>
